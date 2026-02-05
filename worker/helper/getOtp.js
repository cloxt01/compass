import otpKey from "./otpKey.js";
import connection from "../redis.js";

async function getOtp(jobId) {
    const key = otpKey(jobId);
    const otp = await connection.hget(key, 'otp');
    return otp || null;
}

export default getOtp;