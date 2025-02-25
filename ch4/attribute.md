# CSS Attribute Selectors
Page location is [here](attribute.html).
CSS location is [here](attribute.css).


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
- Sets a readable font
- Creates a centered container with max-width
- Adds comfortable line spacing

## Attribute Selector Types

### 1. Simple Attribute Selector `[attribute]`
```css
[title] {
    border-bottom: 2px dotted #007bff;
    cursor: help;
}
```
- Targets any element with a `title` attribute
- Creates a dotted underline
- Shows help cursor on hover
- Useful for indicating tooltips

### 2. Exact Match `[attribute="value"]`
```css
a[title="posts from this country"] {
    background-color: #ffeb3b;
    padding: 2px 5px;
    text-decoration: none;
    color: #333;
}
```
- Matches elements where the attribute exactly equals the specified value
- Applies yellow background
- Removes default link underline

### 3. Space-Separated List `[attribute~="value"]`
```css
[title~="Countries"] {
    border: 2px solid #4caf50;
    padding: 5px;
    display: inline-block;
}
```
- Matches elements where the attribute contains the word in a space-separated list
- Adds green border
- Makes element inline-block

### 4. Begins With `[attribute^="value"]`
```css
a[href^="mailto"] {
    background-color: #ff9800;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
}
```
- Targets elements where the attribute starts with specified value
- Styles email links with orange background
- Adds rounded corners

### 5. Contains Substring `[attribute*="value"]`
```css
img[src*="Flag"] {
    border: 3px solid #e91e63;
    padding: 5px;
}
```
- Matches elements where the attribute contains the specified substring
- Adds pink border to images with "Flag" in their source URL

### 6. Ends With `[attribute$="value"]`
```css
a[href$=".pdf"] {
    background-color: #f44336;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
}
```
- Targets elements where the attribute ends with specified value
- Styles PDF links with red background
- Uses flex for alignment

### PDF Icon Enhancement
```css
a[href$=".pdf"]::after {
    content: " 📄";
    margin-left: 5px;
}
```
- Adds PDF icon after PDF links
- Uses Unicode character for the icon
- Adds spacing between text and icon

## Section Styling
```css
section {
    margin: 30px 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}
```
- Creates separated sections
- Adds subtle border
- Includes rounded corners

## Helper Styles
```css
.explanation {
    color: #666;
    font-style: italic;
    margin-top: 10px;
}
```
- Styles explanatory text
- Uses muted color
- Adds italic style for emphasis
