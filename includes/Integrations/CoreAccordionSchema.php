<?php
/**
 * FAQPage schema integration for core Accordion blocks.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Integrations;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Schema\SchemaOutput;
use Aculect\Blocks\Settings\SettingsRepository;
use Aculect\Blocks\StructuredData\AccordionFaqExtractor;

/**
 * Adds FAQPage JSON-LD for visible core Accordion block content.
 */
final class CoreAccordionSchema implements Module {
	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Schema output helper.
	 *
	 * @var SchemaOutput
	 */
	private SchemaOutput $schema_output;

	/**
	 * Whether this request already has FAQPage schema from this module or Rank Math.
	 *
	 * @var bool
	 */
	private bool $schema_emitted = false;

	/**
	 * Creates the Accordion FAQ schema integration.
	 *
	 * @param SettingsRepository $settings      Settings repository.
	 * @param SchemaOutput       $schema_output Shared schema output helper.
	 */
	public function __construct( SettingsRepository $settings, SchemaOutput $schema_output ) {
		$this->settings      = $settings;
		$this->schema_output = $schema_output;
	}

	/**
	 * Registers integration hooks.
	 */
	public function register(): void {
		add_filter( 'rank_math/json_ld', array( $this, 'add_schema_to_rank_math' ), 99, 2 );
		add_action( 'wp_footer', array( $this, 'print_standalone_schema' ), 5 );
	}

	/**
	 * Adds FAQPage schema data to Rank Math's JSON-LD graph.
	 *
	 * @param mixed $data   Rank Math schema data.
	 * @param mixed $jsonld Rank Math JSON-LD helper instance.
	 *
	 * @return mixed Filtered schema data.
	 */
	public function add_schema_to_rank_math( mixed $data, mixed $jsonld ): mixed {
		unset( $jsonld );

		if ( ! is_array( $data ) || ! is_singular() ) {
			return $data;
		}

		if ( $this->schema_output->contains_type( $data, 'FAQPage' ) ) {
			$this->schema_emitted = true;
			return $data;
		}

		if ( ! $this->is_enabled() ) {
			return $data;
		}

		$node = $this->build_schema_node();

		if ( null === $node ) {
			return $data;
		}

		$this->schema_emitted = true;

		return $this->schema_output->append_if_missing( $data, $node, 'FAQPage' );
	}

	/**
	 * Prints standalone FAQPage JSON-LD when no SEO graph integration emitted it.
	 */
	public function print_standalone_schema(): void {
		if ( $this->schema_emitted || ! is_singular() || ! $this->is_enabled() ) {
			return;
		}

		$node = $this->build_schema_node();

		if ( null === $node ) {
			return;
		}

		$script = $this->schema_output->render_script( $node, 'aculect-blocks-faq-schema' );

		if ( '' === $script ) {
			return;
		}

		$this->schema_emitted = true;

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SchemaOutput returns a JSON-encoded script tag via wp_get_inline_script_tag().
		echo $script;
	}

	/**
	 * Builds a FAQPage schema node from the queried post content.
	 *
	 * @return array<string, mixed>|null
	 */
	private function build_schema_node(): ?array {
		$post_id = get_queried_object_id();

		if ( ! $post_id ) {
			return null;
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( ! is_string( $content ) || '' === trim( $content ) ) {
			return null;
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
			return null;
		}

		return array(
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
	}

	/**
	 * Checks whether FAQ schema output is enabled.
	 */
	private function is_enabled(): bool {
		/**
		 * Filters whether Aculect Blocks should emit FAQPage schema.
		 *
		 * FAQ schema is generated from visible Accordion content, but Google
		 * Search no longer broadly displays FAQ rich results for most sites.
		 *
		 * @param bool $enabled Whether FAQPage schema output is enabled.
		 */
		return $this->settings->is_enabled( 'faq_schema_enabled' ) && (bool) apply_filters( 'aculect_blocks_enable_faq_schema', true );
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
}
