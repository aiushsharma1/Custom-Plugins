# My Slider WordPress Plugin

A premium, smooth, and responsive image slider for WordPress built with Slick Slider.

## 🚀 Features
- **Smart Transitions:** Automatically switches from automatic to manual mode when a user swipes or clicks dots.
- **Premium Aesthetics:** Sleek "dash" style pagination and smooth horizontal sliding transitions.
- **Responsive Design:** Optimized for mobile with high-sensitivity touch support.
- **Flexible Image Sources:** Fetch images from the WordPress Media Library or create custom slides.
- **Clean Architecture:** Refactored with separate CSS and JS files for better performance and maintainability.
- **Built-in Tutorial:** Helpful admin notices to guide you immediately after activation.

## 🛠 Installation
1. Upload the `day02` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Follow the **Activation Tutorial** that appears in your dashboard!

## 📖 How to Use

### 1. Adding Slides
- **Method A (Custom Slides):** Go to `My Slider > Add New`. Upload a **Featured Image** and publish.
- **Method B (Media Library):** The plugin can also pull images directly from your existing Media Library.

### 2. Displaying the Slider
Add the shortcode anywhere in your posts or pages:

- `[myslider]` - Displays random images from your Media Library (Default).
- `[myslider source="custom"]` - Displays only your custom created slides.
- `[myslider source="media_library"]` - Explicitly targets Media Library images.

## 📂 File Structure
```text
day02/
├── assets/
│   ├── css/
│   │   └── myslider.css  # Custom slider styling
│   └── js/
│       └── myslider.js   # Slick initialization & manual logic
├── index.php             # Main plugin file & PHP logic
└── README.md             # This documentation
```

## ⚙️ Technical Details
- **Slick Slider Version:** 1.8.1
- **Transition Speed:** 600ms
- **Autoplay Speed:** 3000ms
- **Manual Override:** Autoplay pauses on `swipe`, `mousedown`, and `dot-click` events.
