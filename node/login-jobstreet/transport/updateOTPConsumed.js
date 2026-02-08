import { safeLog } from '../utils/log.js';
import config from '../config.js';

export async function updateOTPConsumed(userId, uuid) {
  const url = `${config.service.baseUrl}${config.endpoints.updateOTPConsumed}`;
  console.log(`URL: ${url}`);

  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ user_id: userId, uuid: uuid })
  });

  if (!res.ok) {
    throw new Error(`HTTP ${res.status}`);
  }

  const data = await res.json();

  return data.success ? { success: true } : { success: false };
}
