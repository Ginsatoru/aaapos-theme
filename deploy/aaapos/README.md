# WordPress Theme Color Customizer System

## Complete Implementation Guide

This document provides step-by-step instructions for implementing the comprehensive color customizer system in your WordPress theme.

---

## Files Created/Updated

### New/Replaced Files:
1. `inc/customizer/colors.php` - Complete color customizer controls
2. `inc/customizer/customizer.php` - CSS output and helper functions
3. `assets/css/variables.css` - CSS custom properties
4. `assets/js/customizer-preview.js` - Live preview JavaScript
5. `assets/css/components/buttons.css` - Updated button styles
6. `assets/css/components/header.css` - Updated header styles

### Reference Files:
- `inc/enqueue-customizer-snippet.php` - Code to add to your enqueue.php

---

## Installation Steps

### Step 1: Backup Your Current Files
Before making any changes, backup these files:
- `inc/customizer/colors.php`
- `inc/customizer/customizer.php`
- `assets/css/variables.css`
- `assets/js/customizer-preview.js`
- `assets/css/components/buttons.css`
- `assets/css/components/header.css`

### Step 2: Replace Files
Copy the new files to their respective locations in your theme directory.

### Step 3: Update enqueue.php
Add this code to your `inc/enqueue.php` if not already present:

```php
/**
 * Enqueue Customizer Live Preview Script
 */
function aaapos_customizer_live_preview() {
    wp_enqueue_script(
        'aaapos-customizer-preview',
        AAAPOS_ASSETS_URI . '/js/customizer-preview.js',
        array('jquery', 'customize-preview'),
        AAAPOS_VERSION,
        true
    );
}
add_action('customize_preview_init', 'aaapos_customizer_live_preview');
```

### Step 4: Verify functions.php Includes
Make sure your `functions.php` includes the customizer files:

```php
$includes = [
    // ... other includes ...
    'inc/customizer/customizer.php',
    'inc/customizer/colors.php',
    // ... other includes ...
];
```

### Step 5: Clear Caches
After installation:
1. Clear any caching plugins
2. Clear browser cache
3. Regenerate CSS if using any CSS optimization plugins

---

## Customizer Structure

The color system is organized under **Appearance > Customize > Theme Colors**:

### Color Presets
Quick color scheme presets to get started.

### Primary Brand Colors
- Primary Color
- Primary Hover
- Primary Dark
- Primary Light

### Secondary & Accent Colors
- Secondary Color
- Secondary Hover
- Accent Color
- Accent Hover

### Text Colors
- Body Text
- Secondary Text
- Heading Text
- Text on Dark Backgrounds
- Link Color
- Link Hover Color

### Background Colors
- Primary Background
- Secondary Background
- Tertiary Background
- Dark Background

### Button Colors
- Primary Button Background
- Primary Button Text
- Primary Button Hover Background
- Secondary Button Background
- Secondary Button Text

### UI Elements
- Border Color
- Border Light
- Focus Ring Color

### Status Colors
- Success Color
- Warning Color
- Error/Danger Color
- Info Color

### Header Colors
- Topbar Background
- Topbar Gradient End
- Topbar Text
- Header Background
- Header Text
- Navigation Link
- Navigation Hover/Active
- Navigation Underline
- Cart Badge Color

### Footer Colors
- Footer Background
- Footer Text
- Footer Headings
- Footer Links
- Footer Link Hover
- Footer Border

### WooCommerce Colors
- Price Color
- Sale Price Color
- Sale Badge Background
- Sale Badge Text
- Add to Cart Background
- Add to Cart Text
- Add to Cart Hover
- Star Rating Color
- In Stock Color
- Out of Stock Color

---

## CSS Variable Reference

All colors are available as CSS custom properties. Use them in your CSS like this:

```css
.my-element {
    background-color: var(--color-primary);
    color: var(--color-text);
    border: 1px solid var(--color-border);
}

.my-button:hover {
    background-color: var(--color-primary-hover);
}
```

### Complete Variable List

#### Primary Colors
```css
--color-primary
--color-primary-hover
--color-primary-dark
--color-primary-light
--color-primary-rgb  /* For rgba() usage */
```

#### Secondary & Accent
```css
--color-secondary
--color-secondary-hover
--color-secondary-rgb
--color-accent
--color-accent-hover
--color-accent-rgb
```

#### Text Colors
```css
--color-text
--color-text-light
--color-text-dark
--color-text-inverse
--color-link
--color-link-hover
```

#### Background Colors
```css
--color-bg-primary
--color-bg-secondary
--color-bg-tertiary
--color-bg-dark
```

#### Button Colors
```css
--color-button-primary-bg
--color-button-primary-text
--color-button-primary-hover-bg
--color-button-secondary-bg
--color-button-secondary-text
```

#### UI Elements
```css
--color-border
--color-border-light
--color-focus-ring
```

#### Status Colors
```css
--color-success
--color-warning
--color-danger
--color-info
```

#### Header Colors
```css
--color-topbar-bg
--color-topbar-bg-end
--color-topbar-text
--color-header-bg
--color-header-text
--color-nav-link
--color-nav-hover
--color-nav-active
--color-nav-underline
--color-cart-badge
```

