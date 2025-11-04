import { computed } from 'vue';

// --- COLOR UTILITY FUNCTIONS ---

/**
 * Converts a Hex color string (#RRGGBB) to an HSL array [h, s, l].
 * H, S, L are percentages (0-100).
 */
function hexToHsl(hex) {
    if (!hex || hex.length < 7) return [0, 0, 0];

    // Convert hex to RGB
    let r = parseInt(hex.substring(1, 3), 16) / 255;
    let g = parseInt(hex.substring(3, 5), 16) / 255;
    let b = parseInt(hex.substring(5, 7), 16) / 255;

    let min = Math.min(r, g, b);
    let max = Math.max(r, g, b);
    let delta = max - min;
    let h,
        s,
        l = (max + min) / 2;

    if (delta === 0) {
        h = s = 0; // achromatic
    } else {
        s = l > 0.5 ? delta / (2 - max - min) : delta / (max + min);
        switch (max) {
            case r:
                h = (g - b) / delta + (g < b ? 6 : 0);
                break;
            case g:
                h = (b - r) / delta + 2;
                break;
            case b:
                h = (r - g) / delta + 4;
                break;
        }
        h /= 6;
    }

    return [Math.round(h * 360), Math.round(s * 100), Math.round(l * 100)];
}

/**
 * Converts an HSL array [h, s, l] back to a Hex color string (#RRGGBB).
 */
function hslToHex(h, s, l) {
    s /= 100;
    l /= 100;

    let c = (1 - Math.abs(2 * l - 1)) * s,
        x = c * (1 - Math.abs(((h / 60) % 2) - 1)),
        m = l - c / 2,
        r = 0,
        g = 0,
        b = 0;

    if (0 <= h && h < 60) {
        r = c;
        g = x;
        b = 0;
    } else if (60 <= h && h < 120) {
        r = x;
        g = c;
        b = 0;
    } else if (120 <= h && h < 180) {
        r = 0;
        g = c;
        b = x;
    } else if (180 <= h && h < 240) {
        r = 0;
        g = x;
        b = c;
    } else if (240 <= h && h < 300) {
        r = x;
        g = 0;
        b = c;
    } else if (300 <= h && h < 360) {
        r = c;
        g = 0;
        b = x;
    }

    r = Math.round((r + m) * 255).toString(16);
    g = Math.round((g + m) * 255).toString(16);
    b = Math.round((b + m) * 255).toString(16);

    if (r.length === 1) r = '0' + r;
    if (g.length === 1) g = '0' + g;
    if (b.length === 1) b = '0' + b;

    return '#' + r + g + b;
}

/**
 * Adjusts the lightness of a Hex color by a delta value (e.g., +10 or -15).
 * Delta is clamped between -100 and 100.
 */
function adjustLightness(hex, delta) {
    let [h, s, l] = hexToHsl(hex);

    // Adjust Lightness (L) and clamp between 0 and 100
    let newL = Math.max(0, Math.min(100, l + delta));

    return hslToHex(h, s, newL);
}

// Export the utility function to determine good contrasting text color
export function getContrastTextColor(hexColor) {
    const [, , l] = hexToHsl(hexColor);
    return l > 60 ? '#1f2937' : '#f9fafb'; // Dark text for light backgrounds, light text for dark
}

/**
 * @param {import('vue').Ref<string>} baseHexRef - A reactive reference to the base hex color.
 * @returns {{ shades: import('vue').ComputedRef<object>, getContrastTextColor: Function }}
 * - An object containing generated color shades and the contrast utility function.
 */
export function useColorPalette(baseHexRef) {
    const shades = computed(() => {
        const baseColor = baseHexRef.value;
        if (!baseColor || !/^#([0-9A-F]{3}){1,2}$/i.test(baseColor)) {
            // Fallback gray for invalid input
            return { 300: '#CCCCCC', 500: '#AAAAAA', 700: '#888888' };
        }

        // Define the lightness adjustment deltas for the palette
        const paletteDeltas = {
            300: 30, // Lighter
            500: 0, // Base
            700: -30, // Darker
        };

        const result = {};
        for (const shade in paletteDeltas) {
            const delta = paletteDeltas[shade];

            if (delta === 0) {
                result[shade] = baseColor;
            } else {
                // Generate the adjusted color
                result[shade] = adjustLightness(baseColor, delta);
            }
        }

        return result;
    });

    // We can also return the utility function for contrast text color
    return { shades, getContrastTextColor };
}
