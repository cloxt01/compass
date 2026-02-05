import connection from '../redis.js';
import jobKey from './jobKey.js';

async function checkStatus(jobId) {
  const key = jobKey(jobId);
  const status = await connection.hget(key, 'status');
  if (!status || typeof status !== 'string') {
    return null;
  }
  return status;
}

export default checkStatus;