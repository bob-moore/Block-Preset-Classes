<?php
/**
 * Tests for the block preset classes plugin runtime.
 *
 * @package Bmd\BlockPresetClasses\Tests
 */

namespace Bmd\BlockPresetClasses\Tests;

use Bmd\BlockPresetClasses\Providers;
use Bmd\BlockPresetClasses\Services;
use PHPUnit\Framework\TestCase;
use WP_Mock;
use WP_REST_Request;

/**
 * @covers \Bmd\BlockPresetClasses\Providers\Presets
 * @covers \Bmd\BlockPresetClasses\Providers\RestApi
 * @covers \Bmd\BlockPresetClasses\Services\FilePathResolver
 * @covers \Bmd\BlockPresetClasses\Services\UrlResolver
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
	public function file_path_resolver_resolves_relative_paths(): void
	{
		$resolver = new Services\FilePathResolver( '/var/www/plugin/' );

		$this->assertSame( '/var/www/plugin', $resolver->resolve() );
		$this->assertSame( '/var/www/plugin/build/index.js', $resolver->resolve( '/build/index.js' ) );
	}

	/**
	 * @test
	 */
	public function url_resolver_resolves_relative_urls(): void
	{
		$resolver = new Services\UrlResolver( 'https://example.test/plugin/' );

		$this->assertSame( 'https://example.test/plugin', $resolver->resolve() );
		$this->assertSame( 'https://example.test/plugin/build/index.js', $resolver->resolve( '/build/index.js' ) );
	}

	/**
	 * @test
	 */
	public function presets_normalize_filtered_block_preset_maps(): void
	{
		$presets = new Providers\Presets();
		$presets->setPackage( 'block-preset-classes' );

		WP_Mock::onFilter( 'block_preset_classes_presets' )
			->with( [] )
			->reply( [] );

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
			$presets->getBlockPresets()
		);
	}

	/**
	 * @test
	 */
	public function rest_endpoint_returns_registered_presets_as_lists(): void
	{
		$presets = new Providers\Presets();
		$presets->setPackage( 'block-preset-classes' );

		$rest = new Providers\RestApi( $presets );

		WP_Mock::onFilter( 'block_preset_classes_presets' )
			->with( [] )
			->reply( [] );

		WP_Mock::onFilter( 'block_preset_classes' )
			->with( [] )
			->reply(
				[
					'core/group' => [
						'Hero Panel' => 'hero-panel',
					],
				]
			);

		$response = $rest->getBlockClasses( new WP_REST_Request() );

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
}
