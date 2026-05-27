<?php
/**
 * BreadcrumbList schema integration for core Breadcrumbs blocks.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Integrations;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Settings\SettingsRepository;
use Aculect\Blocks\StructuredData\BreadcrumbListBuilder;

/**
 * Adds BreadcrumbList JSON-LD beside rendered core Breadcrumbs block output.
 */
final class CoreBreadcrumbSchema implements Module {
	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Most recently rendered core breadcrumb items.
	 *
	 * @var array<int, mixed>|null
	 */
	private ?array $captured_items = null;

	/**
	 * Whether this request already has BreadcrumbList schema.
	 *
	 * @var bool
	 */
	private bool $schema_output = false;

	/**
	 * Creates the breadcrumb schema integration.
	 *
	 * @param SettingsRepository $settings Settings repository.
	 */
	public function __construct( SettingsRepository $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Registers integration hooks.
	 */
	public function register(): void {
		add_filter( 'block_core_breadcrumbs_items', array( $this, 'capture_items' ), PHP_INT_MAX );
		add_filter( 'render_block_core/breadcrumbs', array( $this, 'append_breadcrumb_schema' ), 10, 2 );
		add_filter( 'rank_math/json_ld', array( $this, 'track_rank_math_breadcrumb_schema' ), PHP_INT_MAX, 2 );
	}

	/**
	 * Captures core breadcrumb items during the block render.
	 *
	 * @param mixed $items Core breadcrumb item rows.
	 *
	 * @return mixed Unmodified breadcrumb items.
	 */
	public function capture_items( mixed $items ): mixed {
		if ( is_array( $items ) ) {
			$this->captured_items = $items;
		}

		return $items;
	}

	/**
	 * Appends BreadcrumbList JSON-LD to rendered core Breadcrumbs output.
	 *
	 * @param string               $block_content Rendered block HTML.
	 * @param array<string, mixed> $parsed_block  Parsed block data.
	 */
	public function append_breadcrumb_schema( string $block_content, array $parsed_block ): string {
		unset( $parsed_block );

		$items                = $this->captured_items;
		$this->captured_items = null;

		if ( '' === trim( $block_content ) || $this->schema_output || ! is_array( $items ) || ! $this->is_enabled() ) {
			return $block_content;
		}

		$current_url = $this->current_url();
		$schema      = ( new BreadcrumbListBuilder() )->build(
			$items,
			untrailingslashit( $current_url ) . '#aculect-breadcrumb',
			$current_url
		);

		if ( null === $schema ) {
			return $block_content;
		}

		$json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		if ( ! is_string( $json ) ) {
			return $block_content;
		}

		$this->schema_output = true;

		return $block_content . "\n" . '<script type="application/ld+json" class="aculect-blocks-breadcrumb-schema">' . $json . '</script>';
	}

	/**
	 * Tracks existing Rank Math BreadcrumbList output to avoid duplicate schema.
	 *
	 * @param mixed $data   Rank Math schema data.
	 * @param mixed $jsonld Rank Math JSON-LD helper instance.
	 *
	 * @return mixed Unmodified Rank Math schema data.
	 */
	public function track_rank_math_breadcrumb_schema( mixed $data, mixed $jsonld ): mixed {
		unset( $jsonld );

		if ( is_array( $data ) && $this->contains_breadcrumb_schema( $data ) ) {
			$this->schema_output = true;
		}

		return $data;
	}

	/**
	 * Checks whether breadcrumb schema output is enabled.
	 */
	private function is_enabled(): bool {
		/**
		 * Filters whether Aculect Blocks should emit BreadcrumbList schema.
		 *
		 * @param bool $enabled Whether BreadcrumbList schema output is enabled.
		 */
		return $this->settings->is_enabled( 'breadcrumb_schema_enabled' ) && (bool) apply_filters( 'aculect_blocks_enable_breadcrumb_schema', true );
	}

	/**
	 * Gets a canonical current URL for schema IDs and unlinked current items.
	 */
	private function current_url(): string {
		$canonical_url = is_singular() ? wp_get_canonical_url() : false;

		if ( is_string( $canonical_url ) && '' !== $canonical_url ) {
			return $canonical_url;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( '' !== $request_uri ) {
			return esc_url_raw( home_url( $request_uri ) );
		}

		return home_url( '/' );
	}

	/**
	 * Checks whether schema nodes already include a BreadcrumbList.
	 *
	 * @param array<mixed> $nodes Schema nodes.
	 */
	private function contains_breadcrumb_schema( array $nodes ): bool {
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}

			if ( $this->node_has_type( $node, 'BreadcrumbList' ) ) {
				return true;
			}

			if ( isset( $node['@graph'] ) && is_array( $node['@graph'] ) && $this->contains_breadcrumb_schema( $node['@graph'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks whether a schema node has the requested @type.
	 *
	 * @param array<mixed> $node Schema node.
	 * @param string       $type Schema type to find.
	 */
	private function node_has_type( array $node, string $type ): bool {
		$node_type = $node['@type'] ?? null;

		return $type === $node_type || ( is_array( $node_type ) && in_array( $type, $node_type, true ) );
	}
}
