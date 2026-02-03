// config.js

const config = {
    environment: process.env.NODE_ENV || 'development',
    user_agent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',

    service: {
        baseUrl: process.env.BASE_URL || 'https://compass-mu-ten.vercel.app/api-v1/internal',
    },

    endpoints: {
        token: '/add-token',
        updateStatus: '/login/update-status',
        updateOTPConsumed: '/login/update-otp-consumed',
        checkOTP: '/login/check-otp'
    },

    puppeteer: {
        loginUrl: process.env.LOGIN_URL || 'https://id.jobstreet.com/id/oauth/login?returnUrl=%2F%3Ficmpid%3Djs_global_landing_page',
        timeoutMs: Number(process.env.TIMEOUT_MS || 600000),
        viewport: { width: 1366, height: 768 },

        selectors: {
            email: [
                '#emailAddress',
                'input[name="emailAddress_seekanz"]',
                'input[type="email"]',
                'input[name="email"]',
                'input[id="email"]',
                'input[placeholder*="email"]',
                'input[placeholder*="Email"]'
            ],
            emailSubmit: [
                'button[data-cy="login"]',
                'button[type="submit"]',
                'button[class*="submit"]'
            ],
            otp: [
                'input[aria-label="verification input"]',
                'input[spellcheck="false"]',
                'input[type="text"][placeholder*="OTP"]',
                'input[type="text"][placeholder*="otp"]',
                'input[name="otp"]',
                'input[id*="otp"]',
                'input[placeholder*="code"]',
                'input[placeholder*="Code"]',
                'input[type="text"]'
            ],
            otpSubmit: [
                '#submit-OTP',
                'button[data-cy="verification"]',
                'button[type="submit"]',
                'button:contains("Masuk")'
            ]
        },

        delays: {
            pageLoad: 1500,
            preField: 300,
            preSubmit: 800,
            formSubmit: 2000,
            typing: 30,
            otpTyping: 50,
            polling: 1000
        }
    },

    flags: {
        debug: process.env.DEBUG === '1'
    }
}

export default config
