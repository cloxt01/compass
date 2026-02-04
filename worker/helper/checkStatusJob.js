import connection from '../redis.js';
import jobKey from './jobKey.js';

async function checkStatus(jobId) {
  const key = jobKey(jobId);
  const status = await connection.hget(key, 'status');
  return status || null;
}

export default checkStatus;