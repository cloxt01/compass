import { safeLog } from '../utils/log.js';
import config from '../config.js';

export async function updateStatus(user_id, uuid, status, exception) {
    try {
        
        
        const url = `${config.service.baseUrl}${config.endpoints.updateStatus}`;
        console.log(`URL: ${url}`);
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                user_id: user_id,
                uuid: uuid,
                status: status,
                data: {
                    error: exception
                }
            })
        });
        
        const data = await response.json();
        
        const result = response.ok 
            ? { success: true, data: data }
            : { success: false, data: data ?? null };
        
        return result;
    } catch (error) {
        safeLog(`  x Error updating status: ${error.message}`);
        return { success: false, message: error.message };
    }
}
