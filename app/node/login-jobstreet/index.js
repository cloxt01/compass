import { launchBrowser } from './puppeteer/browser.js';
import { navigateToLogin, fillEmailField, submitEmailForm, fillOtpField, submitOtpForm } from './puppeteer/action.js';
import { sendTokenToServer } from './transport/sendToken.js';
import { waitForOTP } from './otp/waitForOTP.js';
import { updateOTPConsumed } from './transport/updateOTPConsumed.js';
import { updateStatus } from './transport/updateStatus.js';
import { delay } from './utils/delay.js';
import { safeLog } from './utils/log.js';
import { db } from './db/db.js';
import config from './config.js';

// Mencegah crash jika pipa terminal ditutup oleh PHP (EPIPE)
process.stdout.on('error', (err) => {
    if (err.code === 'EPIPE') process.exit(0);
});

export async function performLogin(user_id, email, uuid) {
    safeLog('\n' + '='.repeat(70));
    safeLog('  SEEK OAuth2 - Automated Login Flow');
    safeLog('  Email: ' + email);
    safeLog('='.repeat(70));
    
    let browser = null;
    let tokenCaptured = false;
    let tokenData = null;

    try {

        // 1. Update status awal

        // 2. Launch browser
        const { browser: b, page } = await launchBrowser((data) => {
            tokenData = data;
            tokenCaptured = true;
        });
        browser = b;

        await navigateToLogin(page);

        // 3. Email Flow
        const emailFilled = await fillEmailField(page, email);
        if (emailFilled) {
            await submitEmailForm(page);
        }

        // 4. Minta OTP ke User di Browser
        const otp = await waitForOTP(user_id, uuid); // Node.js akan "diam" di sini sampai file OTP ada
        const updateOTPConsumedResult = await updateOTPConsumed(user_id, uuid);
        safeLog(JSON.stringify(updateOTPConsumedResult, null, 2));


        // 5. Submit OTP
        const otpFilled = await fillOtpField(page, otp);
        if (otpFilled) {
            await submitOtpForm(page);
        }

        // 6. Tunggu Token Response
        const startTime = Date.now();
        while (!tokenCaptured && (Date.now() - startTime) < config.puppeteer.timeoutMs) {
            await delay(config.puppeteer.delays.polling || 1000);
        }

        if (!tokenCaptured) throw new Error('Token response not captured within timeout');

        // 7. Send response ke server
        if (tokenData) {
            safeLog('\nSending OAuth token response to server...');
            const result = await sendTokenToServer(user_id, tokenData);
            safeLog(JSON.stringify(result, null, 2));
            
            const statusResult = await updateStatus(user_id, uuid, 'done', null);
            
            safeLog(JSON.stringify(statusResult, null, 2));

        }

        safeLog('\n✅ Login flow completed successfully!');
        return true;

    } catch (error) {
        try {
            const statusRes = await updateStatus(user_id, uuid, 'failed', error.message);
            safeLog(JSON.stringify(statusRes, null, 2));
        } catch (e) {
            safeLog('Failed to update status: ' + (e && e.message ? e.message : JSON.stringify(e)));
        }
        safeLog(`\nx ERROR: ${error.message}`);
        return false;
    } finally {
        if (browser) await browser.close();
    }
}

// CLI Entry Point
const user_id = process.argv[2];
const email = process.argv[3];
const uuid = process.argv[4];


if (!email || !uuid) {
    safeLog('x Usage: node index.js <user_id> <email> <uuid>');
    process.exit(1);
}

performLogin(user_id , email, uuid)
    .then(success => process.exit(success ? 0 : 1))
    .catch(err => {
        safeLog(`\nx Fatal error: ${err.message}`);
        process.exit(1);
    });

    