import { safeLog } from '../utils/log.js';
import config from '../config.js';

export async function sendTokenToServer(userId,tokenData) {
    try {
        
        let tokenObj = tokenData;
        if (typeof tokenData === 'string') {
            try {
                tokenObj = JSON.parse(tokenData);
            } catch (e) {
                tokenObj = { raw: tokenData };
            }
        }
        
        const url = `${config.service.baseUrl}${config.endpoints.token}`;
        console.log(`URL: ${url}`);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                user_id: userId,
                token: tokenObj
            })
        });
        
        const data = await response.json();
        
        const result = response.ok 
            ? { success: true, message: 'Token sent successfully', data: data }
            : { success: false, message: 'Server error', data: data ?? null };
        
        return result;
    } catch (error) {
        safeLog(`  x Error sending to server: ${error.message}`);
        return { success: false, message: error.message };
    }
}