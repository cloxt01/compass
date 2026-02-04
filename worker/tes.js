import 'dotenv/config';
import checkStatus from "./helper/checkStatusJob.js";
const status = await checkStatus("8153354b-599b-45c9-b584-4a8b70fd8c56");

console.log("Job status: " + status);