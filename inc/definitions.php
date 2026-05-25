<?php
/**
 * PHP-DI service definitions.
 *
 * @package Bmd\BlockPresetClasses
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/bob-moore/Block-Preset-Classes
 */

namespace Bmd\BlockPresetClasses;

return [
	Controller::class                => \DI\autowire(),
	Services\FilePathResolver::class => \DI\autowire(),
	Services\UrlResolver::class      => \DI\autowire(),
	Services\ScriptLoader::class     => \DI\autowire(),
	Services\StyleLoader::class      => \DI\autowire(),
	Providers\Assets::class          => \DI\autowire(),
	Providers\Presets::class         => \DI\autowire(),
	Providers\RestApi::class         => \DI\autowire(),
	Providers\Demo::class            => \DI\autowire(),
];
