<?php
/**
 * Hook registrar.
 *
 * @package Bmd\BlockPresetClasses
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/bob-moore/Block-Preset-Classes
 */

namespace Bmd\BlockPresetClasses;

use DI\Attribute\Inject;

/**
 * Registers all WordPress hooks for the plugin.
 */
class Controller extends Module
{
	/**
	 * Register WordPress action hooks.
	 *
	 * @param Providers\Assets  $assets Asset provider.
	 * @param Providers\RestApi $rest   REST API provider.
	 * @param Providers\Demo    $demo   Demo provider.
	 *
	 * @return void
	 */
	#[Inject]
	public function registerActions(
		Providers\Assets $assets,
		Providers\RestApi $rest,
		Providers\Demo $demo,
	): void {
		add_action( 'enqueue_block_editor_assets', [ $assets, 'enqueueEditorAssets' ] );
		add_action( 'rest_api_init', [ $rest, 'registerRestRoute' ] );

		if ( $demo->shouldLoad() ) {
			add_action( 'enqueue_block_assets', [ $demo, 'enqueueDemoStyles' ] );
		}
	}

	/**
	 * Register WordPress filter hooks.
	 *
	 * @param Providers\Demo $demo Demo provider.
	 *
	 * @return void
	 */
	#[Inject]
	public function registerFilters( Providers\Demo $demo ): void
	{
		if ( $demo->shouldLoad() ) {
			add_filter( "{$this->package}_presets", [ $demo, 'registerDemoPresets' ] );
		}
	}
}
