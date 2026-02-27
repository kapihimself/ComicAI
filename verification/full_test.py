from playwright.sync_api import sync_playwright
import time

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    page = browser.new_page()

    # Capture console messages
    page.on("console", lambda msg: print(f"BROWSER: {msg.text}"))

    # 1. Landing Page
    print("Navigating to home page...")
    page.goto("http://localhost:3000")

    # 2. Enter Software Engineer Track
    print("Entering SE Track...")
    page.click("text=Jalur Software Engineer", force=True)
    page.wait_for_selector("text=Halo Dunia Web")

    # 3. Fail Case: Run without changes
    print("Running initial code (should fail)...")
    page.click("text=Jalankan")

    # Wait and check
    try:
        page.wait_for_selector("text=Kamu belum membuat elemen <h1>.", timeout=5000)
        print("Confirmed: Validation caught the missing h1.")
    except Exception as e:
        print(f"Validation fail check error: {e}")
    finally:
        page.screenshot(path="verification/3_validation_fail_debug.png")

    # 4. Success Case: Type correct code
    print("Typing correct code...")
    # Focus the editor area (CodeMirror uses contenteditable)
    # We click the first line number as a proxy for the editor area
    try:
      page.click(".cm-content", timeout=2000)
    except:
      print("Could not find .cm-content, trying .sp-code-editor")
      page.click(".sp-code-editor")

    # Clear existing content (ctrl+a, backspace)
    page.keyboard.press("Control+A")
    page.keyboard.press("Backspace")
    # Type the answer
    page.keyboard.type("<h1>Halo Indonesia</h1>")

    # Run again
    print("Running correct code...")
    page.click("text=Jalankan")

    # Wait for success dialog
    try:
        page.wait_for_selector("text=Luar Biasa!", timeout=5000)
        print("Confirmed: Success modal appeared.")
    except Exception as e:
        print(f"Success modal check error: {e}")
    finally:
        page.screenshot(path="verification/4_success_debug.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
