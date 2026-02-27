from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    page = browser.new_page()

    # Go to landing page
    print("Navigating to home page...")
    page.goto("http://localhost:3000")

    # Wait for title
    page.wait_for_selector("text=KodeLokal")
    print("Landing page loaded.")
    page.screenshot(path="verification/landing_page.png")

    # Click on Software Engineer track using force
    print("Clicking 'Mulai Coding'...")
    # The button is covered by a hover effect div, so we force click or click the card itself
    page.click("text=Jalur Software Engineer", force=True)

    # Wait for navigation to lesson page
    page.wait_for_url("**/learn/software_engineer/se-1")
    print("Navigated to lesson page.")

    # Wait for key elements
    page.wait_for_selector("text=Halo Dunia Web") # Title in Left Panel
    page.wait_for_selector("text=Jalankan") # Action Bar

    print("Lesson page loaded.")
    page.screenshot(path="verification/lesson_page.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
