<?php
/**
 * Playground demo provider.
 *
 * @package Bmd\BlockPresetClasses
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/bob-moore/Block-Preset-Classes
 */

namespace Bmd\BlockPresetClasses\Providers;

use Bmd\BlockPresetClasses\Module;

/**
 * Registers sample presets and styles for local/Playground demos.
 */
class Demo extends Module
{
	/**
	 * Demo preset options.
	 *
	 * @var array<string, array<string, string>>
	 */
	private array $block_presets = [
		'global'         => [
			'Soft Shadow' => 'has-preset-soft-shadow',
			'Ribbon Edge' => 'has-preset-ribbon-edge',
		],
		'core/group'     => [
			'Feature Card'  => 'has-preset-feature-card',
			'Callout Panel' => 'has-preset-callout-panel',
		],
		'core/heading'   => [
			'Accent Heading' => 'has-preset-accent-heading',
		],
		'core/paragraph' => [
			'Lead Text'   => 'has-preset-lead-text',
			'Accent Note' => 'has-preset-accent-note',
		],
	];

	/**
	 * Determine whether demo presets should be loaded.
	 *
	 * @return bool
	 */
	public function shouldLoad(): bool
	{
		if ( in_array( wp_get_environment_type(), [ 'local', 'development' ], true ) ) {
			return true;
		}

		foreach ( [ 'IS_PLAYGROUND', 'WP_IS_PLAYGROUND', 'WORDPRESS_PLAYGROUND' ] as $constant ) {
			if ( defined( $constant ) && (bool) constant( $constant ) ) {
				return true;
			}
		}

		$host = sanitize_text_field(
			wp_unslash( $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '' )
		);
		$host = strtolower( $host );

		return str_contains( $host, 'playground.wordpress.net' );
	}

	/**
	 * Add demo preset options.
	 *
	 * @param array<string, array<string, string>> $presets Existing presets.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function registerDemoPresets( array $presets ): array
	{
		foreach ( $this->block_presets as $block_name => $demo_presets ) {
			$presets[ $block_name ] = array_merge( $presets[ $block_name ] ?? [], $demo_presets );
		}

		return $presets;
	}

	/**
	 * Enqueue small demo styles so the preset classes are visible in Playground.
	 *
	 * @return void
	 */
	public function enqueueDemoStyles(): void
	{
		wp_register_style( 'bmd-block-preset-classes-demo', false, [], null );
		wp_enqueue_style( 'bmd-block-preset-classes-demo' );
		wp_add_inline_style( 'bmd-block-preset-classes-demo', $this->demoCss() );
	}

	/**
	 * Get demo CSS.
	 *
	 * @return string
	 */
	private function demoCss(): string
	{
		return '
            .has-preset-feature-card {
                border: 1px solid #d6dde8;
                border-radius: 8px;
                padding: clamp(1.25rem, 4vw, 2rem);
                background: #ffffff;
            }
            .has-preset-callout-panel {
                border-left: 6px solid #3858e9;
                padding: 1rem 1.25rem;
                background: #eef3ff;
            }
            .has-preset-accent-heading {
                color: #183b56;
                text-transform: uppercase;
                letter-spacing: .08em;
            }
            .has-preset-lead-text {
                font-size: 1.25rem;
                line-height: 1.6;
            }
            .has-preset-accent-note {
                color: #3858e9;
                font-weight: 700;
            }
            .has-preset-soft-shadow {
                box-shadow: 0 18px 45px rgba(24, 59, 86, .16);
            }
            .has-preset-ribbon-edge {
                position: relative;
                overflow: hidden;
            }
            .has-preset-ribbon-edge::before {
                content: "";
                position: absolute;
                inset: 0 auto 0 0;
                width: 8px;
                background: linear-gradient(180deg, #3858e9, #00a32a);
            }
        ';
	}
}