#### Footer Colors
```css
--color-footer-bg
--color-footer-text
--color-footer-heading
--color-footer-link
--color-footer-link-hover
--color-footer-border
```

#### WooCommerce Colors
```css
--color-woo-price
--color-woo-sale-price
--color-woo-sale-badge
--color-woo-sale-badge-text
--color-woo-add-to-cart-bg
--color-woo-add-to-cart-text
--color-woo-add-to-cart-hover
--color-woo-rating
--color-woo-in-stock
--color-woo-out-of-stock
```

#### Gradients
```css
--gradient-primary
--gradient-topbar
--gradient-button
--gradient-light
```

---

## Updating Other CSS Files

To make other component CSS files use the new color system, replace hardcoded colors with CSS variables.

### Example: Before
```css
.card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    color: #374151;
}

.card-title {
    color: #1f2937;
}

.card:hover {
    border-color: #0ea5e9;
}
```

### Example: After
```css
.card {
    background: var(--color-bg-primary);
    border: 1px solid var(--color-border);
    color: var(--color-text);
}

.card-title {
    color: var(--color-text-dark);
}

.card:hover {
    border-color: var(--color-primary);
}
```

### Search and Replace Guide

| Old Value | Replace With |
|-----------|--------------|
| `#0ea5e9` | `var(--color-primary)` |
| `#0284c7` | `var(--color-primary-hover)` |
| `#06b6d4` | `var(--color-secondary)` |
| `#f59e0b` | `var(--color-accent)` |
| `#374151` | `var(--color-text)` |
| `#6b7280` | `var(--color-text-light)` |
| `#1f2937` | `var(--color-text-dark)` |
| `#ffffff` | `var(--color-bg-primary)` or `var(--color-white)` |
| `#f9fafb` | `var(--color-bg-secondary)` |
| `#f3f4f6` | `var(--color-bg-tertiary)` |
| `#e5e7eb` | `var(--color-border)` |
| `#10b981` | `var(--color-success)` |
| `#ef4444` | `var(--color-danger)` |
| `#D4AF37` | `var(--color-nav-hover)` |

---

## Troubleshooting

### Colors not updating in customizer preview
1. Check browser console for JavaScript errors
2. Verify customizer-preview.js is being loaded
3. Clear browser cache

### Colors reset after page refresh
1. Ensure customizer settings are saved (click "Publish")
2. Check that customizer.php CSS output function is hooked correctly
3. Verify the inline style is appearing in page source

### Live preview works but frontend doesn't
1. Check that `aaapos_customizer_css_output()` is hooked to `wp_head`
2. Verify the priority (should be 100 or higher to load after main CSS)
3. Check for CSS specificity issues

### Some elements not changing color
1. Check if the element is using the correct CSS variable
2. Look for `!important` declarations that may override variables
3. Verify the variable name matches what's defined in customizer.php

---

## Helper Functions Available

### PHP Functions

```php
// Convert hex to RGB string
$rgb = aaapos_hex_to_rgb('#0ea5e9'); // Returns "14, 165, 233"

// Adjust color brightness (-100 to 100)
$darker = aaapos_adjust_brightness('#0ea5e9', -20);
$lighter = aaapos_adjust_brightness('#0ea5e9', 20);

// Get contrast ratio between two colors
$ratio = aaapos_get_contrast_ratio('#0ea5e9', '#ffffff');

// Get all default colors
$defaults = aaapos_get_default_colors();

// Reset all colors to defaults
aaapos_reset_colors_to_defaults();
```

### JavaScript Functions (in customizer-preview.js)

```javascript
// Update CSS variable
updateCSSVariable('color-primary', '#ff0000');

// Convert hex to RGB
hexToRgb('#0ea5e9'); // Returns "14, 165, 233"

// Adjust brightness
adjustBrightness('#0ea5e9', -20); // Darker
adjustBrightness('#0ea5e9', 20);  // Lighter
```

---

## Color Presets

Five built-in presets are available:

1. **Default (Sky Blue)** - Primary: #0ea5e9
2. **Emerald Green** - Primary: #10b981
3. **Royal Purple** - Primary: #8b5cf6
4. **Sunset Orange** - Primary: #f97316
5. **Slate Professional** - Primary: #475569

---

## Accessibility Notes

- Always ensure sufficient color contrast (WCAG 4.5:1 for normal text)
- Test with browser accessibility tools
- The `aaapos_get_contrast_ratio()` function can help check contrast
- Focus states use `--color-focus-ring` for keyboard navigation visibility

---

## Performance Considerations

- CSS variables are computed once and cached by browsers
- Inline CSS output is minimal (~2KB)
- Live preview uses efficient `setProperty()` calls
- No JavaScript color calculations on page load

---

## Support

If you encounter issues:
1. Check the browser console for errors
2. Verify all files are in the correct locations
3. Ensure WordPress is updated to at least version 5.0
4. Test with default theme to isolate issues#   a a a p o s - t h e m e  
 