import { safeLog } from '../utils/log.js';
import { saveToFile } from '../utils/file.js';

const BLOCKED_RESOURCES = ['image', 'stylesheet', 'font', 'media'];
const SEPARATOR = '='.repeat(70);

export function setupRequestInterceptor(page, onTokenCaptured) {
    page.setRequestInterception(true);
    
    page.on('request', async request => {
        const url = request.url();
        const resourceType = request.resourceType();
        
        // Block unnecessary resources untuk optimization
        if (BLOCKED_RESOURCES.includes(resourceType)) {
            request.abort();
            return;
        }
        
        // Continue semua requests, response akan di-handle oleh response event
        request.continue();
    });
    
    // Setup response listener untuk capture OAuth token response
    page.on('response', async response => {
        const url = response.url();
        
        if (url.includes('/oauth/token') && response.ok) {
            await captureTokenResponse(response, onTokenCaptured);
        }
    });
}

async function captureTokenResponse(response, onTokenCaptured) {
    safeLog(`\n${SEPARATOR}`);
    safeLog('  🔐 ✅ OAUTH TOKEN RESPONSE DETECTED!');
    safeLog(SEPARATOR);
    
    let responseData = null;
    
    try {
        const responseBody = await response.text();
        responseData = responseBody;
        
        safeLog(`\n  📥 RESPONSE Data:`);
        safeLog(`${responseBody}\n`);
        
        displayParsedResponse(responseBody);
    } catch (err) {
        safeLog(`  ⚠️ Could not capture response: ${err.message}`);
    }
    
    if (responseData) {
        saveAndCallback(responseData, response.url(), onTokenCaptured);
    }
}

function displayParsedResponse(responseBody) {
    try {
        const parsedResponse = JSON.parse(responseBody);
        safeLog(`  📦 Parsed Response:`);
        
        Object.entries(parsedResponse).forEach(([key, value]) => {
            const displayValue = truncateIfLong(value, 50);
            safeLog(`     ${key}: ${displayValue}`);
        });
    } catch (e) {
        safeLog(`  (Response bukan JSON)`);
    }
}

function truncateIfLong(value, maxLength) {
    if (typeof value === 'string' && value.length > maxLength) {
        return value.substring(0, maxLength) + '...';
    }
    return value;
}

function saveAndCallback(responseData, url, onTokenCaptured) {
    const tokenData = {
        timestamp: new Date().toISOString(),
        url: url,
        responseData: responseData
    };
    
    if (saveToFile('captured-token-request.json', tokenData)) {
        safeLog(`\n  ✅ Token response captured!`);
        safeLog(`${SEPARATOR}\n`);
        
        onTokenCaptured(responseData);
    }
}
