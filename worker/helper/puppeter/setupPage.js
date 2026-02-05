function setupPage(page) {
  return (async () => {
    await page.setViewport({ width: 1200, height: 800 });
    await page.setUserAgent(process.env.USER_AGENT);
    await page.setJavaScriptEnabled(true);
    page.setDefaultTimeout(60000);
  })};

export default setupPage;