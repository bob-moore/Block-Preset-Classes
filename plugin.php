<?php
/**
 * Plugin bootstrap.
 *
 * @wordpress-plugin
 * Plugin Name: Block Preset Classes
 * Plugin URI:  https://github.com/bob-moore/Block-Preset-Classes
 * Description: Adds configurable preset classes to Gutenberg blocks.
 * Version:     0.3.4
 * Author:      Bob Moore
 * Author URI:  https://www.bobmoore.dev
 * Requires at least: 6.7
 * Tested up to: 7.0
 * Requires PHP: 8.2
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: block-preset-classes
 *
 * @package Bmd\BlockPresetClasses
 */

namespace Bmd\BlockPresetClasses;

use Bmd\BlockPresetClasses\Bmd\GithubWpUpdater;

defined( 'ABSPATH' ) || exit;

/**
 * Load dependencies and mount the plugin after WordPress has loaded plugins.
 *
 * @return void
 */
function load_plugin(): void
{
	try {
		$scoped_autoload   = plugin_dir_path( __FILE__ ) . 'vendor/scoped/autoload.php';
		$scoper_autoload   = plugin_dir_path( __FILE__ ) . 'vendor/scoped/scoper-autoload.php';
		$composer_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

		if ( is_file( $scoped_autoload ) && is_file( $scoper_autoload ) ) {
			require_once $scoped_autoload;
			require_once $scoper_autoload;
		}

		if ( ! is_file( $composer_autoload ) ) {
			throw new \RuntimeException( 'Block Preset Classes dependencies are missing. Run composer install before activating the plugin.' );
		}

		require_once $composer_autoload;

		$plugin = new Main(
			[
				'package' => 'block_preset_classes',
				'version' => '0.3.4',
				'path'    => plugin_dir_path( __FILE__ ),
				'url'     => plugin_dir_url( __FILE__ ),
			]
		);
		$plugin->mount();

	} catch ( \Throwable $e ) {
		error_log( $e->getMessage() );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );

/**
 * Initialize updates from GitHub.
 *
 * @return void
 */
function update_from_github(): void
{
	try {
		if ( ! class_exists( GithubWpUpdater::class ) ) {
			return;
		}

		$updater = new GithubWpUpdater(
			__FILE__,
			[
				'github.user'    => 'bob-moore',
				'github.repo'    => 'Block-Preset-Classes',
				'github.branch'  => 'main',
				'plugin.banners' => [
					'low'  => plugin_dir_url( __FILE__ ) . 'assets/banner-772x250.jpg',
					'high' => plugin_dir_url( __FILE__ ) . 'assets/banner-1544x500.jpg',
				],
				'plugin.icons'   => [
					'default' => plugin_dir_url( __FILE__ ) . 'assets/icon.jpg',
				],
			]
		);
		$updater->mount();
	} catch ( \Error $e ) {
		error_log( $e->getMessage() );
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\update_from_github' );
