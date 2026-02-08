import { delay } from '../utils/delay.js';
import { safeLog } from '../utils/log.js';
import config from '../config.js';

const DELAYS = {
    pageLoad: 800, // Reduced from 1500
    preField: 100, // Reduced from 300
    preSubmit: 500, // Reduced from 800
    formSubmit: 1000, // Reduced from 2000
    typing: 20, // Reduced from 30
    otpTyping: 30, // Reduced from 50
    polling: 500 // Reduced from 1000
};

// Pastikan DOM telah fully loaded sebelum interact dengan elemen
async function waitForDOMReady(page, timeout = 15000) {
    safeLog('  ⏳ Waiting for DOM to be fully loaded...');
    try {
        await page.waitForFunction(
            () => {
                return (
                    document.readyState === 'complete' &&
                    document.body !== null &&
                    document.body.children.length > 0
                );
            },
            { timeout }
        );
        safeLog('  ✓ DOM fully loaded and ready');
        return true;
    } catch (err) {
        safeLog(`  ⚠️ DOM ready timeout (${timeout}ms): ${err.message}`);
        return false;
    }
}

export async function navigateToLogin(page) {
 safeLog('\n[STEP 3] Navigating to SEEK login...');
 try {
 await page.goto(config.puppeteer.loginUrl, {
 waitUntil: 'networkidle2',
 timeout: 30000
 });
 safeLog(`  ✓ Login page loaded`);
 
 // Handle Cloudflare challenge jika ada
 if (page._handleCloudflareChallenge) {
     await page._handleCloudflareChallenge();
 }
 
 // Pastikan DOM sepenuhnya ready sebelum melanjutkan
 await waitForDOMReady(page, 15000);
 
 } catch (err) {
 safeLog(`  ⚠️ Navigation timeout (possible slow connection)`);
 }
}

export async function fillEmailField(page, email) {
    safeLog('[STEP 4] Auto-filling email...');
    
    try {
        // Pastikan DOM fully loaded sebelum mencari elemen
        await waitForDOMReady(page, 15000);
        
        await delay(DELAYS.pageLoad);
        
        for (const selector of config.puppeteer.selectors.email) {
            try {
                safeLog(`  Trying selector: ${selector}`);
                const element = await page.$(selector);
                
                if (element) {
                    safeLog(`  ✓ Found email field: ${selector}`);
                    
                    // Scroll & focus
                    await page.evaluate((sel) => {
                        document.querySelector(sel)?.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }, selector);
                    
                    await delay(DELAYS.preField);
                    await element.focus();
                    await delay(DELAYS.preField);
                    
                    // Clear & type
                    await page.evaluate((sel) => {
                        const el = document.querySelector(sel);
                        if (el) el.value = '';
                    }, selector);
                    
                    await element.type(email, { delay: DELAYS.typing });
                    safeLog(`  ✓ Email filled: ${email}`);
                    
                    return true;
                }
            } catch (err) {
                safeLog(`  ✗ Failed: ${err.message}`);
            }
        }
        
        safeLog(`  ⚠️ Could not find email input field`);
        safeLog(`  Page title: ${await page.title()}`);
        safeLog(`  Page URL: ${page.url()}`);
        
        return false;
    } catch (err) {
        safeLog(`  ⚠️ Error: ${err.message}`);
        return false;
    }
}

export async function submitEmailForm(page) {
    safeLog('\n[STEP 5] Submitting email form...');
    
    try {
        for (const selector of config.puppeteer.selectors.emailSubmit) {
            try {
                const button = await page.$(selector);
                if (button) {
                    safeLog(`  ✓ Found submit button: ${selector}`);
                    await button.click();
                    safeLog(`  ✓ Form submitted`);
                    
                    await delay(DELAYS.formSubmit);
                    return true;
                }
            } catch (err) {
                // Continue to next selector
            }
        }
        
        // Fallback: Press Enter
        safeLog(`  ⚠️ Button not found. Using Enter key...`);
        await page.keyboard.press('Enter');
        await delay(config.puppeteer.delays.formSubmit);
        
        return true;
    } catch (err) {
        safeLog(`  ⚠️ Error: ${err.message}`);
        return false;
    }
}

export async function fillOtpField(page, otp) {
    safeLog('\n[STEP 7] Auto-filling OTP...');
    
    try {
        // Pastikan DOM fully loaded sebelum mencari elemen OTP
        await waitForDOMReady(page, 15000);
        
        await delay(DELAYS.pageLoad);
        
        for (const selector of config.puppeteer.selectors.otp) {
            try {
                safeLog(`  Trying selector: ${selector}`);
                const element = await page.$(selector);
                
                if (element) {
                    safeLog(`  ✓ Found OTP field: ${selector}`);
                    
                    // Scroll & focus
                    await page.evaluate((sel) => {
                        const el = document.querySelector(sel);
                        if (el) {
                            el.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                            el.focus();
                        }
                    }, selector);
                    
                    await delay(config.puppeteer.delays.preField);
                    await element.focus();
                    await delay(config.puppeteer.delays.preField);
                    
                    // Clear & type
                    await page.evaluate((sel) => {
                        const el = document.querySelector(sel);
                        if (el) el.value = '';
                    }, selector);
                    
                    safeLog(`  Typing OTP: ${otp}`);
                    await element.type(otp, { delay: DELAYS.otpTyping });
                    safeLog(`  ✓ OTP filled: ${otp}`);
                    
                    return true;
                }
            } catch (err) {
                safeLog(`  ✗ Failed: ${err.message}`);
            }
        }
        
        safeLog(`  ⚠️ Could not find OTP input field`);
        return false;
    } catch (err) {
        safeLog(`  ⚠️ Error: ${err.message}`);
        return false;
    }
}

export async function submitOtpForm(page) {
    safeLog('  Waiting before submit...');
    await delay(DELAYS.preSubmit);
    
    try {
        for (const selector of config.puppeteer.selectors.otpSubmit) {
            try {
                const button = await page.$(selector);
                if (button) {
                    safeLog(`  ✓ Found submit button: ${selector}`);
                    await button.click();
                    safeLog(`  ✓ OTP form submitted`);
                    return true;
                }
            } catch (err) {
                // Continue to next selector
            }
        }
        
        // Fallback: Press Enter
        safeLog('  Using Enter key...');
        await page.keyboard.press('Enter');
        safeLog(`  ✓ Submitted with Enter key`);
        
        return true;
    } catch (err) {
        safeLog(`  ⚠️ Error: ${err.message}`);
        return false;
    }
}
