async function setupPage(page) {
  await page.setViewport({ width: 1200, height: 800 });
  
  // Set user agent if available, otherwise use realistic one
  const userAgent = process.env.USER_AGENT || 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
  await page.setUserAgent(userAgent);
  
  // Set headers to look more like real browser
  await page.setExtraHTTPHeaders({
    'Accept-Language': 'en-US,en;q=0.9',
    'Accept': 'text/html,application/xhtml+xml',
  });
  
  await page.setJavaScriptEnabled(true);
  page.setDefaultTimeout(60000);
}

export default setupPage;