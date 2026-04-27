<?php
/**
 * Plugin Name:       Block Preset Classes
 * Plugin URI:        https://github.com/bob-moore/Block-Preset-Classes
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Adds configurable preset classes to Gutenberg blocks.
 * Version:           0.3.0
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
use Bmd\BlockPresetClassesDemo;
use Bmd\BlockPresetClasses\Bmd\GithubWpUpdater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/scoped/autoload.php';

function initialize_block_preset_classes_updater(): void
{
	$updater = new GithubWpUpdater(
		__FILE__,
		[
			'github.user'   => 'bob-moore',
			'github.repo'   => 'Block-Preset-Classes',
			'github.branch' => 'main',
		]
	);

	$updater->mount();
}

function create_block_preset_classes_plugin(): void
{
	$plugin = new BlockPresetClasses(
		plugin_dir_url( __FILE__ ),
		plugin_dir_path( __FILE__ )
	);

	$plugin->mount();
}

function create_block_preset_classes_demo(): void
{
	if ( ! get_option( 'block_preset_classes_load_demo', false ) ) {
		return;
	}

	$demo = new BlockPresetClassesDemo(
		plugin_dir_url( __FILE__ ),
		plugin_dir_path( __FILE__ )
	);

	$demo->mount();
}

initialize_block_preset_classes_updater();
create_block_preset_classes_plugin();
create_block_preset_classes_demo();
