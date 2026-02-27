from playwright.sync_api import sync_playwright
import time

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    page = browser.new_page()

    # Go to landing page
    print("Navigating to home page...")
    page.goto("http://localhost:3000")

    # Click on Software Engineer track using force
    print("Clicking 'Jalur Software Engineer'...")
    page.click("text=Jalur Software Engineer", force=True)

    # Wait for navigation to lesson page
    page.wait_for_url("**/learn/software_engineer/se-1")
    print("Navigated to lesson page.")

    # Wait a bit for JS hydration
    time.sleep(5)

    # Take screenshot of whatever is there
    print("Taking debug screenshot...")
    page.screenshot(path="verification/lesson_page_debug.png")

    # Dump HTML content for debugging
    with open("verification/lesson_page_debug.html", "w") as f:
        f.write(page.content())

    # Check for title
    try:
        page.wait_for_selector("text=Halo Dunia Web", timeout=5000)
        print("Success: Found lesson title!")
    except Exception as e:
        print(f"Failed to find title: {e}")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
