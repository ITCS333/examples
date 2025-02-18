# Relative Paths
Page location is [here](index.html).

Relative paths are used to link files in the same directory or in subdirectories. They are shorter and easier to maintain than absolute paths.

1. **Same Directory** (`photo.jpg`)
   ```
   📁 current-folder
   ├── 📄 index.html
   └── 📄 photo.jpg
   ```
   - Just use the filename when the image is in the same folder as your HTML file

2. **Child Directory** (`images/photo.jpg`)
   ```
   📁 current-folder
   ├── 📄 index.html
   └── 📁 images
       └── 📄 photo.jpg
   ```
   - Use foldername/filename to access files in subdirectories

3. **Nested Directories** (`images/2024/january/photo.jpg`)
   ```
   📁 current-folder
   ├── 📄 index.html
   └── 📁 images
       └── 📁 2024
           └── 📁 january
               └── 📄 photo.jpg
   ```
   - Chain directories with forward slashes

4. **Parent Directory** (`../photo.jpg` or `../../photo.jpg`)
```
📁 grandparent
├── 📄 photo2.jpg          <-- ../../photo2.jpg
└── 📁 parent
    ├── 📄 photo1.jpg      <-- ../photo1.jpg
    └── 📁 current-folder
        └── 📄 index.html
```

Think of `..` as an elevator:
- One `..` takes you up one floor (to parent)
- Two `../..` takes you up two floors (to grandparent)
- You can keep adding `../` to go up more levels

For example:
- `../` = up one level
- `../../` = up two levels
- `../../../` = up three levels

5. **Sibling Directory** (`../other-folder/photo.jpg`)
   ```
   📁 root
   ├── 📁 current-folder
   │   └── 📄 index.html
   └── 📁 other-folder
       ├── 📄 photo.jpg
       └── 📁 2024
           └── 📄 photo.jpg
   ```
   - Go up one level (`..`)
   - Then into another folder (`other-folder`)
   - Then to the file (`photo.jpg`)
   - Can also go deeper (`../other-folder/2024/photo.jpg`)

Key Points:
- Forward slash (`/`) goes into folders
- Double dots (`..`) goes up to parent folder
- Paths are like giving directions to find a file
- Paths are case-sensitive on many servers
- Always use forward slashes, even on Windows
