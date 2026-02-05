
const config =  {
    puppeteer: {
        jobstreet: {
            url: 'https://id.jobstreet.com/id/oauth/login?returnUrl=%2F%3Ficmpid%3Djs_global_landing_page',
            selector: {
                emailInput: '#emailAddress',
                otpInput: 'input[aria-label="verification input"]',
                loginBtn: 'button[data-cy="login"]',
                submitOtp: '#submit-OTP'
            },
            timeout: {
                emailInput: 120000,
                otpInput: 60000,
                otpSubmit: 60000,
                emailTyping: 50,
                otpTyping: 50
            }
        }
    }
}

export default config;