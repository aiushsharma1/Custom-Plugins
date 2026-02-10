<?php
/**
 * Plugin Name: Flip Book
 * Description: A WordPress plugin to create interactive flip books using DearFlip jQuery library.
 * Version: 1.1.0
 * Author: Aiush Sharma
 * License: GPL2
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Frontend DearFlip Scripts and Styles
 */
function df_enqueue_assets()
{
    // DearFlip CSS
    wp_enqueue_style('dearflip-css', 'https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/css/dflip.min.css', array(), '1.7.3');

    // DearFlip JS (requires jQuery)
    wp_enqueue_script('dearflip-js', 'https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/js/dflip.min.js', array('jquery'), '1.7.3', true);
}
add_action('wp_enqueue_scripts', 'df_enqueue_assets');

/**
 * Enqueue Admin Assets for Media Uploader
 */
function df_admin_enqueue_assets($hook)
{
    if ('toplevel_page_df-settings' !== $hook) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('df-admin-js', plugins_url('assets/js/admin.js', __FILE__), array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'df_admin_enqueue_assets');

/**
 * Handle Shortcode [flipbook]
 */
function df_flipbook_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
        'type' => 'pdf', // pdf or image
        'source' => '', // URL for PDF or comma-separated IDs/URLs for images
        'id' => '', // Custom ID
        'book' => '', // Name of a saved book config
    ),
        $atts,
        'flipbook'
    );

    $id = !empty($atts['id']) ? esc_attr($atts['id']) : 'book_' . uniqid();
    $sources = array();
    $webgl = 'true';

    // Handle Saved Book Config
    if (!empty($atts['book'])) {
        $configs = get_option('df_flipbooks', array());
        if (isset($configs[$atts['book']])) {
            $config = $configs[$atts['book']];
            $atts['type'] = $config['type'];
            $source_val = $config['source'];

            if ($config['type'] === 'image') {
                $sources = array_map('trim', explode(',', $source_val));
            }
            else {
                $sources = array($source_val);
            }
        }
    }
    else if (!empty($atts['source'])) {
        // Handle Inline Source
        if ($atts['type'] === 'image') {
            $ids = explode(',', $atts['source']);
            foreach ($ids as $img_id) {
                $url = is_numeric(trim($img_id)) ? wp_get_attachment_url(trim($img_id)) : trim($img_id);
                if ($url) {
                    $sources[] = $url;
                }
            }
        }
        else {
            $sources = array($atts['source']);
        }
    }

    if (empty($sources)) {
        return '<p>Flip Book: No source provided.</p>';
    }

    $source_json = ($atts['type'] === 'image') ? json_encode($sources) : '"' . esc_url($sources[0]) . '"';

    $output = '<div class="_df_book" id="' . $id . '"></div>';
    $output .= '<script type="text/javascript">
		var option_' . $id . ' = {
			source: ' . $source_json . ',
			webgl: ' . $webgl . '
		};
	</script>';

    return $output;
}
add_shortcode('flipbook', 'df_flipbook_shortcode');

/**
 * Add Admin Menu
 */
function df_add_admin_menu()
{
    add_menu_page('Flip Book', 'Flip Book', 'manage_options', 'df-settings', 'df_settings_page', 'dashicons-book-alt');
}
add_action('admin_menu', 'df_add_admin_menu');

/**
 * Settings Page Content
 */
function df_settings_page()
{
    // Handle Deletion
    if (isset($_GET['delete']) && check_admin_referer('df_delete_book')) {
        $configs = get_option('df_flipbooks', array());
        unset($configs[$_GET['delete']]);
        update_option('df_flipbooks', $configs);
        echo '<div class="updated"><p>Book deleted.</p></div>';
    }

    // Handle Saving
    if (isset($_POST['df_save_book']) && check_admin_referer('df_save_book_action')) {
        $name = sanitize_title($_POST['book_name']);
        $type = sanitize_text_field($_POST['book_type']);
        $source = sanitize_textarea_field($_POST['book_source']);

        if (!empty($name)) {
            $configs = get_option('df_flipbooks', array());
            $configs[$name] = array(
                'type' => $type,
                'source' => $source,
            );
            update_option('df_flipbooks', $configs);
            echo '<div class="updated"><p>Book "' . esc_html($name) . '" saved.</p></div>';
        }
    }

    $configs = get_option('df_flipbooks', array());
?>
	<div class="wrap">
		<h1>Flip Book Management</h1>
		
		<div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-top: 20px;">
			<h2>Create New Flip Book</h2>
			<form method="post" action="">
				<?php wp_nonce_field('df_save_book_action'); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="book_name">Book Name (ID)</label></th>
						<td><input name="book_name" type="text" id="book_name" value="" class="regular-text" required placeholder="e.g. catalog2024"></td>
					</tr>
					<tr>
						<th scope="row"><label for="book_type">Type</label></th>
						<td>
							<select name="book_type" id="book_type">
								<option value="pdf">PDF</option>
								<option value="image">Image Gallery</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="book_source">Source</label></th>
						<td>
							<textarea name="book_source" id="book_source" rows="5" class="large-text" placeholder="URL for PDF or Comma-separated Image URLs/IDs for Gallery"></textarea>
							<p class="description">For Images, you can click "Select from Media" to add URLs.</p>
							<button type="button" class="button" id="df_select_media">Select from Media Gallery</button>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="df_save_book" class="button button-primary" value="Save Book"></p>
			</form>
		</div>

		<h2 style="margin-top: 40px;">Existing Flip Books</h2>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Shortcode</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($configs)): ?>
					<tr><td colspan="4">No books created yet.</td></tr>
				<?php
    else: ?>
					<?php foreach ($configs as $name => $data): ?>
						<tr>
							<td><strong><?php echo esc_html($name); ?></strong></td>
							<td><?php echo esc_html($data['type']); ?></td>
							<td><code>[flipbook book="<?php echo esc_attr($name); ?>"]</code></td>
							<td>
								<a href="<?php echo wp_nonce_url(admin_url('admin.php?page=df-settings&delete=' . $name), 'df_delete_book'); ?>" class="text-danger" style="color:red;" onclick="return confirm('Are you sure?')">Delete</a>
							</td>
						</tr>
					<?php
        endforeach; ?>
				<?php
    endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
