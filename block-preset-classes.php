<?php
/**
 * Plugin Name:       Block Preset Classes
 * Plugin URI:        https://github.com/bob-moore/Block-Preset-Classes
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Adds configurable preset classes to Gutenberg blocks.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Tested up to:      6.7
 * Requires PHP:      8.2
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-preset-classes
 *
 * @package           block-preset-classes
 */

use Bmd\BlockPresetClasses;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * All plugin functionality is contained in this class so it can be consumed
 * through Composer without automatically registering WordPress hooks.
 */
$plugin = new BlockPresetClasses();

add_action( 'enqueue_block_editor_assets', [ $plugin, 'enqueueEditorScript' ] );
add_action( 'rest_api_init', [ $plugin, 'registerRestRoute' ] );
