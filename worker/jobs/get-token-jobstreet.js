import puppeteer from 'puppeteer';
import toQueryParams from '../helper/toQueryParams.js';

async function handler(cookies, payload, otp) {
  const timeout = 60000;
  const { authParams, ...rest } = JSON.parse(payload);

  const flatParams = {
  ...rest,
  ...authParams,
  protocol: 'oauth2',
  verification_code: otp,
  auth0Client: "eyJuYW1lIjoiYXV0aC0tc3BhLWpzIiwidmVyc2lvbiI6IjIuMy4wIn0="
};
  console.log(flatParams);

  const baseUrl = `https://login.seek.com/passwordless/verify_redirect?${toQueryParams(flatParams)}`;
  console.log(baseUrl);
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

    await page.setCookie(...(JSON.parse(cookies)));
    await page.setUserAgent(process.env.USER_AGENT);
    await page.setViewport({ width: 1200, height: 800 });
    await page.setDefaultTimeout(timeout);
    await page.setExtraHTTPHeaders({
      accept: 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
      'accept-encoding': 'gzip, deflate, br, zstd',
      'accept-language': 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
      dnt: '1',
      referer: 'https://login.seek.com/login', // bisa lo update sesuai state & client
      'sec-ch-ua': '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
      'sec-ch-ua-mobile': '?0',
      'sec-ch-ua-platform': '"Windows"',
      'sec-fetch-dest': 'document',
      'sec-fetch-mode': 'navigate',
      'sec-fetch-site': 'same-origin',
      'sec-fetch-user': '?1',
      'upgrade-insecure-requests': '1'
    });

    await page.goto(baseUrl, { waitUntil: 'networkidle2' });

    const responsePromise = page.waitForResponse(
      req => req.url().includes('/oauth/token') && req.method() === 'POST',
      { timeout }
    );

    const res = await responsePromise;

    return {
      status: 'SUCCESS_GET_TOKEN',
      data: await res.json()
    };

  } catch (err) {
    return {
      status: 'ERROR_GET_TOKEN',
      reason: err.message
    };
  } finally {
    if (browser) await browser.close();
  }
}

export default handler;
