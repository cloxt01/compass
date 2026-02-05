function otpKey(jobId) {
    return `${process.env.REDIS_PREFIX}otp:${jobId}`;
}

export default otpKey;