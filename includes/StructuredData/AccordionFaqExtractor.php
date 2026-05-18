<?php
/**
 * Extracts FAQ data from core Accordion blocks.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\StructuredData;

/**
 * Extracts schema-ready question and answer rows from parsed blocks.
 */
final class AccordionFaqExtractor {
	/**
	 * HTML tags Google Search documents as supported in FAQ Answer.text.
	 *
	 * @var array<string, array<string, true|array<int, string>>>
	 */
	private const ALLOWED_ANSWER_HTML = array(
		'a'      => array(
			'href'   => true,
			'rel'    => true,
			'target' => array( '_blank', '_self', '_parent', '_top' ),
			'title'  => true,
		),
		'b'      => array(),
		'br'     => array(),
		'div'    => array(),
		'em'     => array(),
		'h1'     => array(),
		'h2'     => array(),
		'h3'     => array(),
		'h4'     => array(),
		'h5'     => array(),
		'h6'     => array(),
		'i'      => array(),
		'li'     => array(),
		'ol'     => array(),
		'p'      => array(),
		'strong' => array(),
		'ul'     => array(),
	);

	/**
	 * Extracts FAQ items from parsed blocks.
	 *
	 * @param array<int, mixed> $blocks Parsed blocks.
	 *
	 * @return array<int, array{question: string, answer: string}>
	 */
	public function extract( array $blocks ): array {
		$items = array();

		foreach ( $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			if ( 'core/accordion-item' === ( $block['blockName'] ?? '' ) ) {
				$item = $this->extract_item( $block );

				if ( null !== $item ) {
					$items[] = $item;
				}
			}

			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$items = array_merge( $items, $this->extract( $block['innerBlocks'] ) );
			}
		}

		return $items;
	}

	/**
	 * Extracts one FAQ item from an accordion item block.
	 *
	 * @param array<string, mixed> $item_block Parsed accordion item block.
	 *
	 * @return array{question: string, answer: string}|null
	 */
	private function extract_item( array $item_block ): ?array {
		$question = '';
		$answer   = '';

		if ( empty( $item_block['innerBlocks'] ) || ! is_array( $item_block['innerBlocks'] ) ) {
			return null;
		}

		foreach ( $item_block['innerBlocks'] as $inner_block ) {
			if ( ! is_array( $inner_block ) ) {
				continue;
			}

			$block_name = $inner_block['blockName'] ?? '';

			if ( 'core/accordion-heading' === $block_name ) {
				$question = $this->extract_question( $inner_block );
			}

			if ( 'core/accordion-panel' === $block_name ) {
				$answer = $this->extract_answer( $inner_block );
			}
		}

		if ( '' === $question || '' === wp_strip_all_tags( $answer ) ) {
			return null;
		}

		return array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	/**
	 * Extracts the full text question from an accordion heading block.
	 *
	 * @param array<string, mixed> $heading_block Parsed heading block.
	 */
	private function extract_question( array $heading_block ): string {
		$attrs = isset( $heading_block['attrs'] ) && is_array( $heading_block['attrs'] ) ? $heading_block['attrs'] : array();
		$title = isset( $attrs['title'] ) && is_string( $attrs['title'] ) ? $attrs['title'] : '';

		if ( '' === $title && isset( $heading_block['innerHTML'] ) && is_string( $heading_block['innerHTML'] ) ) {
			$title = $heading_block['innerHTML'];
		}

		return $this->normalize_text( wp_strip_all_tags( $title ) );
	}

	/**
	 * Extracts Google-supported answer HTML from an accordion panel block.
	 *
	 * @param array<string, mixed> $panel_block Parsed panel block.
	 */
	private function extract_answer( array $panel_block ): string {
		$rendered = '';

		if ( ! empty( $panel_block['innerBlocks'] ) && is_array( $panel_block['innerBlocks'] ) ) {
			foreach ( $panel_block['innerBlocks'] as $inner_block ) {
				if ( is_array( $inner_block ) ) {
					$rendered .= render_block( $inner_block );
				}
			}
		} elseif ( isset( $panel_block['innerHTML'] ) && is_string( $panel_block['innerHTML'] ) ) {
			$rendered = $panel_block['innerHTML'];
		}

		$rendered = wp_kses( $rendered, self::ALLOWED_ANSWER_HTML );
		$rendered = preg_replace( '/\s+/', ' ', $rendered );

		return is_string( $rendered ) ? trim( $rendered ) : '';
	}

	/**
	 * Normalizes extracted plain text.
	 *
	 * @param string $text Text to normalize.
	 */
	private function normalize_text( string $text ): string {
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
		$text = preg_replace( '/\s+/', ' ', $text );

		return is_string( $text ) ? trim( $text ) : '';
	}
}
