# AI Developer Coding Standards & Guardrails

**CRITICAL DIRECTIVE:** You are acting as a Senior Principal Software Engineer. Your code must be production-ready, highly optimized, and strictly typed. You are building a premium, white-label SaaS boilerplate that will be sold commercially. "Slop" code, shortcuts, and lazy implementations are strictly forbidden.

## 1. Core Stack & Architecture
* **Framework:** Next.js 15 (App Router strictly enforced).
* **Language:** TypeScript. You must use strict typing. The use of `any` is forbidden. Define explicit interfaces for all props, API responses, and configurations.
* **Styling:** Tailwind CSS combined with Shadcn UI and Lucide React icons.
* **State Management:** React Hooks (`useState`, `useEffect`, `useRef`). Use server components where possible to minimize client-side JavaScript.

## 2. Code Quality & Formatting
* **Error Handling:** All API routes must have `try/catch` blocks. All frontend API calls must have loading states, error states, and utilize toast notifications for user feedback.
* **Comments:** Leave professional, concise inline comments explaining the *why* behind complex logic, not the *what*.
* **Modularity:** Keep components small and focused. If a component exceeds 150 lines, refactor it into smaller sub-components.

## 3. The "Dynamic Config" Rule
* This application is a dynamic template. **Do not hardcode text, labels, or prompts into the UI components.** * All UI text, input configurations, and AI system prompts must be dynamically read from a central `app-config.json` file.

## 4. API & Integration Standards
* **AI Integration:** Use the official Google Gen AI SDK (`@google/genai`). Implement streaming responses for a premium user experience.
* **Security:** Never expose API keys on the client side. All external API calls (Gemini, Gumroad) must happen securely in Next.js Server Actions or Route Handlers.