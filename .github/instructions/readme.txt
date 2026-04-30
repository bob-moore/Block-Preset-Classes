![Responsive Grid Extension banner](assets/banner-1544x500.jpg)

=== Responsive Grid Extension ===
Contributors: Bob Moore
Tags: grid, group block, layout, responsive, blocks
Requires at least: 6.7
Tested up to: 6.7
Stable tag: 0.1.4
Requires PHP: 8.2
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

![Version](https://img.shields.io/badge/version-0.1.4-blue)
![WordPress](https://img.shields.io/badge/WordPress-6.7%2B-3858e9?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-GPL--2.0--or--later-green)
![Lint and Build](https://github.com/bob-moore/Responsive-Grid-Extension/actions/workflows/lint-build.yml/badge.svg)
[![Try it in the WordPress Playground](https://img.shields.io/badge/Try_in_Playground-v0.1.4-blue?logo=wordpress&logoColor=%23fff&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/bob-moore/Responsive-Grid-Extension/main/_playground/blueprint-github.json)

Add responsive grid column and row controls to the core Group block.

== Description ==

Responsive Grid Extension adds responsive grid settings to the core Group block in the block editor.

Use it to define custom grid template columns and rows for desktop, tablet, and mobile without replacing the native Group block workflow.

The plugin adds editor controls and outputs matching classes and CSS custom properties on render so the frontend reflects the configured layout.

Features include:

* Responsive grid column settings for desktop, tablet, and mobile.
* Responsive grid row settings for desktop, tablet, and mobile.
* Extension of the native Group block instead of a separate custom block.
* Support for package-based usage in larger WordPress codebases.

== Installation ==

1. Download the latest installable ZIP from the GitHub Releases page: https://github.com/bob-moore/Responsive-Grid-Extension/releases
2. In WordPress admin, go to Plugins > Add New Plugin > Upload Plugin.
3. Upload the ZIP and activate the plugin through the Plugins screen in WordPress.
4. Add a Group block, set its layout to `grid`, and configure responsive columns and rows in the sidebar.

== Frequently Asked Questions ==

= Does this create a new block? =

No. It extends the core Group block.

= When do the controls appear? =

The responsive controls are intended for Group blocks using the `grid` layout type.

= Can this package be included via Composer? =

Yes.

1. Require `bmd/responsive-grid-extension` in your plugin or theme.
2. Instantiate `Bmd\ResponsiveGridExtension`, passing your plugin's URL and path.
3. Call `$plugin->mount()` to register all hooks.

Make sure Composer autoloading is active and the package is installed in a web-accessible WordPress path.

== Changelog ==

= 0.1.4 =

* Added scoped GitHub updater bootstrap in `plugin.php` using `bmd/github-wp-updater` from `vendor/scoped`.
* Added release and instruction baselines under `.github/` for scoped updater and production packaging workflows.

= 0.1.3 =

* Introduced `BasicPlugin` interface defining the `mount()`, `setUrl()`, and `setPath()` contract.
* `ResponsiveGridExtension` now implements `BasicPlugin`.
* Constructor accepts optional `$url` and `$path` parameters for flexible asset resolution when used as a Composer dependency.
* Plugin bootstrap is now wrapped in a named function.

= 0.1.2 =

* Added `mount()` method to `ResponsiveGridExtension` that registers all WordPress hooks in one call.
* Simplified plugin bootstrap.

= 0.1.1 =

* Moved main class into `inc/` directory for Composer PSR-4 autoloading under `Bmd\`.
* Public class is now `Bmd\ResponsiveGridExtension` (previously `Bmd\ResponsiveGridExtension\Plugin`).
* Fixed asset path resolution after directory restructure.

= 0.1.0 =

* Initial release.
* Added responsive Group block grid column and row controls.

== Upgrade Notice ==

= 0.1.4 =

GitHub updater bootstrap was added. Ensure `vendor/scoped` is present in production installs so updater classes autoload correctly.

= 0.1.3 =

Constructor now accepts `$url` and `$path` parameters. Composer integrations should pass `plugin_dir_url( __FILE__ )` and `plugin_dir_path( __FILE__ )` on instantiation.

= 0.1.1 =

Namespace updated to `Bmd\ResponsiveGridExtension`. Update any Composer integrations that previously referenced `Bmd\ResponsiveGridExtension\Plugin`.
