<?php
/**
 * Block asset registration.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Assets;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Settings\SettingsRepository;

/**
 * Loads frontend and editor assets for Aculect block variations.
 */
final class BlockAssets implements Module {
	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Creates the block asset module.
	 *
	 * @param SettingsRepository $settings Settings repository.
	 */
	public function __construct( SettingsRepository $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Registers WordPress hooks.
	 */
	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_styles' ) );
	}

	/**
	 * Enqueues frontend block style CSS.
	 */
	public function enqueue_frontend_styles(): void {
		if ( ! $this->settings->is_enabled( 'block_styles_enabled' ) ) {
			return;
		}

		wp_enqueue_style(
			'aculect-blocks-block-styles',
			plugins_url( 'assets/css/block-styles.css', ACULECT_BLOCKS_FILE ),
			array(),
			ACULECT_BLOCKS_VERSION
		);
	}

	/**
	 * Enqueues editor block style CSS.
	 */
	public function enqueue_editor_styles(): void {
		if ( ! $this->settings->is_enabled( 'editor_assets_enabled' ) && ! $this->settings->is_enabled( 'block_styles_enabled' ) ) {
			return;
		}

		$dependencies = array( 'wp-edit-blocks' );

		if ( $this->settings->is_enabled( 'block_styles_enabled' ) ) {
			wp_enqueue_style(
				'aculect-blocks-editor-block-styles',
				plugins_url( 'assets/css/block-styles.css', ACULECT_BLOCKS_FILE ),
				$dependencies,
				ACULECT_BLOCKS_VERSION
			);
		}

		if ( $this->settings->is_enabled( 'editor_assets_enabled' ) ) {
			wp_enqueue_style(
				'aculect-blocks-editor',
				plugins_url( 'assets/css/editor.css', ACULECT_BLOCKS_FILE ),
				$dependencies,
				ACULECT_BLOCKS_VERSION
			);
		}
	}
}
