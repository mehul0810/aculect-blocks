<?php
/**
 * Main plugin coordinator.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks;

use Aculect\Blocks\Integrations\RankMathFaqSchema;

/**
 * Coordinates plugin integrations.
 */
final class Plugin {
	/**
	 * Shared plugin instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Gets the shared plugin instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers plugin hooks.
	 */
	public function register(): void {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_integrations' ) );
	}

	/**
	 * Loads translations.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'aculect-blocks',
			false,
			dirname( plugin_basename( ACULECT_BLOCKS_FILE ) ) . '/languages'
		);
	}

	/**
	 * Registers integrations that extend core WordPress behavior.
	 */
	public function register_integrations(): void {
		( new RankMathFaqSchema() )->register();
	}
}
