<?php
/**
 * Block preset provider.
 *
 * @package Bmd\BlockPresetClasses
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/bob-moore/Block-Preset-Classes
 */

namespace Bmd\BlockPresetClasses\Providers;

use Bmd\BlockPresetClasses\Module;

/**
 * Normalizes preset maps for editor consumption.
 */
class Presets extends Module
{
	/**
	 * Get registered block presets.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function getBlockPresets(): array
	{
		$block_presets = apply_filters( "{$this->package}_presets", [] );
		$block_presets = apply_filters( 'block_preset_classes', $block_presets );

		if ( ! is_array( $block_presets ) ) {
			return [];
		}

		$normalized = [];

		foreach ( $block_presets as $block_name => $presets ) {
			if ( ! is_string( $block_name ) || ! is_array( $presets ) ) {
				continue;
			}

			$normalized[ $block_name ] = $this->normalizeBlockPresetMap( $presets );
		}

		return array_filter(
			$normalized,
			static fn ( array $options ): bool => ! empty( $options )
		);
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
	public function blockPresetMapToList( array $map ): array
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

		return str_starts_with( $trimmed, 'has-preset-' )
			? $trimmed
			: 'has-preset-' . $trimmed;
	}
}
