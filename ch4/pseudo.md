# CSS Pseudo-Classes and Pseudo-Elements
Page location is [here](pseudo.html).
CSS location is [here](pseudo.css).

## Base Styles
```css
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
```
- Sets up basic page layout
- Centers content with max-width
- Establishes readable typography

## Link States (Pseudo-classes)

### 1. Default Link State (`:link`)
```css
a:link {
    color: #2196f3;
    text-decoration: none;
}
```
- Styles unvisited links
- Uses blue color
- Removes default underline

### 2. Visited Links (`:visited`)
```css
a:visited {
    color: #e9cb44;
}
```
- Changes color for visited links
- Uses yellow/gold color for distinction

### 3. Hover State (`:hover`)
```css
a:hover {
    text-decoration: underline;
    color: #1976d2;
}
```
- Activates when user hovers over link
- Adds underline
- Darkens the blue color

### 4. Active State (`:active`)
```css
a:active {
    color: #f44336;
}
```
- Applies while link is being clicked
- Changes to red color
- Provides immediate feedback

## List Styling (Structural Pseudo-classes)

### Basic List Structure
```css
.custom-list {
    list-style: none;
    padding: 0;
}

.custom-list li {
    padding: 10px;
    background-color: #f5f5f5;
    margin: 5px 0;
}
```
- Removes default list markers
- Adds spacing and background

### Special List Items

1. **First Child (`:first-child`)**
```css
.custom-list li:first-child {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}
```
- Styles first item differently
- Uses light blue background
- Adds blue left border

2. **Last Child (`:last-child`)**
```css
.custom-list li:last-child {
    background-color: #fce4ec;
    border-left: 4px solid #e91e63;
}
```
- Styles last item
- Uses pink background
- Adds pink left border

3. **Nth Child (`:nth-child()`)**
```css
.custom-list li:nth-child(2) {
    background-color: #f3e5f5;
    border-left: 4px solid #9c27b0;
}
```
- Targets specific items by position
- Uses purple theme
- Demonstrates position-based styling

## Text Enhancements (Pseudo-elements)

### Drop Cap (`:first-letter`)
```css
.drop-cap::first-letter {
    font-size: 3em;
    float: left;
    padding-right: 8px;
    color: #2196f3;
    font-weight: bold;
}
```
- Creates decorative first letter
- Enlarges first character
- Floats it for text wrap
- Adds emphasis with color and weight

### First Line (`:first-line`)
```css
.first-line::first-line {
    font-weight: bold;
    color: #1976d2;
}
```
- Styles the first line of text
- Adds bold weight
- Uses darker blue color

## Helper Styles
```css
.explanation {
    color: #666;
    font-style: italic;
    margin-top: 10px;
    font-size: 0.9em;
}
```
- Provides context for demonstrations
- Uses muted styling
- Slightly smaller text
