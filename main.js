import puppeteer from 'puppeteer';

(async () => {
  const browser = await puppeteer.launch({ headless: false });
  const page = await browser.newPage();

  // 1. Buka login
  await page.goto('https://glints.com/id/login', { waitUntil: 'networkidle0' });
  console.log('Halaman login dimuat');

  // 2. Klik "Login dengan Email"
const loginBtn = page.locator(
  'xpath=//a[.//span[text()="Masuk dengan Email"]]'
);

  await loginBtn.wait();
  await loginBtn.click();

  // 3. Tunggu form muncul
  await page.waitForSelector('input[name="email"]');

  // 4. Isi kredensial
  await page.type('input[name="email"]', 'ferdi.cloxt00@gmail.com');
  await page.type('input[name="password"]', 'Gamerz00');
  await page.click('button[type="submit"]');

  // 5. Tunggu login selesai
  await page.waitForNavigation({ waitUntil: 'networkidle0' });


  // 6. Ambil session cookie
  const cookies = await page.cookies();

  

  // 7. Fetch GraphQL pakai session asli

  const res = await fetch("https://glints.com/api/graphql?op=jobHiringQuestion", {
    "headers": {
      "accept": "*/*",
      "accept-language": "id",
      "content-type": "application/json",
      "priority": "u=1, i",
      "sec-ch-ua": "\"Not(A:Brand\";v=\"8\", \"Chromium\";v=\"144\", \"Google Chrome\";v=\"144\"",
      "sec-ch-ua-mobile": "?0",
      "sec-ch-ua-platform": "\"Windows\"",
      "sec-fetch-dest": "empty",
      "sec-fetch-mode": "cors",
      "sec-fetch-site": "same-origin",
      "traceparent": "00-8188202eae7e79582ad2b950aad2a615-d33c95c23416736b-01",
      "cookie": cookies.map(c => `${c.name}=${c.value}`).join('; '),
      "Referer": "https://glints.com/id/opportunities/jobs/recommended"
    },
    "body": "{\"operationName\":\"jobHiringQuestion\",\"variables\":{\"jobId\":\"76e0a117-5449-4df6-b904-a50d6bf6cd9e\"},\"query\":\"query jobHiringQuestion($jobId: UUID!) {\\n  getJobHiringQuestions(jobId: $jobId) {\\n    predefinedQuestions {\\n      name\\n      type\\n      required\\n      __typename\\n    }\\n    employerScreeningQuestions {\\n      label\\n      labelLokaliseVariables {\\n        key\\n        value\\n        __typename\\n      }\\n      labelLokaliseKey\\n      questionType\\n      name\\n      questions {\\n        id\\n        subLabel\\n        subLabelLokaliseKey\\n        responseOptions {\\n          value\\n          lokaliseKey\\n          mappedValue\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n  }\\n}\"}",
    "method": "POST"
  });
  const ct = res.headers.get('content-type') || '';
  
  const body = ct.includes('application/json')
  ? await res.json()
  : await res.text();

  console.log(body);

  await browser.close();
})();
