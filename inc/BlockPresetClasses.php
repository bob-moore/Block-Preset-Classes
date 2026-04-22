<?php
/**
 * Preset classes block extension
 *
 * PHP Version 8.2
 *
 * @package    Bmd\BlockPresetClasses
 * @author     Bob Moore <bob@bobmoore.dev>
 * @license    GPL-2.0+ <http://www.gnu.org/licenses/gpl-2.0.txt>
 * @link       https://www.bobmoore.dev
 * @since      1.0.0
 */

namespace Bmd;

use WP_Block_Type_Registry;

/**
 * Service class for block preset classes.
 */
class BlockPresetClasses
{
	/**
	 * Mount actions required
	 *
	 * @return void
	 */
	public function mount(): void
	{
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueueEditorScript' ] );
		add_action( 'rest_api_init', [ $this, 'registerRestRoute' ] );
	}
	/**
	 * WordPress block registry.
	 *
	 * @var WP_Block_Type_Registry
	 */
	protected WP_Block_Type_Registry $block_registry;
	/**
	 * Array of block presets.
	 *
	 * Internal structure:
	 * [
	 *   'core/group' => [
	 *     'My Label' => 'has-preset-my-label',
	 *   ],
	 * ]
	 *
	 * @var array<string, array<string, string>>
	 */
	protected array $block_presets = [];

	/**
	 * Array of global block classes.
	 *
	 * @var array<string, string>
	 */
	protected array $global_block_classes = [];

	/**
	 * Constructor.
	 *
	 * @param WP_Block_Type_Registry|null $block_registry The block registry.
	 */
	public function __construct( ?WP_Block_Type_Registry $block_registry = null )
	{
		$this->block_registry = $block_registry ?? WP_Block_Type_Registry::get_instance();
	}

	/**
	 * Enqueue editor assets for the block extension.
	 *
	 * @return void
	 */
	public function enqueueEditorScript(): void
	{
		$script_file = $this->buildPath( 'index.js' );

		if ( ! is_file( $script_file ) ) {
			return;
		}

		$assets = $this->getScriptAssets();
		$version = $assets['version'] ?? (string) filemtime( $script_file );
		$src = $this->buildUrl( 'index.js' );

		if ( empty( $src ) ) {
			return;
		}

		wp_enqueue_script(
			'bmd-block-preset-classes-editor',
			$src,
			$assets['dependencies'],
			$version,
			true
		);

		$this->enqueueStyleFile( 'bmd-block-preset-classes-editor', 'index.css' );
	}

	/**
	 * Enqueue a stylesheet from the build directory if it exists.
	 *
	 * @param string $handle        Style handle.
	 * @param string $relative_path Relative file path inside build.
	 *
	 * @return void
	 */
	protected function enqueueStyleFile( string $handle, string $relative_path ): void
	{
		$style_file = $this->buildPath( $relative_path );

		if ( ! is_file( $style_file ) ) {
			return;
		}

		$assets = $this->getScriptAssets();
		$version = $assets['version'] ?? (string) filemtime( $style_file );
		$src = $this->buildUrl( $relative_path );

		if ( empty( $src ) ) {
			return;
		}

		wp_enqueue_style(
			$handle,
			$src,
			[],
			$version
		);
	}

	/**
	 * Build an absolute path inside the package build directory.
	 *
	 * @param string $relative_path Relative file path inside build.
	 *
	 * @return string
	 */
	protected function buildPath( string $relative_path ): string
	{
		return wp_normalize_path(
			dirname( __DIR__ ) . '/build/' . ltrim( $relative_path, '/' )
		);
	}

	/**
	 * Resolve a build file path into a public URL.
	 *
	 * @param string $relative_path Relative file path inside build.
	 *
	 * @return string
	 */
	protected function buildUrl( string $relative_path ): string
	{
		$absolute_path = $this->buildPath( $relative_path );
		$resolved_path = realpath( $absolute_path );

		if ( false !== $resolved_path ) {
			$absolute_path = wp_normalize_path( $resolved_path );
		}

		$content_dir = wp_normalize_path( WP_CONTENT_DIR );

		if ( str_starts_with( $absolute_path, $content_dir ) ) {
			$relative = ltrim(
				substr( $absolute_path, strlen( $content_dir ) ),
				'/'
			);

			return content_url( $relative );
		}

		$root_dir = wp_normalize_path( ABSPATH );

		if ( str_starts_with( $absolute_path, $root_dir ) ) {
			$relative = ltrim(
				substr( $absolute_path, strlen( $root_dir ) ),
				'/'
			);

			return site_url( $relative );
		}

		return '';
	}

