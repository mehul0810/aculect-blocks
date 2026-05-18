<?php
/**
 * Rank Math FAQPage schema integration for core Accordion blocks.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Integrations;

use Aculect\Blocks\StructuredData\AccordionFaqExtractor;

/**
 * Adds FAQPage JSON-LD for visible core Accordion block content.
 */
final class RankMathFaqSchema {
	/**
	 * Registers integration hooks.
	 */
	public function register(): void {
		add_filter( 'rank_math/json_ld', array( $this, 'add_faq_schema' ), 99, 2 );
	}

	/**
	 * Adds FAQPage schema data to Rank Math's JSON-LD graph.
	 *
	 * @param mixed $data   Rank Math schema data.
	 * @param mixed $jsonld Rank Math JSON-LD helper instance.
	 *
	 * @return mixed Filtered schema data.
	 */
	public function add_faq_schema( mixed $data, mixed $jsonld ): mixed {
		unset( $jsonld );

		if ( ! is_array( $data ) || ! is_singular() ) {
			return $data;
		}

		/**
		 * Filters whether Aculect Blocks should emit FAQPage schema.
		 *
		 * Google currently limits FAQ rich-result eligibility to well-known,
		 * authoritative government or health sites. This filter lets projects
		 * keep valid schema.org output disabled where that policy is undesirable.
		 *
		 * @param bool $enabled Whether FAQPage schema output is enabled.
		 */
		$enabled = (bool) apply_filters( 'aculect_blocks_enable_faq_schema', true );

		if ( ! $enabled || $this->contains_faq_schema( $data ) ) {
			return $data;
		}

		$post_id = get_queried_object_id();

		if ( ! $post_id ) {
			return $data;
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( ! is_string( $content ) || '' === trim( $content ) ) {
			return $data;
		}

		$items = ( new AccordionFaqExtractor() )->extract( parse_blocks( $content ) );

		/**
		 * Filters extracted FAQ schema items before output.
		 *
		 * @param array<int, array{question: string, answer: string}> $items   Extracted FAQ items.
		 * @param int                                                $post_id Current post ID.
		 */
		$items = (array) apply_filters( 'aculect_blocks_faq_schema_items', $items, $post_id );
		$items = $this->normalize_items( $items );

		if ( array() === $items ) {
			return $data;
		}

		$data[] = array(
			'@type'      => 'FAQPage',
			'@id'        => untrailingslashit( get_permalink( $post_id ) ) . '#aculect-faq',
			'mainEntity' => array_map(
				static fn ( array $item ): array => array(
					'@type'          => 'Question',
					'name'           => $item['question'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $item['answer'],
					),
				),
				$items
			),
		);

		return $data;
	}

	/**
	 * Normalizes extracted FAQ rows and removes incomplete items.
	 *
	 * @param array<int, mixed> $items Extracted FAQ items.
	 *
	 * @return array<int, array{question: string, answer: string}>
	 */
	private function normalize_items( array $items ): array {
		$normalized = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$question = isset( $item['question'] ) && is_string( $item['question'] ) ? trim( $item['question'] ) : '';
			$answer   = isset( $item['answer'] ) && is_string( $item['answer'] ) ? trim( $item['answer'] ) : '';

			if ( '' === $question || '' === wp_strip_all_tags( $answer ) ) {
				continue;
			}

			$normalized[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}

		return $normalized;
	}

	/**
	 * Checks whether Rank Math already has an FAQPage node.
	 *
	 * @param array<mixed> $nodes Schema nodes.
	 */
	private function contains_faq_schema( array $nodes ): bool {
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}

			$type = $node['@type'] ?? null;

			if ( 'FAQPage' === $type || ( is_array( $type ) && in_array( 'FAQPage', $type, true ) ) ) {
				return true;
			}
		}

		return false;
	}
}
