# Architecture PRD: "JSON-to-Component" AI Website Builder

## 1. Product Vision
To build a high-performance, zero-slop AI website generator targeting the Indonesian market and beyond. Unlike competitors that use expensive LLMs to write raw, buggy code, this engine uses free/low-cost LLMs (via OpenRouter/Gemini) to act purely as an "Art Director." The AI generates a strict JSON blueprint, and the Next.js engine maps that JSON to pre-coded, highly animated, bulletproof React components.

## 2. Core Tech Stack
* **Framework:** Next.js 15 (App Router)
* **Language:** Strict TypeScript (No `any` types)
* **Styling:** Tailwind CSS + "Industrial Pop" Design System (Pantone colors, hard shadows, 2px borders)
* **Animation:** GSAP (for scroll/parallax physics) and Framer Motion (for layout transitions/micro-interactions)
* **AI Integration:** OpenRouter API or Google Gen AI SDK (prompted strictly for JSON output)

## 3. The Engine Mechanics (How it Works)
1. **The Input:** User enters a prompt (e.g., "Buatkan landing page untuk cafe kopi di Medan yang energetic.")
2. **The LLM Call:** The backend sends a system prompt to the AI containing our `Component Registry` (the list of available blocks it can choose from).
3. **The JSON Blueprint:** The AI returns a strict JSON object containing the chosen theme color, typography vibe, and an array of `blocks` (e.g., `HeroParallax`, `BentoFeatures`, `TestimonialMarquee`).
4. **The Assembly (Dynamic Renderer):** The `<Canvas />` component iterates over the JSON `blocks` array and dynamically imports the corresponding React components from our local Vault, passing the AI-generated text as props.

## 4. The JSON Schema Standard
Every AI generation must perfectly match this structure to prevent crashes:
```json
{
  "siteMeta": { "title": "...", "primaryColor": "#FE5000" },
  "blocks": [
    {
      "id": "block_1",
      "componentName": "HeroParallax",
      "props": { "headline": "...", "subheadline": "...", "ctaText": "..." }
    }
  ]
}

{
  "siteMeta": {
    "title": "...",
    "designSystem": {
      "style": "neo-brutalism",
      "colorTone": "high_contrast",
      "palette": { "primary": "#FE5000", "background": "#F0EFEA", "text": "#1A1A1A" },
      "typography": "space_grotesk"
    }
  },
  "layout": [
    {
      "section": "1_Hero",
      "componentName": "HeroSplit",
      "content": { "headline": "...", "subheadline": "...", "ctaText": "..." }
    },
    {
      "section": "2_ProblemStatement",
      "componentName": "ProblemAgitator",
      "content": { "mainText": "...", "painPoints": ["Point 1", "Point 2", "Point 3"] }
    },
    {
      "section": "3_SolutionOverview",
      "componentName": "SolutionBento",
      "content": { "features": [{ "title": "...", "desc": "..." }, { "title": "...", "desc": "..." }, { "title": "...", "desc": "..." }] }
    },
    {
      "section": "4_SocialProof",
      "componentName": "TrustMarqueeAndCards",
      "content": { "partners": ["Logo1", "Logo2"], "testimonials": [{ "name": "...", "quote": "..." }, { "name": "...", "quote": "..." }] }
    },
    {
      "section": "5_FinalCTA",
      "componentName": "BottomAction",
      "content": { "persuasiveStatement": "...", "ctaText": "..." }
    }
  ]
}
