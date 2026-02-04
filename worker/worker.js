import { Worker } from 'bullmq';
import updateStatus from './helper/updateStatusJob.js';
import checkStatus from './helper/checkStatusJob.js';
import logger from './utils/logger.js';
import connection from './redis.js';


const worker = new Worker(
  process.env.REDIS_QUEUE,
  async job => {
    const payload = JSON.parse(job.id);
    const { id, name, data } = payload;

    logger.info(`[GOT] [ID: ${id}] <-> [NAME: ${name}]`);
    // console.log(`[STATUS] ${process.env.APP_URL}/api/job-status/${id}`);


    try {
      if (name === 'send-otp') {
        await updateStatus(id, 'SENDING_OTP', {
          provider: data.provider
        });

        const { default: handler } = await import(`./jobs/send-otp-${data.provider}.js`);

        const result = await handler(data);
        logger.info(result.status);
        if(result.status === 'OTP_SENT'){
          await updateStatus(
            id, result.status, {...result.data });
        } else {
          await updateStatus(
            id, result.status, { reason: result.reason });
        }
        
      } else if (name === 'verify-otp') {
        let status = await checkStatus(id);
        if ( status != 'OTP_SENT') {
          logger.info(`[ID] ${id} => [STATUS] ${status}`);
          throw new Error(`Job ${id} tidak dalam status OTP_SENT`);
        }
          await import(`./jobs/verify-otp-${data.provider}.js`);
        const { default: handler } = await import(`./jobs/verify-otp-${data.provider}.js`);
        logger.info("Processing verify-otp job for provider: " + data.provider);
      }

    } catch (err) {
      // await updateStatus(id, 'FAILED', {
      //   error: err.message
      // });

      throw err;
    }
  },
  {
    connection,
    prefix: `${process.env.REDIS_PREFIX}bull`
  }
);

export default worker;
