import connection  from "../redis.js";
import jobKey from "./jobKey.js";
import logger from "../utils/logger.js";

async function getJob(jobId) {
  const key = jobKey(jobId);
  if (process.env.APP_DEBUG === 'true') {
    logger.info(`[GET-JOB_KEY] ${key}`);
  }
  return await connection.hgetall(key) || null;
}
export default getJob;