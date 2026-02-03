import puppeteer from 'puppeteer-extra'
import StealthPlugin from 'puppeteer-extra-plugin-stealth'
import { setupRequestInterceptor } from './interceptor.js'
import { safeLog } from '../utils/log.js'
import { delay } from '../utils/delay.js'
import config from '../config.js'

puppeteer.use(StealthPlugin())

// Deteksi dan handle Cloudflare challenge
async function handleCloudflareChallenge(page, timeout = 30000) {
    safeLog('  🛡️ Checking for Cloudflare challenge...');
    
    try {
        // Tunggu hingga Cloudflare challenge selesai atau timeout
        await Promise.race([
            page.waitForFunction(
                () => {
                    const cfChallenge = document.querySelector('form#challenge-form');
                    const cfSuccess = !document.body.innerHTML.includes('challenge');
                    return cfSuccess || !cfChallenge;
                },
                { timeout }
            ).then(() => {
                safeLog('  ✓ Cloudflare challenge passed/not present');
            }),
            new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Cloudflare check timeout')), timeout)
            )
        ]);
        
        return true;
    } catch (err) {
        safeLog(`  ⚠️ ${err.message}`);
        safeLog('  ℹ️ Continuing anyway (user may need to complete challenge manually)');
        return false;
    }
}

export async function launchBrowser(onTokenCaptured) {
    safeLog('\n[STEP 1] Launching browser...');
    
    const browser = await puppeteer.launch({
        headless: true,  // ✅ Browser VISIBLE - user dapat lihat & interact
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--disable-web-resources',  // Reduce fingerprinting
            '--disable-extensions',
            '--disable-plugins',
            '--disable-sync'
        ]
    });

    const page = await browser.newPage();
    await page.setViewport(config.puppeteer.viewport || { width: 1366, height: 768 });
    
    // Set user agent yang lebih realistic
    await page.setUserAgent(config.user_agent);
    
    // Improve stealth dengan extra headers (avoid CORS issues with restrictive headers)
    await page.setExtraHTTPHeaders({
        'Accept-Language': 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none'
    });
    
    // ===== OPTIMIZATIONS =====
    
    // Setup token interceptor AND resource blocking (combined in one handler)
    safeLog('[STEP 2] Setting up request interceptor with resource blocking...');
    setupRequestInterceptor(page, onTokenCaptured);
    
    // Store Cloudflare handler di page object
    page._handleCloudflareChallenge = () => handleCloudflareChallenge(page, 30000);

    return { browser, page };
}
