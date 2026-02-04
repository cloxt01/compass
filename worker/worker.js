import { Worker } from 'bullmq';
import updateStatus from './helper/updateStatusJob.js';
import checkStatus from './helper/checkStatusJob.js';
import getJob from './helper/getJob.js';
import logger from './utils/logger.js';
import connection from './redis.js';


const worker = new Worker(
  process.env.REDIS_QUEUE,
  async job => {
    let payload = JSON.parse(job.id);
    const { id, name, data } = payload;

    logger.info(`[GOT] [ID: ${id}] <-> [NAME: ${name}]`);
    // console.log(`[STATUS] ${process.env.APP_URL}/api/job-status/${id}`);


    try {
      if (name === 'send-otp') {
        await updateStatus(id, 'SENDING_OTP', {
          provider: data.provider
        });

        let { default: handler } = await import(`./jobs/send-otp-${data.provider}.js`);

        let result = await handler(data);
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
        if (status != 'OTP_SENT') {
            logger.info(`[ID] ${id} => [STATUS] ${status}`);
            throw new Error(`[ID] ${id} tidak dalam status OTP_SENT`);
        }

        let session = await getJob(id);
        console.log("----------[SESSION LOAD]------------");

        // Ganti nama agar tidak bentrok
        let { payload: sessionPayload, cookies: sessionCookies, provider } = session;
        const verification_code = data.code;
        logger.info(`[ID] ${id} => [PROVIDER] ${provider} => [VERIFICATION_CODE] ${verification_code}`);
        let { default: handler } = await import(`./jobs/get-token-${provider}.js`);
        let result = await handler(sessionCookies, sessionPayload, verification_code);
        logger.info(result);
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
