import connection  from "../redis.js";
import jobKey from "./jobKey.js";

function getJob(jobId) {
  const key = jobKey(jobId);
  return connection.hgetall(key) || null;
}
export default getJob;