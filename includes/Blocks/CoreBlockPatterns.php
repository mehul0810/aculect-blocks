<?php
/**
 * Core block pattern registrations.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Blocks;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Settings\SettingsRepository;

/**
 * Registers daily-workflow patterns built only from core blocks.
 */
final class CoreBlockPatterns implements Module {
	private const CATEGORY = 'aculect-blocks';

	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Creates the block patterns module.
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
		add_action( 'init', array( $this, 'register_patterns' ) );
	}

	/**
	 * Registers Aculect pattern category and patterns.
	 */
	public function register_patterns(): void {
		if ( ! $this->settings->is_enabled( 'patterns_enabled' ) || ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		register_block_pattern_category(
			self::CATEGORY,
			array(
				'label' => __( 'Aculect Blocks', 'aculect-blocks' ),
			)
		);

		foreach ( $this->patterns() as $name => $pattern ) {
			register_block_pattern( $name, $pattern );
		}
	}

	/**
	 * Gets pattern definitions.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function patterns(): array {
		return array(
			'aculect-blocks/card-grid'         => array(
				'title'       => __( 'Aculect Card Grid', 'aculect-blocks' ),
				'description' => __( 'A responsive three-card feature grid using core Group, Heading, Paragraph, and Button blocks.', 'aculect-blocks' ),
				'categories'  => array( self::CATEGORY ),
				'content'     => $this->card_grid_pattern(),
			),
			'aculect-blocks/faq-accordion'     => array(
				'title'       => __( 'Aculect FAQ Accordion', 'aculect-blocks' ),
				'description' => __( 'A schema-ready FAQ section using the core Accordion block.', 'aculect-blocks' ),
				'categories'  => array( self::CATEGORY ),
				'content'     => $this->faq_accordion_pattern(),
			),
			'aculect-blocks/breadcrumb-header' => array(
				'title'       => __( 'Aculect Breadcrumb Header', 'aculect-blocks' ),
				'description' => __( 'A compact page header with core Breadcrumbs and Post Title blocks.', 'aculect-blocks' ),
				'categories'  => array( self::CATEGORY ),
				'content'     => $this->breadcrumb_header_pattern(),
			),
		);
	}

	/**
	 * Gets the card grid pattern markup.
	 */
	private function card_grid_pattern(): string {
		return '<!-- wp:columns {"className":"is-style-aculect-feature-grid"} -->'
			. '<div class="wp-block-columns is-style-aculect-feature-grid">'
			. '<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"is-style-aculect-card","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-aculect-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html__( 'Feature title', 'aculect-blocks' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'Describe the outcome this card helps a visitor understand.', 'aculect-blocks' ) . '</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Learn more', 'aculect-blocks' ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group --></div><!-- /wp:column -->'
			. '<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"is-style-aculect-card","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-aculect-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html__( 'Feature title', 'aculect-blocks' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'Keep the content short enough to scan during page building.', 'aculect-blocks' ) . '</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Learn more', 'aculect-blocks' ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group --></div><!-- /wp:column -->'
			. '<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"is-style-aculect-card","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-aculect-card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html__( 'Feature title', 'aculect-blocks' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'Use core block controls to tune spacing, colors, and typography.', 'aculect-blocks' ) . '</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">' . esc_html__( 'Learn more', 'aculect-blocks' ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group --></div><!-- /wp:column -->'
			. '</div><!-- /wp:columns -->';
	}

	/**
	 * Gets the FAQ accordion pattern markup.
	 */
	private function faq_accordion_pattern(): string {
		return '<!-- wp:group {"className":"is-style-aculect-surface","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-aculect-surface">'
			. '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'Frequently asked questions', 'aculect-blocks' ) . '</h2><!-- /wp:heading -->'
			. '<!-- wp:accordion --><div class="wp-block-accordion">'
			. '<!-- wp:accordion-item --><div class="wp-block-accordion-item"><!-- wp:accordion-heading {"title":"' . esc_attr__( 'What does this answer cover?', 'aculect-blocks' ) . '"} /--><!-- wp:accordion-panel --><div class="wp-block-accordion-panel"><!-- wp:paragraph --><p>' . esc_html__( 'Write a direct answer that is visible to visitors and suitable for structured data.', 'aculect-blocks' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:accordion-panel --></div><!-- /wp:accordion-item -->'
			. '<!-- wp:accordion-item --><div class="wp-block-accordion-item"><!-- wp:accordion-heading {"title":"' . esc_attr__( 'Can I edit this with core blocks?', 'aculect-blocks' ) . '"} /--><!-- wp:accordion-panel --><div class="wp-block-accordion-panel"><!-- wp:paragraph --><p>' . esc_html__( 'Yes. Keep answers inside the Accordion panel using regular core blocks.', 'aculect-blocks' ) . '</p><!-- /wp:paragraph --></div><!-- /wp:accordion-panel --></div><!-- /wp:accordion-item -->'
			. '</div><!-- /wp:accordion --></div><!-- /wp:group -->';
	}

	/**
	 * Gets the breadcrumb header pattern markup.
	 */
	private function breadcrumb_header_pattern(): string {
		return '<!-- wp:group {"className":"is-style-aculect-surface","layout":{"type":"constrained"}} --><div class="wp-block-group is-style-aculect-surface">'
			. '<!-- wp:breadcrumbs /-->'
			. '<!-- wp:post-title {"level":1} /-->'
			. '</div><!-- /wp:group -->';
	}
}
