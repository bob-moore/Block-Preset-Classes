=== Block Preset Classes ===
Contributors: Bob Moore
Tags: gutenberg, block editor, classes, utility classes, blocks
Requires at least: 6.7
Tested up to: 6.7
Stable tag: 0.1.0
Requires PHP: 8.2
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add reusable class presets to Gutenberg blocks.

== Description ==

Block Preset Classes adds a Block Presets panel in the block editor so users can toggle predefined CSS classes on blocks.

Presets are provided from PHP, exposed via REST, and consumed once per editor load. After the first request, options are filtered in JavaScript from cache for fast UI updates.

Features include:

* Block editor extension using `editor.BlockEdit`.
* REST route for preset delivery: `/wp-json/block-preset-classes/v2/all`.
* PHP filter API to register global and per-block presets.
* JavaScript filter API to mutate options dynamically based on block attributes.

== Installation ==

1. Build assets with `npm run build`.
2. Package with `npm run plugin-zip` or zip the plugin folder.
3. In WordPress admin, go to Plugins > Add New Plugin > Upload Plugin.
4. Upload and activate Block Preset Classes.

== Usage ==

1. Register presets in PHP via `block_preset_classes`.
2. Open the editor and select a supported block.
3. In the sidebar, use the Block Presets panel to toggle preset classes.

Example filter:

`add_filter( 'block_preset_classes', function( array $presets ): array {`
`    $presets['core/group'] = [`
`        'Red Background' => 'has-preset-red-background',`
`        'Blue Background' => 'has-preset-blue-background',`
`    ];`
`    return $presets;`
`} );`

== Frequently Asked Questions ==

= Does this create custom Gutenberg blocks? =

No. It extends existing blocks in the editor.

= Can I set presets per block type? =

Yes. Use block names as keys (for example, `core/group`, `core/paragraph`).

= What data format should I use in PHP? =

Preferred format is label => value:

`$presets['core/group'] = [`
`    'My Label' => 'has-preset-my-label',`
`];`

= Can I change options dynamically in JavaScript? =

Yes. Use the `bmd.blockPresets.classOptions` filter. It receives:

* options
* blockName
* blockAttributes

== Changelog ==

= 0.1.0 =

* Initial release.
* Added REST-backed block preset options.
* Added block editor UI for toggling class presets.
* Added JS filter support for runtime option mutations.

== Upgrade Notice ==

= 0.1.0 =

Initial release.