<?php
/**
 * Plugin Name:       Block Preset Classes
 * Plugin URI:        https://github.com/bob-moore/Block-Preset-Classes
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Adds configurable preset classes to Gutenberg blocks.
 * Version:           0.3.3
 * Requires at least: 6.7
 * Tested up to:      7.0
 * Requires PHP:      8.2
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-preset-classes
 *
 * @package           block-preset-classes
 * @author            Bob Moore <bob@bobmoore.dev>
 * @license           GPL-2.0-or-later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @link              https://github.com/bob-moore/Block-Preset-Classes
 */

use Bmd\BlockPresetClasses;
use Bmd\BlockPresetClassesDemo;
use Bmd\BlockPresetClasses\Bmd\GithubWpUpdater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/scoped/autoload.php';

/**
 * Initialize the GitHub release updater for standalone plugin installs.
 *
 * @return void
 */
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

/**
 * Initialize the block preset classes runtime.
 *
 * @return void
 */
function create_block_preset_classes_plugin(): void
{
	$plugin = new BlockPresetClasses(
		plugin_dir_url( __FILE__ ),
		plugin_dir_path( __FILE__ )
	);

	$plugin->mount();
}

/**
 * Initialize Playground/local demo presets and styles.
 *
 * @return void
 */
function create_block_preset_classes_demo(): void
{
	$demo = new BlockPresetClassesDemo(
		plugin_dir_url( __FILE__ ),
		plugin_dir_path( __FILE__ )
	);

	$demo->mount();
}

/**
 * Determine whether demo presets should be loaded.
 *
 * @return bool
 */
function should_load_block_preset_classes_demo(): bool
{
	return 'local' === wp_get_environment_type()
		|| ( defined( 'IS_PLAYGROUND' ) && IS_PLAYGROUND );
}

initialize_block_preset_classes_updater();
create_block_preset_classes_plugin();

if ( should_load_block_preset_classes_demo() ) {
	create_block_preset_classes_demo();
}
