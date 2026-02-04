import puppeteer from 'puppeteer';
import isJson from '../helper/isJson.js';

async function handler(data) {
  const { email, timeout = 60000 } = data;
  let browser;

  try {
    browser = await puppeteer.launch({
      headless: true,
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage'
      ]
    });
    

    const page = await browser.newPage();
    await page.setUserAgent(process.env.USER_AGENT);
    await page.setViewport({ width: 1200, height: 800 });
    await page.evaluateOnNewDocument(() => {
      Object.defineProperty(navigator, 'webdriver', { get: () => false });
      Object.defineProperty(navigator, 'plugins', { get: () => [1,2,3] });
      Object.defineProperty(navigator, 'languages', { get: () => ['en-US','en'] });
    });
    page.setDefaultTimeout(timeout);



    await page.goto(
      'https://id.jobstreet.com/id/oauth/login?returnUrl=%2F%3Ficmpid%3Djs_global_landing_page',
      { waitUntil: 'domcontentloaded' }
    );

    await page.waitForSelector('#emailAddress', { timeout: 60000 });
    await page.type('#emailAddress', email, { delay: 50 });

    // Pasang listener SEBELUM klik tombol login
    const requestPromise = page.waitForRequest(
      req =>
        req.url().includes('/passwordless/start') &&
        req.method() === 'POST',
      { timeout }
    );
    const responsePromise = page.waitForResponse(
      res =>
        res.url().includes('/passwordless/start') &&
        res.status() === 200,
      { timeout }
    );

    await page.click('button[data-cy="login"]');

    // Tunggu request selesai sebelum return
    const req = await requestPromise;
    const res = await responsePromise;

    const cookies = await page.cookies();

    return {
      status: 'OTP_SENT',
      data: {
        payload: req.postData(),
        cookies: JSON.stringify(cookies)
      }
    };

  } catch (err) {
    return {
      status: 'ERROR_OTP_SENT',
      reason: err.message
    };
  } finally {
    if (browser) {
      await browser.close();
    }
  }
}

export default handler;
