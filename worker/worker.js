import { Worker } from 'bullmq';
import updateStatus from './helper/updateStatusJob.js';
import checkStatus from './helper/checkStatusJob.js';
import getJob from './helper/getJob.js';
import handler from './jobs/handler.js';
import logger from './utils/logger.js';
import connection from './redis.js';


const worker = new Worker(
  process.env.REDIS_QUEUE,
  async job => {
    const { id, operation, data } = JSON.parse(job.id);
    logger.info(`[GOT] [ID: ${id}] <-> [NAME: ${operation}]`);

    try {
      if (operation === 'passwordless-login') {
        await updateStatus(id, 'LOGIN_STARTED' ,{
          id: id,
          provider: data.provider
        });
        const result = await handler(id, operation, data);
        if (result) {
          const token = await getJob(id).token;
          logger.info(token);
        }
      }

      return { processing: true };
    } catch (err) {
      
      throw err;
    }
  },
  {
    connection,
    prefix: `${process.env.REDIS_PREFIX}bull`,
    concurrency: Number(process.env.WORKER_CONCURRENCY)
  }
);

export default worker;