	/**
	 * Resolve script dependency metadata from WordPress build asset files.
	 *
	 * @return array{dependencies: array<int, string>, version: string|null}
	 */
	protected function getScriptAssets(): array
	{
		$asset_candidates = [
			$this->buildPath( 'index.asset.php' ),
			$this->buildPath( 'index.assets.php' ),
		];

		foreach ( $asset_candidates as $asset_file ) {
			if ( ! is_file( $asset_file ) ) {
				continue;
			}

			$asset = include $asset_file;

			if ( ! is_array( $asset ) ) {
				continue;
			}

			$dependencies = $asset['dependencies'] ?? [];
			$version = $asset['version'] ?? null;

			return [
				'dependencies' => is_array( $dependencies ) ? $dependencies : [],
				'version' => is_string( $version ) ? $version : null,
			];
		}

		return [
			'dependencies' => [],
			'version' => null,
		];
	}

	/**
	 * Register options for block presets.
	 *
	 * Internal format:
	 * 'block/name' => [
	 *   'My Label' => 'has-preset-my-label',
	 *   'My Second Label' => 'has-preset-my-second-label',
	 * ]
	 *
	 * @return void
	 */
	public function registerOptions(): void
	{
		$this->block_presets = apply_filters( 'block_preset_classes', $this->block_presets );

		if ( ! is_array( $this->block_presets ) ) {
			$this->block_presets = [];
		}

		$normalized = [];

		foreach ( $this->block_presets as $block_name => $presets ) {
			if ( ! is_string( $block_name ) || ! is_array( $presets ) ) {
				continue;
			}

			$normalized[ $block_name ] = $this->normalizeBlockPresetMap( $presets );
		}

		$this->block_presets = array_filter(
			$normalized,
			static function ( array $options ): bool {
				return ! empty( $options );
			}
		);
	}

	/**
	 * Add a single preset entry for a block.
	 *
	 * @param string $block_name Block name (e.g. 'core/group').
	 * @param string $label      Human-readable label.
	 * @param string $value      CSS class value; derived from label when empty.
	 *
	 * @return void
	 */
	public function addBlockPreset( string $block_name, string $label, string $value = '' ): void
	{
		$block_name = trim( $block_name );
		$label = trim( $label );

		if ( '' === $block_name || '' === $label ) {
			return;
		}

		$preset_value = '' !== trim( $value )
			? $this->presetName( $value )
			: 'has-preset-' . sanitize_title( $label );

		if ( '' === $preset_value || 'has-preset-' === $preset_value ) {
			return;
		}

		if ( ! isset( $this->block_presets[ $block_name ] ) ) {
			$this->block_presets[ $block_name ] = [];
		}

		$this->block_presets[ $block_name ][ $label ] = $preset_value;
	}

	/**
	 * Normalize block presets into label => value map.
	 *
	 * Supports input entries in these formats:
	 * - [ 'Label' => 'value' ]
	 * - [ [ 'label' => 'Label', 'value' => 'value' ] ]
	 * - [ 'my-value' ] (label inferred from value)
	 *
	 * @param array<int|string, mixed> $presets Input presets.
	 *
	 * @return array<string, string>
	 */
	protected function normalizeBlockPresetMap( array $presets ): array
	{
		$normalized = [];

		foreach ( $presets as $key => $preset ) {
			$label = '';
			$value = '';

			if ( is_string( $key ) && is_scalar( $preset ) ) {
				$label = trim( $key );
				$value = trim( (string) $preset );
			} elseif ( is_array( $preset ) ) {
				$label = trim( (string) ( $preset['label'] ?? '' ) );
				$value = trim( (string) ( $preset['value'] ?? '' ) );
			} elseif ( is_string( $preset ) ) {
				$label = trim( str_replace( '-', ' ', $preset ) );
				$value = trim( $preset );
			}

			if ( '' === $label || '' === $value ) {
				continue;
			}

			$normalized[ $label ] = $this->presetName( $value );
		}

		return $normalized;
	}

	/**
	 * Convert label => value map to REST list format expected by JS.
	 *
	 * @param array<string, string> $map Label => value map.
	 *
	 * @return array<int, array{label: string, value: string}>
	 */
	protected function blockPresetMapToList( array $map ): array
	{
		$list = [];

		foreach ( $map as $label => $value ) {
			$list[] = [
				'label' => $label,
				'value' => $value,
			];
		}

		return $list;
	}

	/**
	 * Ensure preset name starts with has-preset-.
	 *
	 * @param string $name Preset name.
	 * @return string
	 */
	protected function presetName( string $name ): string
	{
		$trimmed = trim( $name );

		if ( str_starts_with( $trimmed, 'has-preset-' ) ) {
			return $trimmed;
		}

		return 'has-preset-' . $trimmed;
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
				'permission_callback' => static function (): bool {
					return true;
				},
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
		$this->registerOptions();

		$response = [];

		foreach ( $this->block_presets as $block_name => $preset_map ) {
			$response[ $block_name ] = $this->blockPresetMapToList( $preset_map );
		}

		return new \WP_REST_Response( $response, 200 );
	}
}
