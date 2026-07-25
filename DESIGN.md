---
version: alpha
name: "Astro"
website: "https://astro.build"
description: >-
  A web framework brand whose marketing site runs a deep near-black canvas (#0c0f19) with a purple-to-blue gradient voltage that never resolves to a single hex — the hero deploys a purple-fuchsia-blue gradient mesh as the entire below-headline atmosphere, while the primary CTA is a pill-shaped white ghost button rather than a chromatic fill. Type runs Obviously (a proprietary geometric display family) at 48px / weight 700 for the hero h1, paired with system-ui at weight 300 for body — the lightest body weight in any framework marketing site. The radius scale is pill-dominant at 9999px for CTAs and nav, with 8–16px for cards and code surfaces. An embedded terminal panel with dark editor chrome appears mid-page for the framework-comparison benchmark.

seo:
  title: "Astro Design System for React — purple gradient on near-black, Obviously, 20 components"
  metaDescription: "Astro's marketing design system: purple-blue gradient voltage on a deep near-black canvas, Obviously display at weight 700, system-ui body at weight 300, pill CTAs, 17 color tokens, 20 components. For React, Next.js, and AI tools."
  highlights:
    - "Gradient as primary voltage — Astro's brand identity is a purple-to-royal-blue gradient mesh (#b845ed through #3245ff), not a single hex; neither color stands alone as the brand accent"
    - "Weight 300 body — body text runs system-ui at weight 300 (light), the lightest body weight among major framework marketing sites; it signals confidence by restraint rather than legibility by weight"
    - "Ghost CTA on dark canvas — the primary hero button is white pill outline with white text, not a gradient fill; the gradient stays in the atmosphere behind the headline, never in the UI chrome"
    - "Obviously for display — a proprietary geometric sans runs all h1 / h2 display at weight 700, then drops to weight 300 for one section-level subheading — the weight extremes define the entire typographic scale"
    - "Aqua terminal accent — #4bf3c8 appears as the selection-text color in the embedded terminal panel, a bright teal that reads as interactive feedback, not as brand voltage"
  tags:
    - "Developer Tools & IDEs"
  lastUpdated: "2026-05-19"
  author:
    name: "Dov Azencot"
    url: "https://x.com/dovazencot"
  opening: |
    Astro's marketing page makes the framework comparison benchmark a design feature. Below the hero, a full-width bar chart shows Astro at 66% Core Web Vitals score versus WordPress at 48%, Gatsby at 47%, Next.js at 30%, and Nuxt at 28% — the chart is not tucked into a "performance" section but given its own named section called "Astro Islands," with a teal aqua bar color that matches the terminal selection-text token. The gradient behind the hero headline spans violet through fuchsia through royal blue in a radial mesh — not a single directional gradient, but an atmospheric wash that makes the canvas read as deep space. The hero h1 runs Obviously at 48px / weight 700, the body runs system-ui at weight 300 — a 400-point weight gap between the loudest and quietest typographic moments on the page.

    The DESIGN.md file packages the system as machine-readable tokens for React and AI tools. Inside: 17 color tokens anchored on the near-black canvas, a gradient vocabulary (violet, fuchsia, royal blue, aqua accent), and a structural gray tier; 13 typography tokens spanning Obviously at 36–48px display and system-ui at 14–16px body; 6 radius values dominated by the 9999px pill for CTAs and navigation chips, with 8–16px for cards; and 20 component definitions covering the photographic-gradient hero, the ghost-pill primary CTA, the embedded terminal panel, the performance chart bar, and the framework feature grid.

    Feed this file to Claude or Cursor and it reproduces Astro's specific moves: deep near-black canvas with radial gradient atmosphere, ghost-pill CTAs rather than filled-color buttons, Obviously-equivalent display at heavy weight 700, body text at unusually light weight 300, and the teal terminal accent reserved for interactive feedback states. The most distinctive move worth borrowing is the weight-300 body — it requires a high-quality neutral sans and sufficient line-height to remain legible, but it gives the page a deliberate contrast against the weight-700 headline that most framework sites never attempt.
  related:
    - href: "/design"
      title: "Browse all design systems"
      description: "The full directory of DESIGN.md files on shadcn.io, with live mockups for each."
    - href: "https://astro.build"
      title: "Astro — official site"
      description: "Astro's public marketing site — the source of truth for the live tokens captured in this file."
    - href: "https://github.com/google-labs-code/design.md"
      title: "The DESIGN.md specification"
      description: "Google Labs' open spec for machine-readable design system files — the format this page is built on."
  questions:
    - id: "primary-color"
      title: "What is Astro's primary brand color?"
      answer: "Astro's brand voltage is not a single hex — it is a gradient mesh spanning violet (#b845ed) through fuchsia (#f041ff) through royal blue (#3245ff). The page meta themeColor is a middle-purple sitting between these anchors, but it does not appear on the rendered page as a standalone fill. No single color carries the brand in isolation; the gradient is the identity. The closest single-hex representation for token purposes is violet (#b845ed), which has the highest gradient frequency (12 occurrences) and anchors the warmer end of the mesh. The aqua (#4bf3c8) is a secondary interactive accent, not a brand color."
    - id: "typography"
      title: "What typeface does Astro use, and what should I use as a substitute?"
      answer: "Astro's display headings use Obviously (wired as the font family with fallback to obviously-fallback, system-ui, sans-serif). Obviously appears at 48px / weight 700 for the hero h1, at 36px / weight 700 for section h2s, at 30px / weight 700 for feature headings, and at 20px / weight 700 for callout text. A weight-300 variant of Obviously also appears at 36px for one section-level subheading. Body, nav, and all UI text run system-ui sans-serif at weights 300–600. For open-source substitutes: Obviously is a proprietary family from OH no Type Co — Neue Montreal at weight 700 or Cabinet Grotesk at weight 800 are the closest alternatives for the bold display tier."
    - id: "canvas-color"
      title: "Why does Astro use a near-black (#0c0f19) instead of pure black?"
      answer: "The near-black canvas (#0c0f19 — clustered from three close values #0c0f19, #0d0f14, #060913) carries a very slight blue-violet cast in its oklch hue (h=271). This blue-violet undertone is deliberate: it makes the purple-to-blue gradient mesh in the hero read as continuous with the page surface rather than sitting on top of a neutral black. Against pure #000000, the gradient would feel pasted; against the blue-tinted near-black, it reads as depth. Linear uses the same technique with a blue-tinted near-black; Astro applies it to support purple-spectrum voltages instead of violet-purple."
    - id: "rounded-style"
      title: "What is Astro's corner-radius philosophy?"
      answer: "Astro runs a two-tier radius scale. The dominant tier is 9999px pill — 58 occurrences in the captured page, used for the 'Astro 6.1' version badge, the 'Get Started' and 'Available now' CTAs, the framework integration badges (React, Vue, Preact, Svelte, SolidJS), and the newsletter email input. The second tier covers cards and code surfaces: 8px (27 occurrences) for code panels and dark UI blocks, 12px (20 occurrences) for feature cards, and 16px (21 occurrences) for larger card surfaces. There is no tight 2–4px tier. The system is pill-or-card: either fully rounded or soft-square."
    - id: "use-in-project"
      title: "Can I use this DESIGN.md to build my own web-framework marketing site?"
      answer: "Yes — the file is designed to be fed into Claude, Cursor, or any AI tool that reads structured design tokens. The agent will reproduce Astro's specific moves: deep near-black blue-tinted canvas, purple-to-blue gradient atmosphere behind the hero, Obviously-equivalent bold display at weight 700, system-ui body at weight 300, ghost-pill CTAs, and the teal terminal accent for code surfaces. One important caveat on the gradient: the Astro gradient is a radial mesh, not a linear sweep — the token system captures the color anchors but not the radial geometry. AI tools will need the gradient CSS written explicitly to reproduce the atmospheric depth."

mockups:
  - "marketing-hero"
  - "code-editor-ide"

colors:
  primary: "#b845ed"
  primary-fuchsia: "#f041ff"
  primary-royal: "#3245ff"
  primary-royal-mid: "#2f4cb3"
  aqua: "#4bf3c8"
  sky: "#54b9ff"
  canvas: "#0c0f19"
  surface-1: "#2c2c2c"
  surface-2: "#343841"
  surface-code: "#18181b"
  ink: "#f2f6fa"
  ink-muted: "#bfc1c9"
  ink-dim: "#858b98"
  hairline: "#545864"
  hairline-light: "#e5e7eb"
  amber: "#ffd493"
  accent-periwinkle: "#acafff"

typography:
  display-xl:
    fontFamily: "Obviously, obviously-fallback, system-ui, sans-serif"
    fontSize: 48px
    fontWeight: 700
    lineHeight: 52.8px
    letterSpacing: 0
  display-md:
    fontFamily: "Obviously, obviously-fallback, system-ui, sans-serif"
    fontSize: 36px
    fontWeight: 700
    lineHeight: 40px
    letterSpacing: 0
  display-sm:
    fontFamily: "Obviously, obviously-fallback, system-ui, sans-serif"
    fontSize: 30px
    fontWeight: 700
    lineHeight: 36px
    letterSpacing: 0
  display-sub:
    fontFamily: "Obviously, obviously-fallback, system-ui, sans-serif"
    fontSize: 36px
    fontWeight: 300
    lineHeight: 40px
    letterSpacing: 0
  heading-md:
    fontFamily: "Obviously, obviously-fallback, system-ui, sans-serif"
    fontSize: 20px
    fontWeight: 700
    lineHeight: 28px
    letterSpacing: 0
  body-lg:
    fontFamily: "ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\""
    fontSize: 18px
    fontWeight: 200
    lineHeight: 27px
    letterSpacing: 0
  body-md:
    fontFamily: "ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\""
    fontSize: 16px
    fontWeight: 300
    lineHeight: 24px
    letterSpacing: 0
  body-sm:
    fontFamily: "ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\""
    fontSize: 14px
    fontWeight: 400
    lineHeight: 25.375px
    letterSpacing: 0
  label-md:
    fontFamily: "ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\""
    fontSize: 16px
    fontWeight: 600
    lineHeight: 24px
    letterSpacing: 0
  label-sm:
    fontFamily: "ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\""
    fontSize: 14px
    fontWeight: 300
    lineHeight: 20px
    letterSpacing: 0
  nav-link:
    fontFamily: "ui-sans-serif, system-ui, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\""
    fontSize: 16px
    fontWeight: 400
    lineHeight: 24px
    letterSpacing: 0
  mono-md:
    fontFamily: "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, \"Liberation Mono\", \"Courier New\", monospace"
    fontSize: 13.6px
    fontWeight: 400
    lineHeight: 22.44px
    letterSpacing: 0
  code-badge:
    fontFamily: "MDIO, md-io-fallback, monospace"
    fontSize: 12px
    fontWeight: 400
    lineHeight: 16px
    letterSpacing: "0.3px"

rounded:
  none: "0px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "20px"
  full: "9999px"

spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  base: "16px"
  lg: "24px"
  xl: "32px"
  2xl: "48px"

components:
  button-primary:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    rounded: "{rounded.full}"
    padding: "8px 16px"
    height: "42px"
    borderColor: "{colors.ink-dim}"
  button-primary-hover:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    rounded: "{rounded.full}"
    padding: "8px 16px"
    height: "42px"
  button-secondary:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    rounded: "{rounded.full}"
    padding: "8px 16px"
    height: "40px"
  top-nav:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.nav-link}"
    rounded: "{rounded.none}"
    padding: "0px 32px"
    height: "48px"
  nav-link:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.nav-link}"
    padding: "0px"
  version-badge:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    rounded: "{rounded.full}"
    padding: "4px 12px"
    borderColor: "{colors.hairline}"
  hero-heading:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.display-xl}"
    padding: "0px"
  section-heading:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.display-md}"
  section-subheading:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.display-sub}"
  body-paragraph:
    backgroundColor: "transparent"
    textColor: "{colors.ink-muted}"
    typography: "{typography.body-md}"
  body-paragraph-dim:
    backgroundColor: "transparent"
    textColor: "{colors.ink-dim}"
    typography: "{typography.body-sm}"
  framework-badge:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    rounded: "{rounded.full}"
    padding: "8px 16px"
  card-dark:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.ink}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.md}"
    padding: "16px"
  card-feature:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.lg}"
    padding: "16px"
    borderColor: "{colors.hairline}"
  terminal-panel:
    backgroundColor: "{colors.surface-code}"
    textColor: "{colors.ink}"
    typography: "{typography.mono-md}"
    rounded: "{rounded.sm}"
    padding: "16px"
    borderColor: "{colors.hairline}"
  chart-bar:
    backgroundColor: "{colors.aqua}"
    textColor: "{colors.canvas}"
    rounded: "{rounded.sm}"
  text-input:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body-md}"
    rounded: "{rounded.sm}"
    padding: "4px 12px"
    height: "40px"
    borderColor: "{colors.surface-1}"
  newsletter-input:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body-md}"
    rounded: "{rounded.full}"
    padding: "8px 24px"
    height: "44px"
    borderColor: "{colors.hairline}"
  code-block:
    backgroundColor: "{colors.surface-code}"
    textColor: "{colors.ink}"
    typography: "{typography.mono-md}"
    rounded: "{rounded.sm}"
    padding: "16px"
  footer:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink-dim}"
    typography: "{typography.body-sm}"
    padding: "48px 32px"
