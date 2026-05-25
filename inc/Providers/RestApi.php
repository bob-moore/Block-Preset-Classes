<?php
/**
 * REST API provider.
 *
 * @package Bmd\BlockPresetClasses
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/bob-moore/Block-Preset-Classes
 */

namespace Bmd\BlockPresetClasses\Providers;

use Bmd\BlockPresetClasses\Module;

/**
 * Registers REST routes for block preset data.
 */
class RestApi extends Module
{
	/**
	 * Constructor.
	 *
	 * @param Presets $presets Preset provider.
	 */
	public function __construct( protected Presets $presets )
	{
	}

	/**
	 * Register REST route for block classes.
	 *
	 * @return void
	 */
	public function registerRestRoute(): void
	{
		register_rest_route(
			'block-preset-classes/v2',
			'/all',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'getBlockClasses' ],
				'permission_callback' => static fn (): bool => true,
			]
		);
	}

	/**
	 * Get block classes for all registered presets.
	 *
	 * @param \WP_REST_Request $request REST request object.
	 *
	 * @return \WP_REST_Response REST response object.
	 */
	public function getBlockClasses( \WP_REST_Request $request ): \WP_REST_Response
	{
		$response = [];

		foreach ( $this->presets->getBlockPresets() as $block_name => $preset_map ) {
			$response[ $block_name ] = $this->presets->blockPresetMapToList( $preset_map );
		}

		return new \WP_REST_Response( $response, 200 );
	}
}
