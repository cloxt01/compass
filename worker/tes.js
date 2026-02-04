import 'dotenv/config';
import checkStatus from "./helper/checkStatusJob.js";
const status = await checkStatus("f0efd007-77c2-4f8e-a504-3cf2d6a94e13");

while (true) {
console.log("Job status: " + status);
await new Promise(resolve => setTimeout(resolve, 3000));
}