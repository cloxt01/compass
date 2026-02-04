import puppeteer from 'puppeteer';
import isJson from '../helper/isJson.js';

async function handler(data) {
  const { email, timeout = 30000 } = data;
  let browser;

  try {
    browser = await puppeteer.launch({
      headless: false,
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage'
      ]
    });

    const page = await browser.newPage();
    page.setDefaultTimeout(timeout);

    // Block resource yang nggak perlu
    await page.setRequestInterception(true);
    page.on('request', req => {
      const type = req.resourceType();
      if (['stylesheet', 'image', 'font'].includes(type)) {
        req.abort();
      } else {
        req.continue();
      }
    });

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

    await page.click('button[data-cy="login"]');

    // Tunggu request selesai sebelum return
    const req = await requestPromise;

    const cookies = await page.cookies();

    return {
      status: 'OTP_SENT',
      data: {
        payload: isJson(req.postData()) ? JSON.parse(req.postData()) : req.postData(),
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
