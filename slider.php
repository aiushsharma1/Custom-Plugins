<?php
/**
 * Plugin Name: My Slider
 * Description: A simple slider plugin using Slick Slider.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// 1. Register Custom Post Type for Slides
function myslider_register_cpt()
{
    $labels = array(
        'name' => _x('Slides', 'Post Type General Name', 'myslider'),
        'singular_name' => _x('Slide', 'Post Type Singular Name', 'myslider'),
        'menu_name' => __('My Slider', 'myslider'),
        'name_admin_bar' => __('Slide', 'myslider'),
        'archives' => __('Slide Archives', 'myslider'),
        'attributes' => __('Slide Attributes', 'myslider'),
        'parent_item_colon' => __('Parent Slide:', 'myslider'),
        'all_items' => __('All Slides', 'myslider'),
        'add_new_item' => __('Add New Slide', 'myslider'),
        'add_new' => __('Add New', 'myslider'),
        'new_item' => __('New Slide', 'myslider'),
        'edit_item' => __('Edit Slide', 'myslider'),
        'update_item' => __('Update Slide', 'myslider'),
        'view_item' => __('View Slide', 'myslider'),
        'view_items' => __('View Slides', 'myslider'),
        'search_items' => __('Search Slide', 'myslider'),
        'not_found' => __('Not found', 'myslider'),
        'not_found_in_trash' => __('Not found in Trash', 'myslider'),
        'featured_image' => __('Slide Image', 'myslider'),
        'set_featured_image' => __('Set slide image', 'myslider'),
        'remove_featured_image' => __('Remove slide image', 'myslider'),
        'use_featured_image' => __('Use as slide image', 'myslider'),
        'insert_into_item' => __('Insert into slide', 'myslider'),
        'uploaded_to_this_item' => __('Uploaded to this slide', 'myslider'),
        'items_list' => __('Slides list', 'myslider'),
        'items_list_navigation' => __('Slides list navigation', 'myslider'),
        'filter_items_list' => __('Filter slides list', 'myslider'),
    );
    $args = array(
        'label' => __('Slide', 'myslider'),
        'description' => __('Slides for My Slider', 'myslider'),
        'labels' => $labels,
        'supports' => array('title', 'thumbnail'), // Title for admin identification, Thumbnail for the slide image
        'hierarchical' => false,
        'public' => false, // Not public on frontend as single pages
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-images-alt2',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
    );
    register_post_type('myslider_slide', $args);
}
add_action('init', 'myslider_register_cpt');

// 2. Enqueue Assets (Slick Slider & Custom)
function myslider_enqueue_scripts()
{
    // Slick CSS
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css', array(), '1.8.1');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css', array('slick-css'), '1.8.1');

    // Custom CSS for My Slider
    wp_enqueue_style('myslider-custom-css', plugin_dir_url(__FILE__) . 'assets/css/myslider.css', array('slick-theme-css'), '1.0');

    // Slick JS
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);

    // Custom JS for My Slider
    wp_enqueue_script('myslider-custom-js', plugin_dir_url(__FILE__) . 'assets/js/myslider.js', array('slick-js'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'myslider_enqueue_scripts');

// 3. Shortcode [myslider]
function myslider_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'source' => 'media_library', // Options: 'media_library', 'custom', 'both'
    ), $atts, 'myslider');

    $args = array(
        'posts_per_page' => -1,
        'orderby' => 'rand',
    );

    if ($atts['source'] === 'media_library') {
        $args['post_type'] = 'attachment';
        $args['post_status'] = 'inherit';
        $args['post_mime_type'] = 'image';
    }
    elseif ($atts['source'] === 'custom') {
        $args['post_type'] = 'myslider_slide';
        $args['post_status'] = 'publish';
    }
    else {
        // Fallback or explicit 'both' (complex query, simplification: just fetch both)
        $args['post_type'] = array('attachment', 'myslider_slide');
        $args['post_status'] = array('inherit', 'publish');
    }

    $query = new WP_Query($args);

    // Debugging: Uncomment to see what's happening if no images appear
    // echo '<pre>'; print_r($query); echo '</pre>';

    if ($query->have_posts()) {
        $output = '<div class="myslider-wrapper"><div class="myslider">';
        while ($query->have_posts()) {
            $query->the_post();
            $image_url = '';

            if (get_post_type() === 'attachment') {
                // For attachments, the ID is the attachment ID
                $image_url = wp_get_attachment_image_url(get_the_ID(), 'full');
            }
            else {
                // For custom slides, get the featured image
                if (has_post_thumbnail()) {
                    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                }
            }

            // Only output if we found an image URL
            if ($image_url) {
                $output .= '<div class="myslider-slide">';
                $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '">';
                $output .= '</div>';
            }
        }
        $output .= '</div></div>';
        wp_reset_postdata();
        return $output;
    }
    else {
        return '<p>No slides found.</p>';
    }
}
add_shortcode('myslider', 'myslider_shortcode');

// 4. Activation Tutorial Notice
function myslider_activate()
{
    set_transient('myslider_show_notice', true, 5);
}
register_activation_hook(__FILE__, 'myslider_activate');

function myslider_admin_notice()
{
    if (get_transient('myslider_show_notice')) {
        delete_transient('myslider_show_notice');
?>
        <div class="notice notice-info is-dismissible" style="border-left-color: #2196f3;">
            <p><strong>🚀 Welcome to My Slider!</strong> Here is a quick guide to get you started:</p>
            <ol>
                <li><strong>Add Slides:</strong> Go to <a href="<?php echo admin_url('edit.php?post_type=myslider_slide'); ?>">My Slider > Add New</a> to upload featured images for your slides.</li>
                <li><strong>Shortcode:</strong> Use <code>[myslider]</code> on any page or post.</li>
                <li><strong>Source Options:</strong> 
                    <ul>
                        <li><code>[myslider source="media_library"]</code> - Shows random images from your Media Library (Default).</li>
                        <li><code>[myslider source="custom"]</code> - Shows only the slides you added in the "My Slider" menu.</li>
                    </ul>
                </li>
                <li><strong>Interactive:</strong> The slider pauses automatically when you interact with it (swipe/dots).</li>
            </ol>
            <p>Need help? Just start adding slides!</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'myslider_admin_notice');

// Documentation (Comment at the end)
/*
 * Documentation:
 * 1. Install and activate the plugin.
 * 2. Go to "My Slider" in the admin menu.
 * 3. Add new slides by clicking "Add New".
 *    - Upload an image to the "Featured Image" section.
 *    - Publish.
 * 4. Create a page or post and add the shortcode `[myslider]`.
 * 5. Publish the page/post to see the slider.
 */
