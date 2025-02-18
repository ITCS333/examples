# Href Attribute
Page location is [here](href.html).

1. **External Website Link**
   ```html
   <a id="top" href="https://www.uob.edu.bh" target="_blank">Visit UOB Website</a>
   ```
   - `href="https://..."` - Full URL to external website
   - `target="_blank"` - Opens link in new tab/window
   - `id="top"` - Creates a bookmark that can be linked to

2. **Internal Page Link**
   ```html
   <a href="index.html">Riffa Page</a>
   ```
   - Links to another page on same website
   - Uses relative path (like we discussed with images)

3. **Email Link**
   ```html
   <a href="mailto:asubah@uob.edu.bh?subject=Hello&body=Hi%20there">Send Email</a>
   ```
   - `mailto:` - Opens default email client
   - `subject=` - Pre-fills subject line
   - `body=` - Pre-fills email body
   - `%20` - Represents a space (URL encoding)

4. **Phone Link**
   ```html
   <a href="tel:+97317437509">Call Us</a>
   ```
   - `tel:` - Opens phone app on mobile devices
   - Include country code (+973)

5. **SMS Link**
   ```html
   <a href="sms:+97317437509?body=Hi%20there">Send SMS</a>
   ```
   - `sms:` - Opens messaging app
   - Can include pre-written message in `body`

6. **Skype Link**
   ```html
   <a href="skype:asubah?call">Call on Skype</a>
   ```
   - `skype:` - Opens Skype app
   - `?call` - Initiates call

7. **FaceTime Link**
   ```html
   <a href="facetime:user@example.com">FaceTime Call</a>
   ```
   - `facetime:` - Opens FaceTime on Apple devices

8. **Page Section Link (Anchor)**
   ```html
   <a href="#top">Go to Top</a>
   ```
   - `#` followed by element's ID
   - Links to element with matching `id` attribute
   - Scrolls to that section of the page

Key Points:
- Links can do more than just navigate to websites
- Different protocols (`mailto:`, `tel:`, etc.) trigger different actions
- URL parameters use `?` for first parameter, `&` for additional ones
- Spaces and special characters need URL encoding
- Section links (anchors) are great for long pages
