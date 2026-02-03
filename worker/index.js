import 'dotenv/config';
import logger from './utils/logger.js';
import worker from './worker.js';
import connection from './redis.js';

// Event Redis
connection.on('connect', () => logger.info('Connection successful to Redis'));
connection.on('error', err => logger.error('Redis error', err));

// Event Worker
worker.on('waiting', job => {
  logger.info(`Job menunggu: ${job.id}`);
});

worker.on('completed', job => {
  logger.info(`Job selesai: ${job.id.id}`);
});

worker.on('failed', (job, err) => {
  logger.error(`Job gagal: ${job?.id}`, err?.message || err);
});

// Shutdown aman
process.on('SIGINT', async () => {
  logger.info('Shutting down worker...');
  await worker.close();
  connection.quit();
  process.exit(0);
});

export default worker;