<?php
/**
 * Core block style registrations.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Blocks;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Settings\SettingsRepository;

/**
 * Registers portable enterprise style variations for core blocks.
 */
final class CoreBlockStyles implements Module {
	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Creates the block styles module.
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
		add_action( 'init', array( $this, 'register_block_styles' ) );
	}

	/**
	 * Registers Aculect style variations for core blocks.
	 */
	public function register_block_styles(): void {
		if ( ! $this->settings->is_enabled( 'block_styles_enabled' ) ) {
			return;
		}

		$styles = array(
			'core/group'     => array(
				'aculect-surface' => __( 'Aculect Surface', 'aculect-blocks' ),
				'aculect-card'    => __( 'Aculect Card', 'aculect-blocks' ),
				'aculect-callout' => __( 'Aculect Callout', 'aculect-blocks' ),
				'aculect-hero'    => __( 'Aculect Hero', 'aculect-blocks' ),
				'aculect-section' => __( 'Section', 'aculect-blocks' ),
			),
			'core/columns'   => array(
				'aculect-feature-grid' => __( 'Aculect Feature Grid', 'aculect-blocks' ),
			),
			'core/list'      => array(
				'aculect-check-list' => __( 'Aculect Check List', 'aculect-blocks' ),
			),
			'core/paragraph' => array(
				'aculect-pill' => __( 'Aculect Pill', 'aculect-blocks' ),
				'aculect-caps' => __( 'Aculect Caps', 'aculect-blocks' ),
			),
			'core/button'    => array(
				'aculect-link' => __( 'Link', 'aculect-blocks' ),
			),
		);

		foreach ( $styles as $block_name => $block_styles ) {
			foreach ( $block_styles as $style_name => $label ) {
				register_block_style(
					$block_name,
					array(
						'name'  => $style_name,
						'label' => $label,
					)
				);
			}
		}
	}
}
