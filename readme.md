# Block Preset Classes

![Block Preset Classes](assets/banner-1544x500.jpg)

[![WordPress](https://img.shields.io/badge/WordPress-6.7%2B-3858e9?logo=wordpress&logoColor=fff)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4?logo=php&logoColor=fff)](https://www.php.net/)
[![Latest Release](https://img.shields.io/github/v/release/bob-moore/Block-Preset-Classes?label=release)](https://github.com/bob-moore/Block-Preset-Classes/releases/latest)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)](https://www.gnu.org/licenses/gpl-2.0.html)

[![Lint CSS](https://github.com/bob-moore/Block-Preset-Classes/actions/workflows/lint-css.yml/badge.svg)](https://github.com/bob-moore/Block-Preset-Classes/actions/workflows/lint-css.yml)
[![Lint JS](https://github.com/bob-moore/Block-Preset-Classes/actions/workflows/lint-js.yml/badge.svg)](https://github.com/bob-moore/Block-Preset-Classes/actions/workflows/lint-js.yml)
[![Lint PHP](https://github.com/bob-moore/Block-Preset-Classes/actions/workflows/lint-php.yml/badge.svg)](https://github.com/bob-moore/Block-Preset-Classes/actions/workflows/lint-php.yml)

Want to give it a test drive? Try it in the WP Playground: [![Try it in the WordPress Playground](https://img.shields.io/badge/WP_Playground-v0.3.6-blue?logo=wordpress&logoColor=%23fff&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/bob-moore/Block-Preset-Classes/main/_playground/blueprint-github.json)

Block styles are useful… until you need more than one.

By default, WordPress only lets you apply a single block style at a time. That means if you want combinations (padding + border + background), you end up creating a bunch of nearly identical styles just to cover every variation.

This plugin solves that.

Block Preset Classes lets you define reusable presets (CSS classes) and apply multiple of them to a block.

Instead of picking one style, you can stack presets and mix them however you want.

Under the hood, it simply adds those classes to the block’s Additional CSS Classes field — the same way block styles work, just without the one-style limit.

Add reusable preset classes to WordPress blocks and let editors stack multiple presets on the same block.

## Features

- Lets editors apply multiple class presets to the same block instead of being limited to one block style.
- Supports global and block-specific preset definitions.
- Loads preset options from one REST request per editor session, then filters in JavaScript from cache.
- Supports runtime option mutation in JS via `bmd.blockPresets.classOptions`.
- Ships with a scoped GitHub updater for release-based plugin updates.
- Works as both a standalone plugin and a Composer-installed dependency.

## Requirements

- WordPress 6.7+
- PHP 8.2+

## Installation

### Install as a plugin

1. Download the [latest release ZIP](https://github.com/bob-moore/Block-Preset-Classes/releases/latest/download/block-preset-classes.zip).
2. In WordPress admin, go to Plugins > Add New Plugin > Upload Plugin.
3. Upload the ZIP and activate Block Preset Classes.

### Install via Composer (library usage)

If you are embedding this into your own project:

```bash
composer require bmd/block-preset-classes
```

If your project is not already using PHP-DI, bootstrap the plugin with `Main`:

```php
use Bmd\BlockPresetClasses\Main;

$dependency_url  = plugin_dir_url( __FILE__ ) . 'vendor/bmd/block-preset-classes/';
$dependency_path = plugin_dir_path( __FILE__ ) . 'vendor/bmd/block-preset-classes/';

$plugin = new Main(
	[
		'package' => 'block_preset_classes',
		'path'    => $dependency_path,
		'url'     => $dependency_url,
	]
);

$plugin->mount();
```

The `path` and `url` config values should point to the Block Preset Classes dependency root, not the file where you call it. For example, pass `/path/to/vendor/bmd/block-preset-classes/` and the matching public URL for that directory.

### Using an Existing PHP-DI Container

If your parent plugin already uses PHP-DI, you can skip `Main` and load Block Preset Classes into the parent container. This keeps one application container and lets the parent controller initialize this package's controller alongside its own services.

Add the package definitions to your parent definitions file and override the package-relative path and URL resolvers:

```php
<?php

namespace Acme\Example;

use Bmd\BlockPresetClasses\Controller as BlockPresetClassesController;
use Bmd\BlockPresetClasses\Services\FilePathResolver;
use Bmd\BlockPresetClasses\Services\UrlResolver;
use function DI\autowire;
use function DI\string;

$block_preset_definitions = require __DIR__ . '/../vendor/bmd/block-preset-classes/inc/definitions.php';

return array_merge(
	$block_preset_definitions,
	[
		'package' => 'acme_example',
		'path'    => ACME_EXAMPLE_PATH,
		'url'     => ACME_EXAMPLE_URL,

		FilePathResolver::class => autowire()
			->constructorParameter(
				'path',
				string( '{path}vendor/bmd/block-preset-classes/' )
			),

		UrlResolver::class => autowire()
			->constructorParameter(
				'url',
				string( '{url}vendor/bmd/block-preset-classes/' )
			),
	]
);
```

Then resolve this package's controller from your parent container:

```php
use Bmd\BlockPresetClasses\Controller as BlockPresetClassesController;

$container->get( BlockPresetClassesController::class );
```

If your build scopes Composer dependencies, adjust the namespace and path to match the scoped location, for example `Vendor\Plugin\Bmd\BlockPresetClasses` and `vendor/scoped/bmd/block-preset-classes/`.

### Simple Non-PHP-DI Bootstraps

For a plugin that only wants to load Block Preset Classes as a Composer dependency, let `Main` manage its own internal container:

```php
<?php

use Bmd\BlockPresetClasses\Main;

require_once __DIR__ . '/vendor/autoload.php';

add_action(
	'plugins_loaded',
	static function (): void {
		$plugin = new Main(
			[
				'package' => 'block_preset_classes',
				'path'    => plugin_dir_path( __FILE__ ) . 'vendor/bmd/block-preset-classes/',
				'url'     => plugin_dir_url( __FILE__ ) . 'vendor/bmd/block-preset-classes/',
			]
		);

		$plugin->mount();
	}
);
```

For a theme-embedded dependency, use the theme path and URI instead:

```php
use Bmd\BlockPresetClasses\Main;

$plugin = new Main(
	[
		'package' => 'block_preset_classes',
		'path'    => get_theme_file_path( 'vendor/bmd/block-preset-classes/' ),
		'url'     => get_theme_file_uri( 'vendor/bmd/block-preset-classes/' ),
	]
);

$plugin->mount();
```

## Usage

1. Define preset classes in PHP.
2. Select a supported block in the editor.
3. Open the preset class controls in the sidebar.
4. Choose one or more presets.
5. Save the post and the preset classes are added to the block.

## Registering Presets

Presets are provided in PHP through the `block_preset_classes` filter.

Example:

```php
<?php

add_filter( 'block_preset_classes', function( array $presets ): array {
		$presets['core/group'] = [
				'Red Background'  => 'has-preset-red-background',
				'Blue Background' => 'has-preset-blue-background',
		];

		$presets['core/paragraph'] = [
				'Small Caps' => 'has-preset-small-caps',
		];

		return $presets;
} );
```

Each preset map should use the shape `block_name => [ label => value ]`. If a value does not start with `has-preset-`, it is normalized automatically.

## JavaScript Runtime Mutation

You can dynamically adjust options based on block name and attributes:

```ts
addFilter(
	'bmd.blockPresets.classOptions',
	'my-plugin/block-preset-mutations',
	( options, blockName, blockAttributes ) => {
		if ( blockName === 'core/group' && blockAttributes?.layout?.type === 'flex' ) {
			return [
				...options,
				{ label: 'Reverse Mobile', value: 'has-preset-reverse-mobile' },
			];
		}

		return options;
	}
);
```

This hook runs after the REST data has loaded, so you can adjust options based on block name or current attributes without changing the server-side preset map.

## REST Endpoint

- Route: `/wp-json/block-preset-classes/v2/all`
- Method: `GET`
- Auth: Public (permission callback returns true)

## Updates

This plugin is distributed through GitHub releases (not WordPress.org). The plugin includes a scoped GitHub updater so WordPress can detect and apply new versions from this repository.

## Changelog

### 0.3.6

- Restored demo preset loading in WordPress Playground and development environments.

### 0.3.5

- Renamed the plugin bootstrap file to `block-preset-classes.php`.
- Scoped PHP linting to plugin source classes while preserving syntax checks for the bootstrap file.
- Refreshed CI dependency installation metadata for the current package lock.

### 0.3.4

- Unified the PHP architecture around `Main`, `Controller`, provider, service, and utility classes under the `Bmd\\BlockPresetClasses` namespace.
- Simplified editor asset loading to match the related plugins' enqueue and asset-resolution patterns.
- Split GitHub Actions into dedicated CSS, JS, and PHP workflows and aligned package lint scripts with the other plugins.
- Updated README and release metadata to match the shared plugin structure.

### 0.3.3

- Refined the PHP plugin architecture around a dedicated bootstrapper, plugin service, demo service, and utility helper.
- Updated Composer autoloading for the `Bmd\BlockPresetClasses` namespace structure.
- Split GitHub Actions into separate CSS, JS, and PHP workflows to match the related plugins.
- Added automatic package URL/path inference for Composer consumers.
- Removed the shared plugin interface dependency.
- Loaded demo presets only in local and WordPress Playground environments.
- Updated Playground setup to use `IS_PLAYGROUND` and the latest release ZIP.
- Added repository-local Codex/GitHub skills for WordPress workflows.

### 0.3.2

- Added searchable multi-select UI for blocks with more than ten preset options.
- Added `react-select` as the editor select dependency.

### 0.3.1

- Added PHPUnit coverage with WP_Mock.
- Added WordPress Playground demo content and sample preset registration.
- Added GitHub Actions lint, build, and PHP test workflow.

### 0.3.0

- Moved the GitHub updater into a scoped Composer dependency under `vendor/scoped`.
- Added `wpify/scoper` configuration and tracked scoped lock files for reproducible releases.
- Standardized the release packaging workflow.

### 0.2.1

- Added GitHub updater integration so plugin installs can detect new releases from this repository.
- Release ZIP now includes the updater dependency installed from Composer.

### 0.2.0

- Added `mount()`, `setUrl()`, and `setPath()` methods for package bootstrap integration.
- Refactored `BlockPresetClasses` to support injected URL and path values.
- `buildPath` and `buildUrl` now pass through `block_preset_classes_plugin_path` / `block_preset_classes_plugin_url` filters for integrator overrides.
- Updated plugin bootstrap to a named function `create_block_preset_classes_plugin()`.
- Removed hardcoded `"version"` from `composer.json`; version is now read from git tags.

### 0.1.1

- Added `mount()` method to class that registers all WordPress hooks in one call
- Simplified plugin bootstrap: replaced individual `add_action`/`add_filter` calls with `$plugin->mount()`.
- When using the library via Composer, call `$plugin->mount()` after instantiation instead of wiring hooks manually.

### 0.1.0

- Initial Block Preset Classes release.
- Added REST-backed block preset options.
- Added block editor UI for toggling class presets.
- Added JavaScript filter support for runtime option mutations.

## License

GPL-2.0-or-later. See [GNU GPL v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).
