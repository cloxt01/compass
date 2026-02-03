import puppeteer from 'puppeteer';

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

    await page.type('#emailAddress', email, { delay: 50 });

    // 🔑 tunggu request sebelum klik
    const requestPromise = page.waitForRequest(req =>
      req.url().includes('/passwordless/start') &&
      req.method() === 'POST'
    );

    await page.click('button[data-cy="login"]');

    const req = await requestPromise;

    return {
      status: 'OTP_SENT',
      data: {
        cookies: await page.cookies(),
        payload: req.postData()
      }
    };

  } catch (err) {
    return { status: 'ERROR_OTP_SENT', reason: err.message };
  } finally {
    if (browser) await browser.close();
  }
}

export default handler;
