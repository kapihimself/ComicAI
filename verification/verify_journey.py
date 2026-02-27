from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    page = browser.new_page()

    # 1. Landing Page
    print("Navigating to home page...")
    page.goto("http://localhost:3000")

    # 2. Go to Map (Software Engineer)
    print("Clicking SE Track to go to Map...")
    page.click("text=Jalur Software Engineer", force=True)

    # Wait for map
    page.wait_for_url("**/learn/software_engineer")
    page.wait_for_selector("text=Peta software engineer")
    print("Map page loaded.")
    page.screenshot(path="verification/5_map_page.png")

    # 3. Check first lesson is unlocked and clickable
    print("Clicking first lesson on map...")
    # Click the "Mulai" button for the first lesson
    page.click("text=Mulai")

    # Wait for lesson page
    page.wait_for_url("**/learn/software_engineer/se-1")
    page.wait_for_selector("text=Peta Harta Karun: HTML")
    print("Lesson page loaded.")

    # 4. Check for Quiz
    page.wait_for_selector("text=Cek Pemahaman")
    print("Quiz component found.")
    page.screenshot(path="verification/6_lesson_with_quiz.png")

    # 5. Check Back Button
    print("Clicking 'Kembali ke Peta'...")
    page.click("text=Kembali ke Peta")

    # Verify back to map
    page.wait_for_url("**/learn/software_engineer")
    print("Returned to map successfully.")
    page.screenshot(path="verification/7_back_to_map.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
