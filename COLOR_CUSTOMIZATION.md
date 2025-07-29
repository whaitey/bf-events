# Color Customization Feature

## Overview
The BF Events plugin now includes a built-in color customization feature that allows administrators to easily change the main theme color throughout the entire plugin interface.

## How to Use

### 1. Access the Settings
1. Go to **WordPress Admin** → **BSF Events** → **Beállítások** (Settings)
2. Click on the **"Megjelenés"** (Appearance) tab

### 2. Change the Main Color
1. In the "Fő szín" (Main Color) field, click the color picker
2. Choose your desired color
3. Click **"Mentés"** (Save) to apply the changes

### 3. What Gets Updated
The selected color will automatically update:
- **Buttons** - Primary action buttons (indigo style)
- **Accent elements** - Highlights and focus states
- **Interactive elements** - Hover states and active elements
- **Text links** - CTA (Call-to-Action) styled text

## Technical Details

### Color Variables Updated
The system automatically generates and applies these CSS custom properties:
- `--c-indigo`: Your selected main color
- `--c-purple-3`: A darker variant (main color - 30 RGB values)
- `--c-purple-2`: A lighter variant (main color + 30 RGB values)

### Implementation
- **Dynamic CSS Generation**: The color is applied via inline CSS that overrides the default variables
- **Automatic Variants**: The system automatically creates darker and lighter versions of your chosen color
- **Fallback**: If no color is selected, it defaults to the original indigo (#2F24A1)

### Files Modified
- `includes/admin-page.php` - Added color picker field to admin settings
- `bf-events.php` - Added dynamic CSS generation function
- `TODO.md` - Updated to reflect completed feature

## Best Practices

### Color Selection Tips
- **Contrast**: Ensure your chosen color has good contrast with white text
- **Brand Consistency**: Choose a color that matches your brand guidelines
- **Accessibility**: Consider users with color vision deficiencies

### Testing
After changing the color:
1. Check button hover states
2. Verify text readability
3. Test on different page types (events, calendar, speakers)
4. Ensure mobile responsiveness is maintained

## Troubleshooting

### Color Not Updating
1. Clear your browser cache
2. Check if the color was saved in the admin settings
3. Verify the CSS is being loaded (check browser developer tools)

### Color Looks Wrong
1. Try a different color with better contrast
2. Check if the color format is correct (should be hex format)
3. Test on different browsers

## Future Enhancements
Potential future improvements could include:
- Multiple color schemes (light/dark mode)
- Custom color palettes
- Per-page color customization
- Advanced color picker with predefined themes 