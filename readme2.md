# Flip Book Plugin Documentation

**Author:** Aiush Sharma  
**Library:** [DearFlip jQuery Flipbook](https://github.com/dearhive/dearflip-jquery-flipbook)

This plugin allows you to easily embed interactive 3D Flip Books into your WordPress site. You can now manage your flip books directly from the admin panel!

## New: Managed Flip Books (Recommended)

1.  Go to the **Flip Book** menu in your WordPress dashboard.
2.  Enter a **Book Name (ID)** (e.g., `my_album`).
3.  Select the **Type** (PDF or Image Gallery).
4.  Use the **"Select from Media Gallery"** button to easily add images or PDFs.
5.  Click **"Save Book"**.
6.  Use the shortcode: `[flipbook book="my_album"]`

## Shortcode Usage

### 1. Using a Managed Book
```shortcode
[flipbook book="your_book_id"]
```

### 2. PDF Flip Book (Inline)
```shortcode
[flipbook type="pdf" source="http://yourdomain.com/path/to/your.pdf"]
```

### 3. Image Gallery Flip Book (Inline)
```shortcode
[flipbook type="image" source="123,454,789"]
```
*Note: You can use Image IDs or full URLs.*

## Shortcode Attributes

| Attribute | Default | Description |
|-----------|---------|-------------|
| `book`    | (empty) | The ID of a book saved in the admin panel. |
| `type`    | `pdf`   | Type of flipbook: `pdf` or `image`. (Overridden by `book` attribute) |
| `source`  | (empty) | URL for PDF or comma-separated IDs/URLs for images. (Overridden by `book` attribute) |
| `id`      | random  | CSS ID for the container. |
| `webgl`   | `true`  | Enable or disable WebGL 3D effects. |

## Implementation Details

- **Admin Media Integration:** Uses the native WordPress Media Uploader to select image sources.
- **Custom Images:** You can manually paste any image URL into the "Source" field in the admin panel.
- **Shortcode Logic:** If a `book` attribute is provided, the plugin fetches the saved configuration from the database.

---
*Created by Aiush Sharma*
