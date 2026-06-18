<?php
/**
 * BreadcrumbList schema integration for core Breadcrumbs blocks.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Integrations;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Schema\SchemaOutput;
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
	 * Schema output helper.
	 *
	 * @var SchemaOutput
	 */
	private SchemaOutput $schema_output;

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
	private bool $schema_emitted = false;

	/**
	 * Creates the breadcrumb schema integration.
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

		if ( '' === trim( $block_content ) || $this->schema_emitted || ! is_array( $items ) || ! $this->is_enabled() ) {
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

		$script = $this->schema_output->render_script( $schema, 'aculect-blocks-breadcrumb-schema' );

		if ( '' === $script ) {
			return $block_content;
		}

		$this->schema_emitted = true;

		return $block_content . "\n" . $script;
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

		if ( is_array( $data ) && $this->schema_output->contains_type( $data, 'BreadcrumbList' ) ) {
			$this->schema_emitted = true;
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
}
