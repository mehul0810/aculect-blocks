<?php
/**
 * Admin asset registration.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Assets;

use Aculect\Blocks\Admin\AdminPage;
use Aculect\Blocks\Contracts\Module;

/**
 * Loads admin assets on Aculect Blocks screens.
 */
final class AdminAssets implements Module {
	/**
	 * Registers WordPress hooks.
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueues settings-page assets.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue( string $hook_suffix ): void {
		if ( 'settings_page_' . AdminPage::MENU_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'aculect-blocks-admin',
			plugins_url( 'assets/css/admin.css', ACULECT_BLOCKS_FILE ),
			array(),
			ACULECT_BLOCKS_VERSION
		);
	}
}
