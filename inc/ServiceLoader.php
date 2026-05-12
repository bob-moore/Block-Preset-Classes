<?php
/**
 * Plugin service loader class.
 *
 * PHP Version 8.2
 *
 * @package    Bmd\BlockPresetClasses
 * @author     Bob Moore <bob@bobmoore.dev>
 * @license    GPL-2.0+ <http://www.gnu.org/licenses/gpl-2.0.txt>
 * @link       https://www.bobmoore.dev
 * @since      1.0.0
 */

namespace Bmd\BlockPresetClasses;

use Bmd\BlockPresetClasses\Bmd\GithubWpUpdater;

/**
 * Service loader/locator class for the plugin.
 */
class ServiceLoader
{
	/**
	 * Main plugin service instance.
	 *
	 * @var Plugin|null
	 */
	protected static ?Plugin $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$plugin_file = dirname( __DIR__ ) . '/plugin.php';

		if ( null === self::$instance ) {
			self::$instance = new Plugin(
				plugin_dir_url( $plugin_file ),
				plugin_dir_path( $plugin_file )
			);

			self::$instance->mount();

			if ( $this->shouldLoadDemo() ) {
				$demo = new Demo(
					plugin_dir_url( $plugin_file ),
					plugin_dir_path( $plugin_file )
				);

				$demo->mount();
			}

			if ( class_exists( GithubWpUpdater::class ) ) {
				$updater = new GithubWpUpdater(
					$plugin_file,
					[
						'github.user'   => 'bob-moore',
						'github.repo'   => 'Block-Preset-Classes',
						'github.branch' => 'main',
					]
				);

				$updater->mount();
			}
		}
	}

	/**
	 * Get the initialized plugin service.
	 *
	 * @return Plugin|null
	 */
	public static function getInstance(): ?Plugin
	{
		return self::$instance;
	}

	/**
	 * Determine whether demo presets should be loaded.
	 *
	 * @return bool
	 */
	protected function shouldLoadDemo(): bool
	{
		return 'local' === wp_get_environment_type()
			|| ( defined( 'IS_PLAYGROUND' ) && (bool) constant( 'IS_PLAYGROUND' ) );
	}
}
