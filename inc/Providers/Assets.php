<?php
/**
 * Asset provider.
 *
 * @package Bmd\BlockPresetClasses
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/bob-moore/Block-Preset-Classes
 */

namespace Bmd\BlockPresetClasses\Providers;

use Bmd\BlockPresetClasses\Module;
use Bmd\BlockPresetClasses\Services;

/**
 * Enqueues editor assets.
 */
class Assets extends Module
{
	protected const EDITOR_SCRIPT_HANDLE = 'bmd-block-preset-classes-editor';
	protected const EDITOR_STYLE_HANDLE  = 'bmd-block-preset-classes-editor';

	/**
	 * Constructor.
	 *
	 * @param Services\ScriptLoader $script_loader Script loader.
	 * @param Services\StyleLoader  $style_loader  Style loader.
	 */
	public function __construct(
		protected Services\ScriptLoader $script_loader,
		protected Services\StyleLoader $style_loader,
	) {
	}

	/**
	 * Enqueue editor assets for the block extension.
	 *
	 * @return void
	 */
	public function enqueueEditorAssets(): void
	{
		$this->script_loader->enqueue(
			handle: self::EDITOR_SCRIPT_HANDLE,
			src: 'build/index.js'
		);

		$this->style_loader->enqueue(
			handle: self::EDITOR_STYLE_HANDLE,
			src: 'build/index.css'
		);
	}
}
