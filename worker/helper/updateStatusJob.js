import connection from '../redis.js';


function jobKey(jobId) {
  return `${process.env.REDIS_PREFIX}job:${jobId}`;
}

async function updateStatus(jobId, status, extra = {}) {
  const key = jobKey(jobId);

  await connection.hset(key, {
    status,
    ...extra,
    updated_at: Date.now()
  });

  await connection.expire(key, 600); 
}

export default updateStatus;
