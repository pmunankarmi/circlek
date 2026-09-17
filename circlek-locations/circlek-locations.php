<?php
/**
 * Plugin Name: Circle K Locations
 * Description: Dynamic, searchable Circle K store directory with editable location custom fields.
 * Version: 1.4.0
 * Author: Circle K
 * Update URI: https://github.com/pmunankarmi/circlek
 * Text Domain: circlek-locations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CKL_VERSION', '1.4.0' );
define( 'CKL_FILE', __FILE__ );
define( 'CKL_DIR', plugin_dir_path( __FILE__ ) );
define( 'CKL_URL', plugin_dir_url( __FILE__ ) );

require_once CKL_DIR . 'includes/class-circlek-locations.php';
require_once CKL_DIR . 'includes/class-circlek-github-updater.php';

CircleK_Locations::instance();
new CircleK_GitHub_Updater( CKL_FILE, CKL_VERSION );

register_activation_hook( __FILE__, array( 'CircleK_Locations', 'activate' ) );
