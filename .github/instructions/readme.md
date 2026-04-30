![Responsive Grid Extension banner](assets/banner-1544x500.jpg)

# Responsive Grid Extension

![Version](https://img.shields.io/badge/version-0.1.4-blue)
![WordPress](https://img.shields.io/badge/WordPress-6.7%2B-3858e9?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-GPL--2.0--or--later-green)
![Lint and Build](https://github.com/bob-moore/Responsive-Grid-Extension/actions/workflows/lint-build.yml/badge.svg)
[![Try it in the WordPress Playground](https://img.shields.io/badge/Try_in_Playground-v0.1.4-blue?logo=wordpress&logoColor=%23fff&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/bob-moore/Responsive-Grid-Extension/main/_playground/blueprint-github.json)

Control your grid layout per breakpoint—without ditching the core Group block.

## What this does

The core Group block supports grid layouts, but it’s pretty limited once you need different layouts across devices.

Responsive Grid Extension fills that gap. It adds simple controls for columns and rows at desktop, tablet, and mobile—then outputs the correct classes and CSS variables on the frontend.

No custom blocks. No weird workflows. Just more control where you already expect it.

## Features

- Extends the core Group block (no replacement block)
- Set grid columns per device (desktop, tablet, mobile)
- Set grid rows per device (desktop, tablet, mobile)
- Uses classes + CSS variables for clean output
- Works as a standalone plugin or Composer dependency

## Requirements

- WordPress 6.7+
- PHP 8.2+
- Node 18.12+ (for development only)

## Installation

### As a WordPress plugin

1. Download the latest installable ZIP from the [GitHub Releases page](https://github.com/bob-moore/Responsive-Grid-Extension/releases).
2. In WordPress admin, go to **Plugins > Add New Plugin > Upload Plugin**.
3. Upload the ZIP and activate **Responsive Grid Extension**.

### As a Composer dependency

1. Require the package from your consuming plugin or theme:

```bash
composer require bmd/responsive-grid-extension
```

2. Instantiate the plugin class and register its hooks in your bootstrap code:

```php
<?php

use Bmd\ResponsiveGridExtension;

$plugin = new ResponsiveGridExtension(
    plugin_dir_url( __FILE__ ),
    plugin_dir_path( __FILE__ )
);

$plugin->mount();
```

3. Ensure Composer autoloading is active in the consuming plugin or theme.
4. Keep the package in a WordPress-accessible location so built assets can be loaded.

## Usage

1. Insert a core Group block.
2. Set the Group block layout type to `grid`.
3. Open the block inspector.
4. Enter custom grid template values for columns and rows per device.

Example values:

- Columns: `repeat(3, 1fr)`
- Columns: `2fr 1fr`
- Rows: `auto auto`
- Rows: `minmax(120px, auto) 1fr`

## Development

Install dependencies:

## Changelog

### 0.1.4

- Added scoped GitHub updater bootstrap in `plugin.php` using `bmd/github-wp-updater` from `vendor/scoped`.
- Added Copilot instruction baselines under `.github/` for scoped updater install and production release packaging workflows.

### 0.1.3

- Introduced `BasicPlugin` interface (`Bmd\BasicPlugin`) defining the `mount()`, `setUrl()`, and `setPath()` contract.
- `ResponsiveGridExtension` now implements `BasicPlugin`.
- Constructor accepts optional `$url` and `$path` parameters for flexible asset resolution when used as a Composer dependency.
- `buildPath()` and `buildUrl()` now use injected URL and path properties instead of deriving them from filesystem constants.
- Plugin bootstrap is now wrapped in a named function (`create_responsive_grid_extension_plugin()`).

### 0.1.2

- Added `mount()` method to `ResponsiveGridExtension` that registers all WordPress hooks in one call.
- Simplified plugin bootstrap: replaced individual `add_action`/`add_filter` calls with `$plugin->mount()`.
- When using the library via Composer, call `$plugin->mount()` after instantiation instead of wiring hooks manually.

### 0.1.1

- Moved main class into `inc/` directory for Composer PSR-4 autoloading under `Bmd\`.
- Public class is now `Bmd\ResponsiveGridExtension` (previously `Bmd\ResponsiveGridExtension\Plugin`).
- Fixed asset path resolution after directory restructure.

### 0.1.0

- Initial release.
- Added responsive Group block grid extensions for columns and rows.

## License

GPL-2.0-or-later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
