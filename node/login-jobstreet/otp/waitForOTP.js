import { checkOTP } from '../transport/checkOTP.js';
import { safeLog } from '../utils/log.js';
import { delay } from '../utils/delay.js';

export async function waitForOTP(userId, uuid, { interval = 3000, timeout = 60000 } = {}) {
  const startTime = Date.now();

  while (true) {
    try {
      const data = await checkOTP(userId, uuid); // { success: true/false, otp: '123456' }

      if (data.success && data.otp) {
        // Return langsung string OTP, biar bisa dipakai type()
        return data.otp;
      }

      safeLog('  - OTP belum ready, polling...');

    } catch (err) {
      safeLog(`  x Error polling OTP: ${err.message}`);
    }

    if (Date.now() - startTime > timeout) {
      return null; // timeout berarti gak ada OTP
    }

    await delay(interval);
  }
}
