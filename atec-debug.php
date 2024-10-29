<?php
if (!defined( 'ABSPATH' )) { exit; }
  /**
  * Plugin Name:  atec Debug
  * Plugin URI: https://atecplugins.com/
  * Description: Essential toolbox to debug a WordPress installation.
  * Version: 1.1.2
  * Requires at least: 5.2
  * Tested up to: 6.6.3
  * Requires PHP: 7.4
  * Author: Chris Ahrweiler
  * Author URI: https://atec-systems.com
  * License: GPL2
  * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
  * Text Domain:  atec-debug
  */

if (is_admin()) 
{
	wp_cache_set('atec_wpd_version','1.1.2');
	register_activation_hook( __FILE__, function() { require_once('includes/atec-wpd-activation.php'); });
	require_once('includes/atec-wpd-install.php');
}
?>