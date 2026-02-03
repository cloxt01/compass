
import IORedis from 'ioredis';

const connection = new IORedis(process.env.REDIS_URL, {
  maxRetriesPerRequest: null,
  retryStrategy: times => Math.min(times * 50, 2000), // reconnect dengan linear backoff max 2 detik
});

export default connection;