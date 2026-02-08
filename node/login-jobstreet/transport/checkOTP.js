import { safeLog } from '../utils/log.js';
import config from '../config.js';

export async function checkOTP(userId, uuid) {
  const url = `${config.service.baseUrl}${config.endpoints.checkOTP}`;
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

  if (data.success && data.otp) {
    return { success: true, otp: data.otp };
  }

  return { success: false, reason: 'OTP_NOT_READY', raw: data };
}
