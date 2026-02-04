import connection from '../redis.js';


function jobKey(jobId) {
  return `${process.env.REDIS_PREFIX}job:${jobId}`;
}

async function checkStatus(jobId) {
  const key = jobKey(jobId);
  console.log("Checking status for key: " + key);
  const status = await connection.hget(key, 'status');
  return status || null;
}

export default checkStatus;