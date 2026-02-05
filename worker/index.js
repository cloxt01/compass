import 'dotenv/config';
import logger from './utils/logger.js';
import worker from './worker.js';
import connection from './redis.js';
import checkStatus from './helper/checkStatusJob.js';

// Event Redis
connection.on('connect', () => logger.info('Connection successful to Redis'));
connection.on('error', err => logger.error('Redis error', err));

// Event Worker
worker.on('waiting', job => {
  logger.info(`Job menunggu: ${job.id}`);
});

worker.on('completed', async job => {
  const { id } = JSON.parse(job.id);
  const status = await checkStatus(id);
  logger.info(`[COMPLETED] [${id}] => [STATUS] ${status}`);

});

worker.on('failed', (job, err) => {
  logger.error(`Job gagal: ${JSON.parse(job?.id).id}`, err?.message || err);
});

// Shutdown aman
process.on('SIGINT', async () => {
  logger.info('Shutting down worker...');
  await worker.close();
  connection.quit();
  process.exit(0);
});
