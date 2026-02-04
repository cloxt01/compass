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

    await page.goto(
      'https://id.jobstreet.com/id/oauth/login?returnUrl=%2F%3Ficmpid%3Djs_global_landing_page',
      { waitUntil: 'networkidle2' }
    );

    // pasang listener SEBELUM klik
    const requestPromise = page.waitForRequest(
      req =>
        req.url().includes('/passwordless/verify-redirect?') &&
        req.method() === 'POST',
      { timeout }
    );


    return {
      status: 'OTP_SENT',
      data: {
        payload: isJson(payload) ? JSON.parse(payload) : payload,
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
