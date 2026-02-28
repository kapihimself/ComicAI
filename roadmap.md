# Master Development Roadmap: AI Website Builder (Vite + Supabase)

**CRITICAL DIRECTIVE:** This engine generates polymorphic, SEO-optimized landing pages. [cite_start]All styles MUST be defined in the design system using HSL semantic tokens. [cite_start]Never use explicit utility colors like `text-white` in components[cite: 111]. [cite_start]The stack is React, Vite, Tailwind CSS, and TypeScript.

## Phase 1: Core Infrastructure & The Strict Design System
*Setting up the environment and the absolute laws of the UI.*

- [ ] **Task 1: Scaffold React + Vite**
  - Initialize Vite with React and TypeScript.
  - Install Tailwind CSS and Shadcn UI dependencies.
- [ ] **Task 2: The HSL Design Token System**
  - [cite_start]Edit `index.css` to define the baseline semantic tokens using ONLY HSL values.
  - Configure `tailwind.config.ts` to map to these CSS variables (e.g., `primary: "hsl(var(--primary))"`).
- [ ] **Task 3: Supabase Integration**
  - [cite_start]Set up the Supabase client for backend functionality, authentication, and database management.

## Phase 2: The 5-Step Wireframe Vault
[cite_start]*Jules must build these 5 components using `cva` for styling[cite: 89]. Every component must be fully responsive and semantic.*

- [ ] **Task 4: `HeroSection.tsx`**
  - Layout: Large Headline, Subheadline, CTA button, and a dedicated image area.
  - [cite_start]SEO: Headline must use an `<h1>` tag[cite: 28].
- [ ] **Task 5: `ProblemStatement.tsx`**
  - Layout: Main problem statement paragraph on one side, with 3 distinct icon/text blocks highlighting pain points.
- [ ] **Task 6: `SolutionOverview.tsx`**
  - Layout: A 3-column grid of feature blocks. Each block contains an image/icon, a title, and a description.
- [ ] **Task 7: `SocialProof.tsx`**
  - Layout: Top section features a horizontal row of partner company logos. Bottom section contains 2 distinct testimonial speech bubbles with user avatars.
- [ ] **Task 8: `CallToAction.tsx`**
  - Layout: Centered CTA button followed by a final persuasive statement.

## Phase 3: The AI "Art Director" & Asset Engine
*Connecting the LLM and the Image Generator.*

- [ ] **Task 9: The JSON Generator API**
  - Build the Supabase Edge Function that prompts the LLM (OpenRouter).
  - The AI must return a strict JSON blueprint filling in the text for the 5 Vault components, and selecting a design theme (e.g., Brutalism, Glassmorphism).
- [ ] **Task 10: Automated SEO & Assets**
  - [cite_start]Ensure the AI generates Title Tags and Meta Descriptions (max 160 characters)[cite: 28].
  - [cite_start]Integrate an Image Generation API (like Flux) so the layout renders with real, thematic images instead of empty placeholders.

## Phase 4: Assembly & Polish
*Where the user sees the generated site.*

- [ ] **Task 11: The Dynamic `<Canvas />`**
  - Build the React component that reads the AI's JSON and stacks the 5 Vault components sequentially.
- [ ] **Task 12: Real-time Editing**
  - Allow the user to click any text on the generated Canvas to edit it live, or swap the HSL color palette to instantly change the vibe of the entire page.
