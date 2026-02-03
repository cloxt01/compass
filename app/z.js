import puppeteer from 'puppeteer';
import fs from 'fs/promises';

const logFile = 'glints_log.json';
const logQueue = [];

// Inisialisasi file JSON async
const initLogFile = async () => {
    try {
        await fs.access(logFile);
    } catch {
        await fs.writeFile(logFile, '[]');
    }
};

// Append JSON async
const appendJson = async (file, data) => {
    let content = [];
    try {
        const text = await fs.readFile(file, 'utf-8');
        content = JSON.parse(text || '[]');
    } catch {
        content = [];
    }
    content.push(...data); // bisa banyak data
    await fs.writeFile(file, JSON.stringify(content, null, 2));
};

// Flush log queue ke file tanpa blocking
const flushLogs = async () => {
    if (logQueue.length === 0) return;
    const dataToWrite = [...logQueue];
    logQueue.length = 0;
    try {
        await appendJson(logFile, dataToWrite);
    } catch (err) {
        console.error('Gagal menyimpan log:', err);
    }
};

(async () => {
    await initLogFile();

    const browser = await puppeteer.launch({ headless: false });
    const page = await browser.newPage();

    await page.setRequestInterception(true);

    // Intercept requests
    page.on('request', request => {
        if (request.url().includes('https://glints.com/api')) {
            // Simpan sementara data request
            logQueue.push({
                url: request.url(),
                method: request.method(),
                requestData: request.postData() || null,
                type: 'REQUEST'
            });
            console.log("v2-alc REQUEST =>", request.method(), request.url());
        }
        request.continue();
    });

    

    // Flush logs tiap 1 detik
    const flushInterval = setInterval(flushLogs, 1000);

    // Tangkap Ctrl+C
    process.on('SIGINT', async () => {
        console.log('\nDetected Ctrl+C, menutup browser dan menyimpan log...');
        clearInterval(flushInterval);
        await flushLogs();
        await browser.close();
        process.exit();
    });

    await page.goto('https://glints.com/', { waitUntil: 'networkidle0', timeout: 60000 });

    console.log('Skrip berjalan. Login dan navigasi halaman jika perlu, tekan Ctrl+C untuk berhenti.');
})();
