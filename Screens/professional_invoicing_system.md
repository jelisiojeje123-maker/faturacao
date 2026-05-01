---
name: Professional Invoicing System
colors:
  surface: '#faf8ff'
  surface-dim: '#d9d9e5'
  surface-bright: '#faf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3fe'
  surface-container: '#ededf9'
  surface-container-high: '#e7e7f3'
  surface-container-highest: '#e1e2ed'
  on-surface: '#191b23'
  on-surface-variant: '#434655'
  inverse-surface: '#2e3039'
  inverse-on-surface: '#f0f0fb'
  outline: '#737686'
  outline-variant: '#c3c6d7'
  surface-tint: '#0053db'
  primary: '#004ac6'
  on-primary: '#ffffff'
  primary-container: '#2563eb'
  on-primary-container: '#eeefff'
  inverse-primary: '#b4c5ff'
  secondary: '#545f73'
  on-secondary: '#ffffff'
  secondary-container: '#d5e0f8'
  on-secondary-container: '#586377'
  tertiary: '#943700'
  on-tertiary: '#ffffff'
  tertiary-container: '#bc4800'
  on-tertiary-container: '#ffede6'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dbe1ff'
  primary-fixed-dim: '#b4c5ff'
  on-primary-fixed: '#00174b'
  on-primary-fixed-variant: '#003ea8'
  secondary-fixed: '#d8e3fb'
  secondary-fixed-dim: '#bcc7de'
  on-secondary-fixed: '#111c2d'
  on-secondary-fixed-variant: '#3c475a'
  tertiary-fixed: '#ffdbcd'
  tertiary-fixed-dim: '#ffb596'
  on-tertiary-fixed: '#360f00'
  on-tertiary-fixed-variant: '#7d2d00'
  background: '#faf8ff'
  on-background: '#191b23'
  surface-variant: '#e1e2ed'
typography:
  h1:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  h2:
    fontFamily: Inter
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
    letterSpacing: -0.02em
  h3:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
---

## Brand & Style

The design system is engineered for a Professional Invoicing System, prioritizing clarity, trust, and efficiency. The brand personality is authoritative yet accessible, targeting freelance professionals and small-to-medium business owners who require a dependable financial interface. 

The aesthetic follows a **Corporate / Modern** style with heavy influences from **Minimalism**. It leverages expansive whitespace to reduce cognitive load during complex financial tasks. The visual language is high-fidelity, utilizing subtle depth and a restrained color palette to ensure the user's data—invoices, balances, and reports—remains the primary focus. The emotional response should be one of "organized calm," transforming the potentially stressful task of billing into a streamlined, frictionless experience.

## Colors

This design system utilizes a high-contrast functional palette. 
- **Primary Blue (#2563EB)**: Reserved for primary actions, active states, and brand presence.
- **Secondary Dark Navy (#1E293B)**: Used for structural navigation, sidebar backgrounds, and high-emphasis text.
- **Accent Green (#22C55E)**: Applied exclusively to "Paid" statuses, success notifications, and positive financial trends.
- **Surface & Background**: A clear distinction is made between the **Light Gray (#F8FAFC)** canvas and **White (#FFFFFF)** surfaces to create a tiered information architecture.

Text should primarily use Dark Navy for headings to maintain strong hierarchy, while Neutral 500-600 shades are used for secondary labels and metadata.

## Typography

The typography system is built on **Inter**, a typeface designed for screens. To achieve the "Professional SaaS" aesthetic, we employ a strict hierarchy:
- **Bold Headings**: H1 and H2 use a bold weight (700) with slight negative letter-spacing to create a compact, modern editorial feel.
- **Readable Body**: The main interface text uses a 16px base (body-md) to ensure high legibility for financial figures.
- **Utility Labels**: Small caps are used sparingly for category headers in sidebars or table headers to provide clear differentiation from data.

## Layout & Spacing

This design system is governed by a **strict 8px grid system**. All dimensions, padding, and margins must be multiples of 8 (or 4 for micro-adjustments).

The dashboard uses a **Fixed-Fluid hybrid grid**:
- A fixed-width **Sidebar** (280px) for primary navigation.
- A **Fluid 12-column content area** that scales with the viewport, capped at a maximum width of 1440px to prevent excessive line lengths.
- Internal card padding is standardized at **24px (lg)** to ensure data-heavy tables and forms have room to breathe.

## Elevation & Depth

Hierarchy in the design system is communicated through **Tonal Layers** and **Ambient Shadows**. 

- **Level 0 (Background)**: #F8FAFC - The base canvas.
- **Level 1 (Surface)**: #FFFFFF - All primary cards and content containers. These use a "Soft Shadow" (Y: 4px, Blur: 12px, Opacity: 5% Black) to lift them slightly from the background.
- **Level 2 (Interactive)**: Hover states on cards or dropdown menus. These use a more pronounced "Floating Shadow" (Y: 8px, Blur: 20px, Opacity: 8% Black) to indicate interactivity.
- **Level 3 (Overlays)**: Modals and Toast notifications. These use a deep, diffused shadow to focus user attention.

Avoid using heavy borders; depth should feel atmospheric rather than structural.

## Shapes

The shape language is defined by a **Rounded-xl (12px)** standard for primary containers. This "Soft" radius balances professional rigidity with modern approachable design. 

- **Primary Cards**: 12px (rounded-xl)
- **Buttons and Inputs**: 8px (base) to maintain a slightly sharper, more "active" feel than the containers they sit within.
- **Status Chips**: Fully rounded (pill) to distinguish them from interactive buttons.
- **Inner Elements**: Elements inside a card should use a nested radius (typically 8px) to maintain visual harmony with the 12px outer boundary.

## Components

The components within this design system are optimized for a data-centric invoicing environment:

- **Buttons**: Primary buttons are solid Blue (#2563EB) with white text. Secondary buttons use a subtle Gray-100 fill or a 1px border. All use 8px border radius and 16px horizontal padding.
- **Input Fields**: Ghost-style borders (1px, Neutral-200) that transition to Primary Blue on focus. Labels sit above the field in `label-md` style.
- **Data Tables**: Minimalist approach with no vertical lines. Row hover states use Neutral-50. The "Amount" column always uses bold font weights for quick scanning.
- **Status Chips**: Small, pill-shaped indicators.
    - *Paid*: Green background (10% opacity) with Green text.
    - *Pending*: Blue background (10% opacity) with Blue text.
    - *Overdue*: Red background (10% opacity) with Red text.
- **Cards**: The primary container for all dashboard widgets. Must include a 24px internal padding and the standard 12px (rounded-xl) corner radius.
- **Navigation Sidebar**: Dark Navy (#1E293B) background with high-contrast white text for the active state and 60% opacity for inactive states.