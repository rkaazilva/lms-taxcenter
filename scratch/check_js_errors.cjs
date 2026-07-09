const puppeteer = require('puppeteer');

(async () => {
    try {
        const browser = await puppeteer.launch({ headless: "new" });
        const page = await browser.newPage();
        
        // Listen to console logs
        page.on('console', msg => {
            if (msg.type() === 'error') {
                console.log('BROWSER ERROR:', msg.text());
            }
        });

        page.on('pageerror', error => {
            console.log('PAGE ERROR:', error.message);
        });

        console.log("Navigating to login...");
        await page.goto('http://localhost:8000/login', { waitUntil: 'networkidle2' });
        
        console.log("Filling login form...");
        await page.type('input[name="email"]', 'guru@test.com');
        await page.type('input[name="password"]', '123');
        
        console.log("Submitting...");
        await Promise.all([
            page.click('button[type="submit"]'),
            page.waitForNavigation({ waitUntil: 'networkidle0' })
        ]);
        
        console.log("Current URL:", page.url());
        console.log("Waiting 3 seconds for JS execution...");
        await new Promise(r => setTimeout(r, 3000));
        
        console.log("Done checking.");
        await browser.close();
    } catch (err) {
        console.error("SCRIPT ERROR:", err);
    }
})();
