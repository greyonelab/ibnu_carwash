---
name: Car Wash Admin
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#45464d'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#0051d5'
  on-secondary: '#ffffff'
  secondary-container: '#316bf3'
  on-secondary-container: '#fefcff'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#001e2f'
  on-tertiary-container: '#008cc7'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#dbe1ff'
  secondary-fixed-dim: '#b4c5ff'
  on-secondary-fixed: '#00174b'
  on-secondary-fixed-variant: '#003ea8'
  tertiary-fixed: '#c9e6ff'
  tertiary-fixed-dim: '#89ceff'
  on-tertiary-fixed: '#001e2f'
  on-tertiary-fixed-variant: '#004c6e'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  h1:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  h2:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
    letterSpacing: -0.01em
  h3:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: '1.4'
    letterSpacing: '0'
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: '0'
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
    letterSpacing: '0'
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.05em
  stats-num:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: '1'
    letterSpacing: -0.03em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 16px
  md: 24px
  lg: 32px
  xl: 48px
  gutter: 24px
  margin: 32px
---

## Brand & Style

The visual identity of the design system is anchored in the concepts of **clarity, fluidity, and industrial precision**. It is designed for high-frequency use by business owners and managers who require immediate access to operational data. 

The style is **Corporate / Modern**, characterized by a rigorous adherence to hierarchy and functional aesthetics. It avoids unnecessary ornamentation, opting instead for a "clean-room" feel that mirrors the service excellence of a premium car wash. The emotional response is one of calm control and reliability, ensuring that complex scheduling and financial reporting feel manageable and structured.

## Colors

The palette utilizes **Deep Navy (#0F172A)** as the primary anchor for navigation and high-level headers, providing a sense of authority and depth. **Action Blue (#2563EB)** serves as the secondary color, used strictly for interactive elements like primary buttons and active states to guide the eye toward productivity.

**Crisp White (#FFFFFF)** is the foundational surface color, maintaining a sterile and organized environment. We use a spectrum of **Professional Grays** (Slate) for borders, secondary text, and background layering to ensure the interface remains soft on the eyes during long periods of use while maintaining high accessibility ratios.

## Typography

This design system uses **Inter** exclusively to leverage its systematic, utilitarian nature. The font's high x-height and neutral character make it ideal for data-heavy dashboards where legibility is paramount. 

Headlines are tight and bold to establish clear section boundaries. Body text utilizes a generous line height (1.5 - 1.6) to prevent eye fatigue in dense tables. A specialized "stats-num" style is included for top-level metrics, using a condensed letter spacing to give financial and performance figures a high-impact, professional appearance.

## Layout & Spacing

The system employs a **12-column fluid grid** with a fixed gutter of 24px, ensuring that statistical cards and data tables reflow logically across desktop and tablet views. 

The rhythm is built on a **4px baseline**, with standard component padding favoring the "md" (24px) unit to maintain an airy, clean aesthetic. Sidebars are fixed at 280px for stability, while the main content area expands. Alignment should prioritize left-justification for data columns to enhance scannability in reports.

## Elevation & Depth

Depth is communicated through **tonal layers and low-contrast outlines** rather than heavy shadows. The primary background uses a subtle off-white tint, while interactive cards and containers sit on pure white surfaces.

To distinguish between the base and elevated elements, we use a single, extremely soft ambient shadow: `0px 1px 3px rgba(15, 23, 42, 0.08)`. For modal overlays or dropdowns, a secondary shadow with more vertical displacement is used. This approach keeps the UI feeling light and "crisp" while providing enough visual affordance to identify clickable regions.

## Shapes

The design system uses a **Soft** shape language. Standard UI elements like input fields, buttons, and small cards utilize a 0.25rem (4px) corner radius. Large containers and main dashboard cards use a 0.5rem (8px) radius. 

This subtle rounding strikes a balance between the "mechanical" feel of a service business and the "modern" feel of a software tool. It avoids the playfulness of fully rounded pills, maintaining a serious, professional tone suitable for business management.

## Components

### Buttons & Inputs
Buttons feature high-contrast backgrounds with white text for primary actions. Form inputs use a subtle 1px border in `Slate-200`, which shifts to `Action Blue` on focus. Labels are consistently placed above the input field using the `label-md` typographic style for maximum clarity.

### Stats Cards
Cards for key metrics (e.g., "Daily Wash Count," "Revenue") are the centerpiece of the dashboard. They should feature a top-aligned label, a large `stats-num` value, and a small bottom-aligned trend indicator (green for growth, red for decline).

### Data Tables
Tables are designed for high-density reporting. Row heights are set to a minimum of 48px to ensure touch-targets are accessible. Alternate row striping is avoided in favor of thin horizontal dividers to keep the look "clean." Header columns are uppercase and slightly tracked out to distinguish them from row data.

### Status Chips
Use small, rounded chips for status tracking (e.g., "In Progress," "Completed," "Pending"). These use low-saturation background tints of green, blue, or amber with high-saturation text to indicate state without overwhelming the visual hierarchy.