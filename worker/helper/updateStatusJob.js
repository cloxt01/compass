import connection from '../redis.js';
import jobKey from './jobKey.js';
import logger from '../utils/logger.js';

async function updateStatus(jobId, status, extra = {}) {
  const key = jobKey(jobId);

  if (process.env.APP_DEBUG === 'true') {
    logger.info(`[UPDATE-KEY] ${key}`)
    logger.info(`[ID] ${jobId} => [STATUS] ${status}`);
  }
  await connection.hset(key, {
    status,
    ...extra,
    updated_at: Date.now()
  });
  await connection.expire(key, 600); 
}

export default updateStatus;
