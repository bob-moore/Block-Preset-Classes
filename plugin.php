<?php
/**
 * Plugin Name:       Block Preset Classes
 * Plugin URI:        https://github.com/bob-moore/Block-Preset-Classes
 * Author:            Bob Moore
 * Author URI:        https://www.bobmoore.dev
 * Description:       Adds configurable preset classes to Gutenberg blocks.
 * Version:           0.3.4
 * Requires at least: 6.7
 * Tested up to:      7.0
 * Requires PHP:      8.2
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       block-preset-classes
 *
 * @package           block-preset-classes
 * @author            Bob Moore <bob@bobmoore.dev>
 * @license           GPL-2.0-or-later <https://www.gnu.org/licenses/gpl-2.0.html>
 * @link              https://github.com/bob-moore/Block-Preset-Classes
 */

use Bmd\BlockPresetClasses\ServiceLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/scoped/autoload.php';

new ServiceLoader();
