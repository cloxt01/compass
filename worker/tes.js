import 'dotenv/config';
import checkStatus from "./helper/checkStatusJob.js";
import getJob from './helper/getJob.js';
import toQueryParams from './helper/toQueryParams.js';
import toFlatParams from './helper/toFlatParams.js';

// const id = "accfc6e2-c976-479c-b6ef-11f045dfccf8";
// const job = await getJob(id);
// const status = await checkStatus(id);

const otp = "542687";
const payload = {"client_id":"8OVhpvtaI9n5QVEQK3X5yfsmCbrrLXfE","connection":"email","send":"link","email":"ferdi.cloxt00@gmail.com","authParams":{"response_type":"code","redirect_uri":"https://id.jobstreet.com/id/oauth/callback/","scope":"openid profile email offline_access","audience":"https://seek/api/candidate","_csrf":"ZfiOOnH6-Cx33Lx6AwKcmtsM3In5GA6RQD6E","state":"hKFo2SBMS3dsS3ZBYTZ4amVOZ3NIZlBHY1Nxb1F1YmFDbWlNZ6FupWxvZ2luo3RpZNkga0VRaXUyVlFWRDA5VUlLejZHYjc3eWhuYWk0SENSZzOjY2lk2SA4T1ZocHZ0YUk5bjVRVkVRSzNYNXlmc21DYnJyTFhmRQ","_intstate":"deprecated","nonce":"cjViT3lNMmxZSGJSdk42RzRmLkFiQmdBcWJFNkFyeVhaZFcwY0dlZUFPZw=="}};
const params = toQueryParams(toFlatParams(JSON.stringify(payload) , otp));
console.log(`https://login.seek.com/passwordless/verify_redirect?${params}`);
// while (true) {
// console.log(job);
// console.log("Job status: " + status);

// await new Promise(resolve => setTimeout(resolve, 3000));
// }