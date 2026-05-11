<?php
/**
 * Tests for the block preset classes plugin runtime.
 *
 * @package Bmd\BlockPresetClasses\Tests
 */

namespace Bmd\BlockPresetClasses\Tests;

use PHPUnit\Framework\TestCase;
use WP_Mock;
use WP_REST_Request;

/**
 * Test helper exposing protected Plugin members.
 */
class TestablePlugin extends \Bmd\BlockPresetClasses\Plugin
{
	public function publicUrl(): string
	{
		return $this->url;
	}

	public function publicPath(): string
	{
		return $this->path;
	}

	/**
	 * @return array<string, array<string, string>>
	 */
	public function publicBlockPresets(): array
	{
		return $this->block_presets;
	}
}

/**
 * @covers \Bmd\BlockPresetClasses\Plugin
 */
class BlockPresetClassesTest extends TestCase
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
	public function constructor_sets_url_and_path(): void
	{
		$plugin = new TestablePlugin(
			'https://example.test/wp-content/plugins/block-preset-classes',
			'/var/www/html/wp-content/plugins/block-preset-classes'
		);

		$this->assertSame( 'https://example.test/wp-content/plugins/block-preset-classes/', $plugin->publicUrl() );
		$this->assertSame( '/var/www/html/wp-content/plugins/block-preset-classes/', $plugin->publicPath() );
	}

	/**
	 * @test
	 */
	public function constructor_infers_url_and_path_when_not_provided(): void
	{
		$plugin = new TestablePlugin();

		$this->assertSame( 'https://example.test/' . basename( dirname( __DIR__ ) ) . '/', $plugin->publicUrl() );
		$this->assertSame( trailingslashit( dirname( __DIR__ ) ), $plugin->publicPath() );
	}

	/**
	 * @test
	 */
	public function mount_registers_expected_wordpress_hooks(): void
	{
		$plugin = new \Bmd\BlockPresetClasses\Plugin( 'https://example.test/plugin/', '/var/www/plugin/' );

		WP_Mock::expectActionAdded( 'enqueue_block_editor_assets', [ $plugin, 'enqueueEditorAssets' ] );
		WP_Mock::expectActionAdded( 'rest_api_init', [ $plugin, 'registerRestRoute' ] );

		$plugin->mount();

		$this->addToAssertionCount( 2 );
	}

	/**
	 * @test
	 */
	public function enqueue_editor_script_returns_without_script_file(): void
	{
		$temporary_root = $this->createTemporaryPluginRoot( false );
		$plugin         = new \Bmd\BlockPresetClasses\Plugin( 'https://example.test/plugin/', $temporary_root );

		WP_Mock::userFunction( 'wp_enqueue_script', [ 'times' => 0 ] );
		WP_Mock::userFunction( 'wp_enqueue_style', [ 'times' => 0 ] );

		$plugin->enqueueEditorAssets();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * @test
	 */
	public function enqueue_editor_script_enqueues_script_and_style_when_assets_exist(): void
	{
		$temporary_root = $this->createTemporaryPluginRoot();
		$plugin         = new \Bmd\BlockPresetClasses\Plugin( 'https://example.test/plugin/', $temporary_root );

		WP_Mock::userFunction(
			'wp_enqueue_script',
			[
				'args'  => [
					'bmd-block-preset-classes-editor',
					'https://example.test/plugin/build/index.js',
					[ 'wp-element' ],
					'abc123',
					true,
				],
				'times' => 1,
			]
		);
		WP_Mock::userFunction(
			'wp_enqueue_style',
			[
				'args'  => [
					'bmd-block-preset-classes-editor',
					'https://example.test/plugin/build/index.css',
					[],
					'abc123',
				],
				'times' => 1,
			]
		);

		$plugin->enqueueEditorAssets();

		$this->addToAssertionCount( 2 );
	}

	/**
	 * @test
	 */
	public function register_options_normalizes_filtered_block_presets(): void
	{
		$plugin = new TestablePlugin( 'https://example.test/plugin/', '/var/www/plugin/' );

		WP_Mock::onFilter( 'block_preset_classes' )
			->with( [] )
			->reply(
				[
					'core/group'     => [
						'Hero Panel' => 'hero-panel',
						[
							'label' => 'Muted Card',
							'value' => 'has-preset-muted-card',
						],
					],
					'core/paragraph' => [
						'eyebrow-text',
					],
					'bad-block'      => 'not-an-array',
				]
			);

		$plugin->registerOptions();

		$this->assertSame(
			[
				'core/group'     => [
					'Hero Panel' => 'has-preset-hero-panel',
					'Muted Card' => 'has-preset-muted-card',
				],
				'core/paragraph' => [
					'eyebrow text' => 'has-preset-eyebrow-text',
				],
			],
			$plugin->publicBlockPresets()
		);
	}

	/**
	 * @test
	 */
	public function add_block_preset_adds_sanitized_preset_to_block(): void
	{
		$plugin = new TestablePlugin( 'https://example.test/plugin/', '/var/www/plugin/' );

		$plugin->addBlockPreset( 'core/group', 'Hero Panel' );
		$plugin->addBlockPreset( 'core/group', 'Muted Card', 'muted-card' );

		$this->assertSame(
			[
				'core/group' => [
					'Hero Panel' => 'has-preset-hero-panel',
					'Muted Card' => 'has-preset-muted-card',
				],
			],
			$plugin->publicBlockPresets()
		);
	}

	/**
	 * @test
	 */
	public function register_rest_route_registers_classes_endpoint(): void
	{
		$plugin = new \Bmd\BlockPresetClasses\Plugin( 'https://example.test/plugin/', '/var/www/plugin/' );

		WP_Mock::userFunction(
			'register_rest_route',
			[
				'args'  => [
					'block-preset-classes/v2',
					'/all',
					WP_Mock\Functions::type( 'array' ),
				],
				'times' => 1,
			]
		);

		$plugin->registerRestRoute();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * @test
	 */
	public function get_block_classes_returns_rest_response_with_registered_presets(): void
	{
		$plugin = new \Bmd\BlockPresetClasses\Plugin( 'https://example.test/plugin/', '/var/www/plugin/' );

		WP_Mock::onFilter( 'block_preset_classes' )
			->with( [] )
			->reply(
				[
					'core/group' => [
						'Hero Panel' => 'hero-panel',
					],
				]
			);

		$response = $plugin->getBlockClasses( new WP_REST_Request() );

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame(
			[
				'core/group' => [
					[
						'label' => 'Hero Panel',
						'value' => 'has-preset-hero-panel',
					],
				],
			],
			$response->get_data()
		);
	}

	/**
	 * Create a temporary plugin root with optional build assets.
	 *
	 * @param bool $with_assets Whether to create editor assets.
	 *
	 * @return string
	 */
	private function createTemporaryPluginRoot( bool $with_assets = true ): string
	{
		$root = trailingslashit( sys_get_temp_dir() ) . 'bpc-tests-' . uniqid( '', true ) . '/';

		mkdir( $root . 'build', 0777, true );

		if ( $with_assets ) {
			file_put_contents( $root . 'build/index.js', 'console.log("test");' );
			file_put_contents( $root . 'build/index.css', '.test {}' );
			file_put_contents(
				$root . 'build/index.asset.php',
				"<?php\nreturn [ 'dependencies' => [ 'wp-element' ], 'version' => 'abc123' ];\n"
			);
		}

		return $root;
	}
}
