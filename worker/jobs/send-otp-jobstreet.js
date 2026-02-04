import puppeteer from 'puppeteer';
import isJson from '../helper/isJson.js';

async function handler(data) {
  const { email, timeout = 30000 } = data;
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
    page.setDefaultTimeout(timeout);

    await page.goto(
      'https://id.jobstreet.com/id/oauth/login?returnUrl=%2F%3Ficmpid%3Djs_global_landing_page',
      { waitUntil: 'networkidle2' }
    );

    await page.type('#emailAddress', email, { delay: 15000 });

    // pasang listener SEBELUM klik
    const requestPromise = page.waitForRequest(
      req =>
        req.url().includes('/passwordless/start') &&
        req.method() === 'POST',
      { timeout }
    );

    const beforeCookies = await page.cookies();

    const [req] = await Promise.all([
      requestPromise,
      page.click('button[data-cy="login"]')
    ]);

    return {
      status: 'OTP_SENT',
      data: {
        payload: isJson(req.postData())
          ? JSON.parse(req.postData())
          : req.postData(),
        cookies: JSON.stringify(beforeCookies)
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
