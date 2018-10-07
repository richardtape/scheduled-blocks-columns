<?php
/**
 * Scheduled Blocks Columns Add-on
 *
 * @package     scheduled-blocks-columns
 * @author      Richard Tape
 * @copyright   2018 Richard Tape
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:  Scheduled Blocks Columns Blocks Add-On
 * Plugin URI:   https://scheduledblocks.com/add-ons/columns
 * Description:  Schedule when your columns blocks go live. An add-on for Scheduled Blocks.
 * Version:      0.1.0
 * Author:       Richard Tape
 * Requires PHP: 7
 * Author URI:   https://scheduledblocks.com/
 * Text Domain:  scheduled-blocks-columns
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load our required files.
require_once 'lib/class-scheduled-blocks-columns.php';
require_once 'inc/html5-dom-document-php/vendor/autoload.php';

/**
 * Initialize ourselves!
 *
 * @return void
 */
function plugins_loaded__scheduled_blocks_columns_init() {

	$scheduled_blocks_columns = new Scheduled_Blocks_Columns();
	$scheduled_blocks_columns->init();

}// end plugins_loaded__scheduled_blocks_columns_init()

add_action( 'plugins_loaded', 'plugins_loaded__scheduled_blocks_columns_init', 12 );
