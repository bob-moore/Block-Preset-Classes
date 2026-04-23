<?php
/**
 * Plugin Name:       Block Preset Classes
 * Plugin URI:        https://github.com/bob-moore/Block-Preset-Classes
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Adds configurable preset classes to Gutenberg blocks.
 * Version:           0.2.0
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

function create_block_preset_classes_plugin(): void
{
	$plugin = new BlockPresetClasses(
		plugin_dir_url( __FILE__ ),
		plugin_dir_path( __FILE__ )
	);

	$plugin->mount();
}
create_block_preset_classes_plugin();
