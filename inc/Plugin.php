<?php
/**
 * Main plugin service.
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

/**
 * Service class for block preset classes.
 */
class Plugin
{
	/**
	 * URL of this plugin/package.
	 *
	 * @var string
	 */
	protected string $url;

	/**
	 * Path of this plugin/package.
	 *
	 * @var string
	 */
	protected string $path;

	/**
	 * Array of block presets.
	 *
	 * @var array<string, array<string, string>>
	 */
	protected array $block_presets = [];

	/**
	 * Constructor.
	 *
	 * @param string $url  URL to the plugin directory.
	 * @param string $path Absolute path to the plugin directory.
	 */
	public function __construct(
		string $url = '',
		string $path = ''
	) {
		$this->setUrl( ! empty( $url ) ? $url : Utilities::getUrl() );
		$this->setPath( ! empty( $path ) ? $path : Utilities::getPath() );
	}

	/**
	 * Set the plugin root URL.
	 *
	 * @param string $url URL to the plugin root.
	 *
	 * @return void
	 */
	public function setUrl( string $url ): void
	{
		$this->url = trailingslashit(
			apply_filters( 'block_preset_classes_plugin_url', esc_url_raw( $url ) )
		);
	}

	/**
	 * Set the plugin root path.
	 *
	 * @param string $path Absolute path to the plugin root.
	 *
	 * @return void
	 */
	public function setPath( string $path ): void
	{
		$this->path = trailingslashit(
			apply_filters( 'block_preset_classes_plugin_path', wp_normalize_path( $path ) )
		);
	}

	/**
	 * Mount actions required.
	 *
	 * @return void
	 */
	public function mount(): void
	{
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueueEditorAssets' ] );
		add_action( 'rest_api_init', [ $this, 'registerRestRoute' ] );
	}

	/**
	 * Enqueue editor assets for the block extension.
	 *
	 * @return void
	 */
	public function enqueueEditorAssets(): void
	{
		$script_file = $this->path . 'build/index.js';

		if ( ! is_file( $script_file ) ) {
			return;
		}

		$assets  = $this->getAssetData( 'index' );
		$version = $assets['version'] ?? (string) filemtime( $script_file );
		$src     = $this->url . 'build/index.js';

		wp_enqueue_script(
			'bmd-block-preset-classes-editor',
			$src,
			$assets['dependencies'],
			$version,
			true
		);

		$style_file = $this->path . 'build/index.css';

		if ( ! is_file( $style_file ) ) {
			return;
		}

		$style_data = $this->getAssetData( 'index' );

		wp_enqueue_style(
			'bmd-block-preset-classes-editor',
			$this->url . 'build/index.css',
			[],
			$style_data['version'] ?? (string) filemtime( $style_file )
		);
	}

	/**
	 * Resolve script dependency metadata from WordPress build asset files.
	 *
	 * @param string $key Build asset key without the `.asset.php` suffix.
	 *
	 * @return array{dependencies: array<int, string>, version: string|null}
	 */
	protected function getAssetData( string $key ): array
	{
		$asset_file = $this->path . "build/{$key}.asset.php";

		if ( ! is_file( $asset_file ) ) {
			return [
				'dependencies' => [],
				'version'      => null,
			];
		}

		$asset = include $asset_file;

		if ( ! is_array( $asset ) ) {
			return [
				'dependencies' => [],
				'version'      => null,
			];
		}

		$dependencies = $asset['dependencies'] ?? [];
		$version      = $asset['version'] ?? null;

		return [
			'dependencies' => is_array( $dependencies ) ? $dependencies : [],
			'version'      => is_string( $version ) ? $version : null,
		];
	}

	/**
	 * Register options for block presets.
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
		$label      = trim( $label );

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
	 *
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
