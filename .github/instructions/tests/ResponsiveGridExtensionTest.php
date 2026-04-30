<?php
/**
 * Tests for the responsive grid extension runtime.
 *
 * @package Bmd\ResponsiveGridExtension
 */

namespace Bmd\Tests;

use Bmd\ResponsiveGridExtension;
use PHPUnit\Framework\TestCase;
use WP_Mock;

/**
 * @covers \Bmd\ResponsiveGridExtension
 */
class ResponsiveGridExtensionTest extends TestCase
{
	/**
	 * Set up WP_Mock.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		WP_Mock::setUp();
	}

	/**
	 * Tear down WP_Mock.
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function mount_registers_expected_wordpress_hooks(): void
	{
		$plugin = new ResponsiveGridExtension(
			'https://example.test/wp-content/plugins/responsive-grid-extension/',
			'/var/www/html/wp-content/plugins/responsive-grid-extension/'
		);

		WP_Mock::expectActionAdded( 'enqueue_block_editor_assets', [ $plugin, 'enqueueEditorScript' ] );
		WP_Mock::expectActionAdded( 'wp_enqueue_scripts', [ $plugin, 'enqueueFrontendStyle' ] );
		WP_Mock::expectFilterAdded( 'render_block_core/group', [ $plugin, 'processGridBlock' ], 10, 2 );

		$plugin->mount();

		$this->addToAssertionCount( 3 );
	}

	/**
	 * @test
	 */
	public function build_path_and_url_resolve_files_inside_build_directory(): void
	{
		$plugin = new class(
			'https://example.test/plugin/',
			'/var/www/plugin/'
		) extends ResponsiveGridExtension {
			public function publicBuildPath( string $relative_path ): string
			{
				return $this->buildPath( $relative_path );
			}

			public function publicBuildUrl( string $relative_path ): string
			{
				return $this->buildUrl( $relative_path );
			}
		};

		$this->assertSame(
			'/var/www/plugin/build/index.js',
			$plugin->publicBuildPath( '/index.js' )
		);
		$this->assertSame(
			'https://example.test/plugin/build/frontend.css',
			$plugin->publicBuildUrl( 'frontend.css' )
		);
	}

	/**
	 * @test
	 */
	public function get_script_assets_reads_wordpress_asset_metadata(): void
	{
		$temporary_root = $this->createTemporaryPluginRoot();
		$asset_file     = $temporary_root . 'build/index.asset.php';

		file_put_contents(
			$asset_file,
			"<?php\nreturn [ 'dependencies' => [ 'wp-element' ], 'version' => 'abc123' ];\n"
		);

		$plugin = new class( 'https://example.test/plugin/', $temporary_root ) extends ResponsiveGridExtension {
			/**
			 * @return array{dependencies: array<int, string>, version: string|null}
			 */
			public function publicGetScriptAssets(): array
			{
				return $this->getScriptAssets();
			}
		};

		$this->assertSame(
			[
				'dependencies' => [ 'wp-element' ],
				'version'      => 'abc123',
			],
			$plugin->publicGetScriptAssets()
		);
	}

	/**
	 * @test
	 */
	public function process_grid_block_returns_original_markup_for_non_grid_groups(): void
	{
		$plugin = new ResponsiveGridExtension( 'https://example.test/plugin/', '/var/www/plugin/' );
		$html   = '<div class="wp-block-group">Content</div>';

		$this->assertSame(
			$html,
			$plugin->processGridBlock(
				$html,
				[
					'attrs' => [
						'layout' => [
							'type' => 'constrained',
						],
					],
				]
			)
		);
	}

	/**
	 * @test
	 */
	public function process_grid_block_adds_responsive_classes_and_css_variables(): void
	{
		$plugin = new ResponsiveGridExtension( 'https://example.test/plugin/', '/var/www/plugin/' );
		$html   = '<div class="wp-block-group alignwide" style="padding: 1rem">Content</div>';

		$updated = $plugin->processGridBlock(
			$html,
			[
				'attrs' => [
					'layout'                => [
						'type' => 'grid',
					],
					'responsiveGridColumns' => [
						'desktop' => 'repeat(4, 1fr)',
						'tablet'  => 'repeat(2, 1fr)',
						'mobile'  => '',
					],
					'responsiveGridRows'    => [
						'desktop' => '',
						'mobile'  => 'auto auto',
					],
				],
			]
		);

		$this->assertStringContainsString( 'wp-block-group alignwide', $updated );
		$this->assertStringContainsString( 'has-responsive-grid-columns', $updated );
		$this->assertStringContainsString( 'has-responsive-grid-columns--desktop', $updated );
		$this->assertStringContainsString( 'has-responsive-grid-columns--tablet', $updated );
		$this->assertStringNotContainsString( 'has-responsive-grid-columns--mobile', $updated );
		$this->assertStringContainsString( 'has-responsive-grid-rows', $updated );
		$this->assertStringContainsString( 'has-responsive-grid-rows--mobile', $updated );
		$this->assertStringContainsString( '--responsive-grid-template-columns--desktop: repeat(4, 1fr);', $updated );
		$this->assertStringContainsString( '--responsive-grid-template-columns--tablet: repeat(2, 1fr);', $updated );
		$this->assertStringContainsString( '--responsive-grid-template-rows--mobile: auto auto;', $updated );
	}

	/**
	 * Create a temporary plugin root with a build directory.
	 *
	 * @return string
	 */
	private function createTemporaryPluginRoot(): string
	{
		$root = trailingslashit( sys_get_temp_dir() ) . 'rge-tests-' . uniqid( '', true ) . '/';

		mkdir( $root . 'build', 0777, true );

		return $root;
	}
}
