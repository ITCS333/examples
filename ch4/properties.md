# CSS Properties
Page location is [here](properties.html).
CSS location is [here](properties.css).

## 1. Base Structure & Reset
```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
```
This universal selector (*) implements a CSS reset which:
- Removes default margins and padding
- Uses `box-sizing: border-box` to include padding and border in element's total width/height calculations

## 2. Body Styling
```css
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f5f5;
}
```
- Uses modern system fonts with fallbacks
- Sets comfortable reading line height
- Uses dark gray text (#333) for good contrast
- Light gray background for subtle contrast

## 3. Container Layout
```css
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
```
Creates a responsive container that:
- Centers content with `margin: 0 auto`
- Limits width to 1200px for readability
- Adds consistent padding around content

## 4. Typography Demonstration
```css
.font-section {
    font-family: Georgia, serif;
    font-size: 18px;
    font-style: italic;
    font-weight: 700;
}
```
Shows typography control with:
- Serif font for formal/elegant appearance
- 18px base size for readability
- Italic styling for emphasis
- Bold weight (700) for strong visual impact

## 5. Text Formatting
```css
.text-section {
    letter-spacing: 1px;
    line-height: 1.8;
    text-align: justify;
    text-decoration: underline;
    text-indent: 2em;
}
```
Demonstrates text manipulation:
- Increased letter spacing for clarity
- Generous line height for readability
- Justified alignment for formal appearance
- First-line indentation using `text-indent`

## 6. Advanced Background Effects
```css
.color-bg-section {
    background-color: #ffffff;
    background-image: linear-gradient(45deg, #f0f0f0 25%, transparent 25%);
    background-position: 0 0;
    background-repeat: repeat;
    background-size: 20px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
```
Creates sophisticated background effects:
- Base white background
- Diagonal striped pattern using gradients
- Precise pattern sizing and positioning
- Subtle shadow for depth

## 7. Border Styling Techniques
```css
.border-section {
    border: 2px solid #3498db;
    border-radius: 10px;
}

.border-variants {
    display: flex;
    gap: 20px;
}
```
Shows different border applications:
- Solid borders with specific width
- Rounded corners using border-radius
- Flex layout for border style comparison
- Modern gap property for spacing

## 8. Spacing Control
```css
.spacing-section {
    background: white;
    margin: 40px 0;
}

.spacing-box {
    background: #ecf0f1;
    padding: 20px;
    margin: 10px;
}
```
Demonstrates spacing techniques:
- Vertical margins for section separation
- Internal padding for content breathing room
- Combined margin and padding usage

## 9. Size Management
```css
.sizing-container {
    width: 80%;
    max-width: 600px;
    min-width: 500px;
}

.sizing-content {
    max-height: 150px;
    overflow: auto;
}
```
Shows size control methods:
- Responsive percentage-based widths
- Maximum and minimum width constraints
- Height limitations with overflow handling
- Scrollable content areas

## 10. Advanced Layout Techniques
```css
.layout-section {
    position: relative;
    min-height: 300px;
}

.position-absolute {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 2;
}
```
Demonstrates positioning:
- Relative positioning for context
- Absolute positioning for precise placement
- Z-index for stacking control
- Minimum height requirements

## 11. Interactive Effects
```css
.effect-box {
    transition: all 0.3s ease;
    transform: scale(1);
    cursor: pointer;
}

.effect-box:hover {
    transform: scale(1.05);
    background: #2980b9;
}
```
Shows interactive elements:
- Smooth transitions
- Scale transform on hover
- Cursor changes
- Background color transitions

## 12. Z-Index and Layering
```css
.z-index-demo {
    position: relative;
    height: 150px;
}

.z-box {
    position: absolute;
    width: 100px;
    height: 100px;
}
```
Demonstrates layer management:
- Stacking context creation
- Absolute positioning for overlap
- Semi-transparent colors
- Z-index hierarchy
