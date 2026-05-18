<?php
/**
 * Plugin module contract.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Contracts;

/**
 * Represents a self-contained plugin module.
 */
interface Module {
	/**
	 * Registers WordPress hooks for the module.
	 */
	public function register(): void;
}
