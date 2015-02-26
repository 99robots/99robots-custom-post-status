<?php
/*
Plugin Name: Gabfire Custom Post Status Beta
plugin URI:
Description:
version: 0.9.0
Author:
Author URI:
License: GPL2
*/

/**
 * Global Definitions
 */

/* Plugin Name */

if (!defined('GABFIRE_CUSTOM_POST_STATUS_PLUGIN_NAME'))
    define('GABFIRE_CUSTOM_POST_STATUS_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

/* Plugin directory */

if (!defined('GABFIRE_CUSTOM_POST_STATUS_PLUGIN_DIR'))
    define('GABFIRE_CUSTOM_POST_STATUS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . GABFIRE_CUSTOM_POST_STATUS_PLUGIN_NAME);

/* Plugin url */

if (!defined('GABFIRE_CUSTOM_POST_STATUS_PLUGIN_URL'))
    define('GABFIRE_CUSTOM_POST_STATUS_PLUGIN_URL', WP_PLUGIN_URL . '/' . GABFIRE_CUSTOM_POST_STATUS_PLUGIN_NAME);

/* Plugin verison */

if (!defined('GABFIRE_CUSTOM_POST_STATUS_VERSION_NUM'))
    define('GABFIRE_CUSTOM_POST_STATUS_VERSION_NUM', '0.9.0');


/**
 * Activatation / Deactivation
 */

register_activation_hook( __FILE__, array('GabfireCustomPostStatus', 'register_activation'));

/**
 * Hooks / Filter
 */

add_action('init', array('GabfireCustomPostStatus', 'load_textdomain'));
add_action('admin_menu', array('GabfireCustomPostStatus', 'menu_page'));
//add_action('admin_enqueue_scripts', array('GabfireCustomPostStatus', 'post_status_dropdown'));

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", array('GabfireCustomPostStatus', 'plugin_links'));

foreach ( array( 'post', 'post-new' ) as $hook ) {
	add_action( "admin_footer-{$hook}.php", array('GabfireCustomPostStatus', 'extend_submitdiv_post_status' ) );
}

/**
 *  GabfireCustomPostStatus main class
 *
 * @since 1.0.0
 * @using Wordpress 3.8
 */

class GabfireCustomPostStatus {

	/**
	 * text_domain
	 *
	 * (default value: 'gabfire-custom-post-status')
	 *
	 * @var string
	 * @access private
	 * @static
	 */
	private static $text_domain = 'gabfire-custom-post-status';

	/**
	 * prefix
	 *
	 * (default value: 'gabfire_custom_post_status_')
	 *
	 * @var string
	 * @access private
	 * @static
	 */
	private static $prefix = 'gabfire_custom_post_status_';

	/**
	 * settings_page
	 *
	 * (default value: 'gabfire-custom-post-status-admin-menu-settings')
	 *
	 * @var string
	 * @access private
	 * @static
	 */
	private static $settings_page = 'gabfire-custom-post-status-admin-menu-settings';

	/**
	 * default
	 *
	 * @var mixed
	 * @access private
	 * @static
	 */
	private static $default = array();

	/**
	 * Load the text domain
	 *
	 * @since 1.0.0
	 */
	static function load_textdomain() {
		load_plugin_textdomain(self::$text_domain, false, GABFIRE_CUSTOM_POST_STATUS_PLUGIN_DIR . '/languages');

		if (function_exists('is_multisite') && is_multisite()) {
			$settings = get_site_option(self::$prefix . 'settings');
		} else {
			$settings = get_option(self::$prefix . 'settings');
		}

		/* Default values */

		if ($settings === false) {
			$settings = self::$default;
		}

		foreach($settings as $status => $args) {
			register_post_status($status, array(
				'label'                     => __($args['label'], self::$text_domain),
				'public'                    => $args['public'],
				'exclude_from_search'       => $args['exclude_from_search'],
				'show_in_admin_all_list'    => $args['show_in_admin_all_list'],
				'show_in_admin_status_list' => $args['show_in_admin_status_list'],
				'label_count'               => _n_noop($args['label_count'] . ' <span class="count">(%s)</span>', $args['label_count'] . ' <span class="count">(%s)</span>' ),
			) );
		}
	}

	/**
	 * Hooks to 'register_activation_hook'
	 *
	 * @since 1.0.0
	 */
	static function register_activation() {

		/* Check if multisite, if so then save as site option */

		if (function_exists('is_multisite') && is_multisite()) {
			add_site_option(self::$prefix . 'version', GABFIRE_CUSTOM_POST_STATUS_VERSION_NUM);
		} else {
			add_option(self::$prefix . 'version', GABFIRE_CUSTOM_POST_STATUS_VERSION_NUM);
		}
	}

	/**
	 * Hooks to 'plugin_action_links_' filter
	 *
	 * @since 1.0.0
	 */
	static function plugin_links($links) {
		$settings_link = '<a href="options-general.php?page=' . self::$settings_page . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Hooks to 'admin_menu'
	 *
	 * @since 1.0.0
	 */
	static function menu_page() {

	    /* Cast the first sub menu to the top menu */

	    $settings_page_load = add_submenu_page(
	    	'options-general.php', 										// parent slug
	    	__('Gabfire Custom Post Status', self::$text_domain), 						// Page title
	    	__('Gabfire Custom Post Status', self::$text_domain), 						// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('GabfireCustomPostStatus', 'dashboard')	// Callback function
	    );
	    add_action("admin_print_scripts-$settings_page_load", array('GabfireCustomPostStatus', 'include_admin_scripts'));
	}

	/**
	 * Hooks to 'admin_print_scripts-$page'
	 *
	 * @since 1.0.0
	 */
	static function include_admin_scripts() {

		/* CSS */

		wp_register_style(self::$prefix . 'dashboard_css', GABFIRE_CUSTOM_POST_STATUS_PLUGIN_URL . '/css/dashboard.css');
		wp_enqueue_style(self::$prefix . 'dashboard_css');

		wp_register_script(self::$prefix . 'dashboard_js', GABFIRE_CUSTOM_POST_STATUS_PLUGIN_URL . '/js/dashboard.js');
		wp_enqueue_script(self::$prefix . 'dashboard_js');
		wp_localize_script(self::$prefix . 'dashboard_js', 'gabfire_cps', array('prefix' => self::$prefix));
	}

	/**
	 * Displays the HTML for the 'gabfire-custom-post-status-admin-menu-settings' admin page
	 *
	 * @since 1.0.0
	 */
	static function dashboard() {

		if (function_exists('is_multisite') && is_multisite()) {
			$settings = get_site_option(self::$prefix . 'settings');
		} else {
			$settings = get_option(self::$prefix . 'settings');
		}

		/* Default values */

		if ($settings === false) {
			$settings = self::$default;
		}

		if (isset($_GET['type']) && $_GET['type'] == 'dashboard') {

			// Delete Gallery

			if (isset($_GET['action']) && $_GET['action'] == "delete" && check_admin_referer(self::$prefix . 'delete')) {

				if (isset($_GET['status'])) {
					unset($settings[$_GET['status']]);
				}

				if (function_exists("is_multisite") && is_multisite()) {
					update_site_option(self::$prefix . 'settings', $settings);
				}else {
					update_option(self::$prefix . 'settings', $settings);
				}

				?>
				<script type="text/javascript">
					window.location = "<?php echo $_SERVER['PHP_SELF']?>?page=<?php echo self::$settings_page; ?>";
				</script>
				<?php
			}
		}

		if (isset($_GET['type']) && $_GET['type'] == 'add_edit') {

			/* Edit Existing */

			if (isset($_GET['action']) && $_GET['action'] == "edit") {
				if (isset($_GET['status'])) {
					$data = $settings[$_GET['status']];
				}
			} else {
				$data = array();
			}

			/* Save data nd check nonce */

			if (isset($_POST['submit']) && check_admin_referer(self::$prefix . 'admin_settings')) {

				$status_id = strtolower(str_replace(' ','',sanitize_text_field($_POST[self::$prefix . 'status'])));

				if (isset($_GET['action']) && $_GET['action'] == 'add' && !array_key_exists($status_id, $settings)) {

					$settings[$status_id] = array(
						'label' 					=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'label'])),
						'public' 					=> isset($_POST[self::$prefix . 'public']) && $_POST[self::$prefix . 'public'] ? true : false,
						'exclude_from_search' 		=> isset($_POST[self::$prefix . 'exclude-from-search']) && $_POST[self::$prefix . 'exclude-from-search'] ? true : false,
						'show_in_admin_all_list'	=> isset($_POST[self::$prefix . 'show-in-admin-all-list']) && $_POST[self::$prefix . 'show-in-admin-all-list'] ? true : false,
						'show_in_admin_status_list' => isset($_POST[self::$prefix . 'show-in-admin-status-list']) && $_POST[self::$prefix . 'show-in-admin-status-list'] ? true : false,
						'label_count' 				=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'label-count'])),
					);

					if (function_exists("is_multisite") && is_multisite()) {
						update_site_option(self::$prefix . 'settings', $settings);
					}else {
						update_option(self::$prefix . 'settings', $settings);
					}
				} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

					$settings[$status_id] = array(
						'label' 					=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'label'])),
						'public' 					=> isset($_POST[self::$prefix . 'public']) && $_POST[self::$prefix . 'public'] ? true : false,
						'exclude_from_search' 		=> isset($_POST[self::$prefix . 'exclude-from-search']) && $_POST[self::$prefix . 'exclude-from-search'] ? true : false,
						'show_in_admin_all_list'	=> isset($_POST[self::$prefix . 'show-in-admin-all-list']) && $_POST[self::$prefix . 'show-in-admin-all-list'] ? true : false,
						'show_in_admin_status_list' => isset($_POST[self::$prefix . 'show-in-admin-status-list']) && $_POST[self::$prefix . 'show-in-admin-status-list'] ? true : false,
						'label_count' 				=> stripcslashes(sanitize_text_field($_POST[self::$prefix . 'label-count'])),
					);

					if (function_exists("is_multisite") && is_multisite()) {
						update_site_option(self::$prefix . 'settings', $settings);
					}else {
						update_option(self::$prefix . 'settings', $settings);
					}
				}

				/* Go back to the main dashboard */
				?>
				<script type="text/javascript">
					window.location = "<?php echo $_SERVER['PHP_SELF']?>?page=<?php echo self::$settings_page; ?>";
				</script>
				<?php
			}
		}

		require('admin/dashboard.php');
	}

	/**
	 * Adds post status to the "submitdiv" Meta Box and post type WP List Table screens
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function extend_submitdiv_post_status() {
		global $wp_post_statuses, $post, $post_type;

		// Get all non-builtin post status and add them as <option>
		$options = $display = '';
		foreach ( $wp_post_statuses as $status )
		{
			if ( ! $status->_builtin ) {
				// Match against the current posts status
				$selected = selected( $post->post_status, $status->name, false );

				// If we one of our custom post status is selected, remember it
				$selected AND $display = $status->label;

				// Build the options
				$options .= "<option{$selected} value='{$status->name}'>{$status->label}</option>";
			}
		}
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function($)
			{
				<?php
				// Add the selected post status label to the "Status: [Name] (Edit)"
				if ( ! empty( $display ) ) :
				?>
					$( '#post-status-display' ).html( '<?php echo $display; ?>' )
				<?php
				endif;

				// Add the options to the <select> element
				?>
				var select = $( '#post-status-select' ).find( 'select' );
				$( select ).append( "<?php echo $options; ?>" );
			} );
		</script>
		<?php
	}

	/**
	 * Includes file to add posts status to dropdown
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function post_status_dropdown() {

		// Custom javascript to modify the post status dropdown where it shows up
		if (self::is_whitelisted_page() ) {
			wp_enqueue_script(self::$prefix . 'edit_flow-custom_status', GABFIRE_CUSTOM_POST_STATUS_PLUGIN_URL . 'js/post-status-dropdown.js', array( 'jquery','post' ));
		}
	}

	/**
	 * Check whether custom status stuff should be loaded on this page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function is_whitelisted_page() {
		global $pagenow;

		// Only add the script to Edit Post and Edit Page pages -- don't want to bog down the rest of the admin with unnecessary javascript
		return in_array( $pagenow, array( 'post.php', 'edit.php', 'post-new.php', 'page.php', 'edit-pages.php', 'page-new.php' ) );
	}
}

?>