function jobKey (jobId) {
  return `${process.env.REDIS_PREFIX}job:${jobId}`;
}

export default jobKey;