# Signature Design System: "Industrial Pop" (Refined Neo-Brutalism)

**CRITICAL DIRECTIVE:** This project uses a highly specific, premium design language. It marries the structural minimalism and whitespace of Jony Ive (Apple) with the vibrant, tactile nature of Colorful Neo-Brutalism. Every component must feel physical, deliberate, and high-end.

## 1. The Core Aesthetic (The "Soul")
* **Tactile Brutalism:** Elements must feel like physical objects. We achieve this using hard, solid-color drop shadows instead of soft, blurry drop shadows.
  * *Tailwind Example:* `border-2 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)]`
* **Jony Ive Minimalism:** Despite the bold colors and borders, the layout must breathe. Use massive padding, perfect vertical rhythm, and center-aligned focus. No clutter.
* **Component Shapes:** Buttons and input fields should utilize dramatic rounding (pill shapes, `rounded-full` or `rounded-2xl`) to contrast with the harsh brutalist shadows.

## 2. Pantone Color Theory & Philosophy
We do not use random hex codes. Every micro-app uses a strict, 3-color philosophical palette based on Pantone references:
* **The Canvas (Background):** Never pure white. Use a warm, premium off-white to reduce eye strain.
  * *Reference:* Pantone 11-0106 (Sweet Corn) -> `#F0EFEA`
* **The Ink (Text & Borders):** Never pure black. Use a deep, rich off-black for maximum crispness.
  * *Reference:* Pantone 419 C -> `#1A1A1A`
* **The Soul (The Primary Accent):** Each micro-app gets ONE dominant Pantone color based on its psychology. This color is used for the primary button, the hard shadows, and key accents.
  * *Example (Action/Energy):* Pantone 021 C (Bright Orange) -> `#FE5000`
  * *Example (Trust/Tech):* Pantone Reflex Blue C -> `#001489`
  * *Example (Growth/Money):* Pantone 354 C (Vibrant Green) -> `#00B140`

*Jules Instruction:* The `app-config.json` will pass a "primary color hex". You must inject this hex dynamically into the Tailwind configuration for the app's accents and brutalist shadows.

## 3. Typography
* **Primary Font:** Space Grotesk or Clash Display (for a geometric, brutalist edge).
* **Body Font:** Inter (for ultimate readability).
* **Execution:** Headings must be tight, bold, and track slightly negative (letter-spacing). Body text must be generous in line-height.

## 4. UI Component Rules
* **Buttons:** Must have a 2px solid dark border, a bright Pantone background, and a hard 4px solid shadow. On `:active` (click), the button must translate down 4px and the shadow must reduce to 0px, mimicking a physical mechanical keyboard switch being pressed.
* **Input Fields:** Thick 2px borders. When `:focus` is triggered, the border color changes to the App's Primary Pantone color, and a hard shadow appears.
* **Cards/Containers:** White backgrounds, 2px dark borders, subtle geometric patterns or noise textures in the background to give it a physical paper feel.