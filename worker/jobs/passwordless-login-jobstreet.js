import puppeteer, { TimeoutError } from 'puppeteer';
import isJson from '../helper/isJson.js';
import logger from '../utils/logger.js';
import updateStatus from '../helper/updateStatusJob.js';
import getOtp from '../helper/getOtp.js';
import setupPage from '../helper/puppeter/setupPage.js';
import config from '../config.js'


async function handler(id, data) {
  const { email } = data;
  let browser

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
    await setupPage(page);
    await page.goto(config.puppeteer.jobstreet.url, { waitUntil: 'domcontentloaded'});
    await page.waitForSelector(config.puppeteer.jobstreet.selector.emailInput, { timeout: config.puppeteer.jobstreet.timeout.emailInput});
    await page.type(config.puppeteer.jobstreet.selector.emailInput, email, { delay: config.puppeteer.jobstreet.timeout.emailTyping });
    await page.click(config.puppeteer.jobstreet.selector.loginBtn);

    await updateStatus(id, 'WAITING_OTP');

    let otp;
      const timeout = Date.now() + 120000; // 2 menit

    while (!otp && Date.now() < timeout) {
        otp = await getOtp(id);
        if (!otp) {
          await new Promise(r => setTimeout(r, 2000));
        }
    }
    if (!otp) {
        throw new Error('OTP timeout');
    }

    await updateStatus(id, 'VERIFYING_OTP');
    await page.waitForSelector(config.puppeteer.jobstreet.selector.otpInput, { timeout: config.puppeteer.jobstreet.timeout.otpInput });
    await page.type(config.puppeteer.jobstreet.selector.otpInput, otp, { delay: config.puppeteer.jobstreet.timeout.otpTyping });
    await page.click(config.puppeteer.jobstreet.selector.submitOtp);

    
    const res = await page.waitForResponse(
      res =>
        res.url().includes('/oauth/token') &&
        res.status() === 200 &&
        res.request().method() === 'POST',
      { timeout: 15000 }
    );
    const token = await res.json();
    await updateStatus(id, 'LOGIN_SUCCESS', {
      token: JSON.stringify(token)
    });
    return true;

   } catch (err) {
    throw err;
  } finally {
    if (browser) {
    try {
      await browser.close();
    } catch (_) {}
  }
  }
}

export default handler;
