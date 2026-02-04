import 'dotenv/config';
import checkStatus from "./helper/checkStatusJob.js";
import getJob from './helper/getJob.js';

const id = "accfc6e2-c976-479c-b6ef-11f045dfccf8";
const job = await getJob(id);
const status = await checkStatus(id);


while (true) {
console.log(job);
console.log("Job status: " + status);

await new Promise(resolve => setTimeout(resolve, 3000));
}