---

## Overview

Astro's marketing site is the only major web-framework brand that makes a performance benchmark chart a hero-tier design element. **Gradient-as-atmosphere.** Where Next.js uses photography and Remix uses a bare white-on-dark canvas, Astro fills the hero with a radial gradient mesh — purple through fuchsia through royal blue — that reads as deep space rather than a brand-color fill. The gradient is not a background gradient behind content; it is the visual identity, and it spans the entire above-fold zone without resolving to a single named accent. The CTA button is a ghost pill outline in white, refusing to compete chromatically with the atmosphere behind it.

Typography makes the second structural decision. **Weight extremes as signature**: the hero h1 runs Obviously at 48px / weight 700 — a blocky, high-confidence geometric display. Body text runs system-ui at weight 300. The gap between headline and body is 400 weight units; no other framework marketing site captures this range. Section subheadings also drop to Obviously weight 300 for one registered rhetorical move ("Astro is a JavaScript web framework optimized for building fast, content-driven websites"), where the weight drop reads as a shift from declaration to explanation.

The embedded terminal and code panels use a near-black surface (`{colors.surface-code}` — #18181b) with aqua teal (`{colors.aqua}` — #4bf3c8) as the selection-text and interactive feedback color — a usage consistent with VS Code's aqua selection but mapped to the brand context. The aqua never appears in navigation, CTAs, or body text; it is scoped entirely to code-surface interactivity.

**Key Characteristics:**
- Gradient brand identity: purple (#b845ed) through fuchsia (#f041ff) through royal blue (#3245ff) — no single hex IS the brand.
- Near-black blue-tinted canvas (`{colors.canvas}` — #0c0f19) carries a slight violet cast in oklch that makes the gradient atmospheric rather than pasted-on.
- Ghost-pill primary CTA — white outline, white text, no fill — defers to the gradient behind it rather than competing.
- Obviously at weight 700 for all display headings; system-ui at weight 300 for body — a 400-unit weight gap.
- 9999px pill radius dominates (58 occurrences) for CTAs, version badge, framework integration chips, newsletter input.
- Aqua teal (`{colors.aqua}`) appears only on the performance benchmark chart bar and as terminal selection-text — scoped to code interactivity.
- MDIO monospace family used for the "Astro 6.1" version badge label — a subtle typographic signal of technical precision.
- Performance-benchmark chart (Astro 66% vs WordPress 48%, Gatsby 47%, Next.js 30%, Nuxt 28%) is a named design section, not a footnote.

## Colors

### Brand Gradient

- **Primary Violet** (`{colors.primary}` — #b845ed): frequency 12, all gradient. The warm anchor of the brand gradient mesh — violet at the high-energy end.
- **Primary Fuchsia** (`{colors.primary-fuchsia}` — #f041ff): frequency 10, all gradient. The mid-point of the brand gradient — fuchsia that reads as pink-violet.
- **Primary Royal** (`{colors.primary-royal}` — #3245ff): frequency 13, all gradient. The cool anchor of the brand gradient — a near-indigo royal blue. The page meta themeColor (a middle-purple) sits perceptually between the violet and fuchsia anchors but does not appear as a standalone rendered color.
- **Primary Royal Mid** (`{colors.primary-royal-mid}` — #2f4cb3): frequency 5, all gradient. A mid-saturation royal used as a gradient stop on the below-fold feature sections.

### Interactive Accent

- **Aqua** (`{colors.aqua}` — #4bf3c8): frequency 39. Used as text (18), border (17), gradient (4). Wired as `--ec-uiSelFg` (terminal selection-foreground). The interactive feedback color — scoped entirely to code panels, the performance chart bar fill, and terminal selection states. Never appears in marketing chrome.
- **Sky** (`{colors.sky}` — #54b9ff): frequency 32. Used as text (16), border (16). A lighter periwinkle-blue that appears in code syntax highlighting and feature-card border accents on the dark surface.

### Surface

- **Canvas** (`{colors.canvas}` — #0c0f19): frequency 35 as background. The deep near-black canvas with a subtle violet cast (oklch hue ~271). The page floor that makes the purple-blue gradient read as atmospheric depth.
- **Surface-1** (`{colors.surface-1}` — #2c2c2c): frequency 23 as background. The dark card and panel surface — used for framework-integration badge backgrounds and feature card interiors.
- **Surface-2** (`{colors.surface-2}` — #343841): frequency 6 as background. Secondary elevated surface for the performance chart panel and sidebar navigation elements.
- **Surface Code** (`{colors.surface-code}` — #18181b): frequency 8. The code editor and terminal panel background — wired as `--ec-codeBg` and `--ec-frm-edBg`. Darker than surface-1 to keep code panels visually distinct.

### Text

- **Ink** (`{colors.ink}` — #f2f6fa): frequency 444 — the dominant text color on the dark canvas. Slightly blue-tinted near-white (clustered from #f2f6fa and pure white) that softens the stark contrast against the near-black canvas.
- **Ink Muted** (`{colors.ink-muted}` — #bfc1c9): frequency 84. Body paragraph text — a cool light gray, 84 text occurrences on body and supporting-paragraph elements.
- **Ink Dim** (`{colors.ink-dim}` — #858b98): frequency 73. Caption text and secondary labels — used under feature headings and for metadata labels. 15 text + 50 border occurrences.

### Hairlines

- **Hairline** (`{colors.hairline}` — #545864): frequency 8, predominantly borders. Dark panel borders — used on code editor tabs, terminal frame outlines, and card boundaries.
- **Hairline Light** (`{colors.hairline-light}` — #e5e7eb): frequency 628 — the dominant border tone. Used for hairline card outlines in the feature grid below the fold (clustered from #e5e7eb and a near-white code-editor chrome value).

### Syntax & Illustrations

- **Amber** (`{colors.amber}` — #ffd493): frequency 6. Warm amber used in code-syntax highlighting and as an illustration accent on framework-integration icons.
- **Accent Periwinkle** (`{colors.accent-periwinkle}` — #acafff): frequency 6. A lavender-periwinkle used in code annotations and the occasional feature-card accent.

## Typography

### Font Families

The system runs three distinct families. **Obviously** (the proprietary display family from OH no Type Co) handles every heading level with weight 700 for display and one weight-300 rhetorical variant. **System-ui** (resolved as ui-sans-serif) handles all body, nav, button, and label text at weights 200–600 — a deliberately broad weight range. **MDIO** (a specialized monospace from Mass-Driver) appears only in the "Astro 6.1" version badge as a 12px label — a micro-typographic signal of technical care.

The two-variable-weight pairings (Obviously 700/300 and system-ui 200/600) produce the page's extreme weight range.

### Hierarchy

| Token | Size | Weight | Line Height | Use |
|---|---|---|---|---|
| `{typography.display-xl}` | 48px | 700 | 52.8px | Hero h1 ("The web framework for content-driven websites") |
| `{typography.display-md}` | 36px | 700 | 40px | Section h2 headings |
| `{typography.display-sm}` | 30px | 700 | 36px | Feature section headings |
| `{typography.display-sub}` | 36px | 300 | 40px | Subheading variant ("Astro is a JavaScript web framework…") |
| `{typography.heading-md}` | 20px | 700 | 28px | Callout bold text and feature card titles |
| `{typography.body-lg}` | 18px | 200 | 27px | Hero sub-paragraph |
| `{typography.body-md}` | 16px | 300 | 24px | Default body paragraphs |
| `{typography.label-md}` | 16px | 600 | 24px | CTA button labels, nav active states |
| `{typography.nav-link}` | 16px | 400 | 24px | Top-nav link labels |
| `{typography.mono-md}` | 13.6px | 400 | 22.44px | Code editor and terminal text |
| `{typography.code-badge}` | 12px | 400 | 16px | Version badge label (MDIO family) |

### Principles

The body weight of 300 (system-ui) is the system's most distinctive typographic move. At 16px / 24px line-height on a near-black canvas with muted-gray text, weight 300 remains legible because the line-height is generous and the letter-spacing is default — not because the contrast is high. The hero sub-paragraph drops to weight 200, which is at the edge of readability for body text; it works here because the sub-paragraph is short (two lines) and sits on a gradient-rich background that already carries visual weight. Matching this move requires a neutral-geometry system sans (Inter or Inter Display) that remains readable at weight 200–300.

### Note on Font Substitutes

Obviously is proprietary (OH no Type Co). **Neue Montreal** at weight 700 or **Cabinet Grotesk** at weight 800 are the closest open-source alternatives for the bold display tier. For the weight-300 variant, these same families at 300 work. For system-ui body, the browser's own system sans is acceptable; **Inter** at matching weights is the controlled substitute.

## Layout

### Spacing System

- **Base unit:** 8px (63 occurrences — the dominant gap value).
- **Tokens:** `{spacing.xs}` 4px · `{spacing.sm}` 8px · `{spacing.md}` 12px · `{spacing.base}` 16px · `{spacing.lg}` 24px · `{spacing.xl}` 32px · `{spacing.2xl}` 48px.
- **Section padding (vertical):** variable by section — the hero has no explicit top padding (content begins immediately under the nav), while below-fold sections use ~64px vertical spacing.
- **Card internal padding:** 16px on dark feature cards; `{spacing.base}` (16px) standard.
- **Newsletter section padding:** `{spacing.2xl}` (48px) vertical.

### Grid & Container

- **Max content width:** ~1024px on the hero headline; ~1280px on the feature grid.
- **Hero:** centered single column, gradient as full-bleed background behind transparent surfaces.
- **Performance chart:** full-width bar chart below the hero, with the Astro bar in aqua teal against gray comparison bars.
- **Framework integration section:** single row of circular icon chips (React, Vue, Preact, Svelte, SolidJS) with a code-panel split to the right.
- **Feature grid ("Fully Featured"):** 3-column masonry-style grid with dark bordered cards, each showing a feature illustration (icons, code snippets, interface screenshots).

### Rhythm

The page moves through four distinct bands: gradient-atmosphere hero → white-canvas interstitial (the "What is Astro?" section on a lighter near-white) → dark-panel code-and-benchmark sections → dark feature grid. The white interstitial is the most unusual: a band of lighter surface that creates a breath between the dramatic gradient hero and the technical content below.

## Elevation

The system uses **surface-color contrast** rather than shadows for depth. Dark-surface tiers (`{colors.surface-code}` → `{colors.surface-1}` → `{colors.canvas}`) create a 3-step elevation ladder. Shadows appear in only 8 occurrences (all as rgba-black ink on the `{colors.surface-code}` terminal panel). The hairline border (`{colors.hairline-light}` — #e5e7eb at 628 occurrences) carries the elevation signal on the below-fold feature cards, where a 1px border lifts the card off the lighter page band.

## Shapes

The radius scale is **pill-dominant**: 9999px (58 occurrences) for interactive chrome, with a 8–16px tier for content cards.

- `{rounded.none}` 0px — hero headline zone, section dividers.
- `{rounded.sm}` 8px — code panels, terminal frames, small input fields. 27 occurrences.
- `{rounded.md}` 12px — feature cards in the grid below the fold. 20 occurrences.
- `{rounded.lg}` 16px — larger card surfaces, embedded product UI screenshots. 21 occurrences.
- `{rounded.xl}` 20px — one large card variant.
- `{rounded.full}` 9999px — version badge, CTA buttons, framework integration chips, newsletter input, nav chips. 58 occurrences — the dominant radius.

The pill dominance is striking: the system uses 9999px more than all other radii combined. Pills read as the "interactive" shape — everything a user might tap or read-as-a-tag gets the pill treatment.

## Components

**`button-primary`** — The ghost-pill CTA. Near-black `{colors.canvas}` fill, white text, 9999px pill radius, 8×16px padding, 42px height, 1px `{colors.ink-dim}` border. "Get Started" and "Skip to content" are the canonical instances. Notably, the primary CTA is not a chromatic fill — it is a ghost outline.

**`button-primary-hover`** — Lifts to `{colors.surface-2}` (#343841) on hover — a slightly lighter dark surface.

**`version-badge`** — Dark `{colors.surface-1}` pill, white text, 1px `{colors.hairline}` border, 4×12px padding. "Astro 6.1" and "Available now →" rendered as two adjacent pill badges in the hero.

**`hero-heading`** — Obviously 48px / weight 700, white text, transparent background. "The web framework for content-driven websites" — the loudest typographic moment.

**`section-heading`** — Obviously 36px / weight 700 for major section h2s. Falls to `{typography.display-sub}` (36px / weight 300) for the descriptive "What is Astro?" subheading.

**`body-paragraph`** — System-ui 16px / weight 300, muted-gray `{colors.ink-muted}` text. The lightest-weight body style in any major framework brand.

**`framework-badge`** — `{colors.surface-2}` pill, white text, 8×16px padding. Used for React, Vue, Preact, Svelte, SolidJS integration chips in the Zero Lock-In section.

**`card-dark`** — `{colors.surface-1}` fill, white text, 12px radius, 16px padding. The default feature card in the "Fully Featured" grid.

**`card-feature`** — Canvas-colored with `{colors.hairline-light}` border, 16px radius. The lighter feature card variant used in the "built-on" section.

**`terminal-panel`** — `{colors.surface-code}` fill, white text, mono typography, 8px radius, 1px `{colors.hairline}` border. The embedded code editor and terminal-output panel.

**`chart-bar`** — Aqua `{colors.aqua}` fill, 8px radius. The Astro bar in the Core Web Vitals performance comparison chart — the only place where `{colors.aqua}` appears as a fill.

**`newsletter-input`** — Canvas fill, white text, pill radius (9999px), 8×24px padding, 44px height, 1px `{colors.hairline}` border.

**`code-block`** — `{colors.surface-code}` fill, 8px radius, 16px padding, monospace text.

**`footer`** — Canvas fill, dim-gray text, 48×32px padding. Continues the near-black surface without surface contrast.

## Do's and Don'ts

**Do** treat the gradient as the brand identity, not any individual hex. Violet, fuchsia, and royal blue only work together as a mesh; using any one color alone as a "brand accent" produces a result that reads as generic purple SaaS, not as Astro.

**Do** keep body text at weight 200–300. The weight-300 body is the page's most unusual move and its most transferable; it requires a generous line-height (24px at 16px font size, which is 1.5) to maintain legibility.

**Do** treat the aqua teal (`{colors.aqua}` — #4bf3c8) as a code-interactivity-only color. Its role is scoped entirely to the terminal selection-text token and the performance chart bar; deploying it in navigation, CTAs, or card accents would break the color discipline.

**Do** use 9999px pill radius for all interactive chrome (buttons, badges, chips, inputs). The system's pill-dominant radius scale makes every interactive element visually distinct from content cards; switching to 8px radius on CTAs would make them read as cards.

**Don't** add a chromatic fill to the primary CTA button. The ghost-pill treatment is the deliberate move — the gradient behind the headline is the visual energy, and the CTA defers to it. A purple or blue fill button would compete with the gradient and reduce the atmospheric reading.

**Don't** use `{colors.aqua}` (#4bf3c8) or `{colors.sky}` (#54b9ff) as section backgrounds or card fills. Both appear exclusively as text, border, or gradient tones in the captured page (0 background occurrences for sky, 0 for aqua outside the chart bar). Filling a card or section with either color would create a jarring chromatic block on a page built around atmospheric gradient.

**Don't** apply `{typography.display-sub}` (36px / weight 300) to more than one heading per section. The weight-300 display variant appears exactly once in the captured page as a contrast move against the surrounding weight-700 headings; using it broadly would collapse the weight range that defines the typographic identity.

**Don't** use the MDIO mono family (`{typography.code-badge}`) outside the version badge context. It appears exactly twice in the captured page — both in the "Astro 6.1" badge label. It signals a specific typographic precision; deploying it in body or label contexts would read as misplaced terminal aesthetic.

## Known Gaps

- **Gradient geometry:** the token system captures the gradient color anchors but not the radial mesh geometry. The actual CSS gradient is a radial gradient with multiple stops and blur layers; the colors alone are not sufficient to reproduce the atmospheric depth without the radial definition.
- **Hover states beyond button:** full hover, focus, and active state matrix is not captured for nav links, framework chips, or the newsletter input.
- **Light-mode variant:** the "What is Astro?" section below the hero uses a lighter near-white surface band (#f2f6fa), but no full light-mode token set is declared on the captured page.
- **Responsive breakpoints:** all captured surfaces are desktop-only. Mobile navigation, single-column card layout, and responsive chart sizing are not documented.
- **Motion:** the hero gradient animates on mouse-move and the performance chart bars animate on scroll-into-view; easing curves and animation timing are not captured.
- **Product application surfaces:** this DESIGN.md captures the marketing site only. The Astro documentation site (docs.astro.build) and the Astro DB product carry separate token sets.
- **Social proof section:** the customer logo wall (Google, Microsoft, Porsche, Visa, etc.) uses white logos at uniform treatment, but the logo-wall component token is not fully specified here — it would follow the same pattern as Cloudflare's monochrome logo wall.
