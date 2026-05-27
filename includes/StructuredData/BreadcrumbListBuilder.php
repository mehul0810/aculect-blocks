<?php
/**
 * Builds BreadcrumbList schema from core Breadcrumbs items.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\StructuredData;

/**
 * Converts rendered core Breadcrumbs data into schema.org BreadcrumbList nodes.
 */
final class BreadcrumbListBuilder {
	/**
	 * Builds a BreadcrumbList node.
	 *
	 * @param array<int, mixed> $items       Breadcrumb item rows.
	 * @param string            $schema_id   Stable schema node ID.
	 * @param string            $current_url Current request URL for unlinked current items.
	 *
	 * @return array<string, mixed>|null
	 */
	public function build( array $items, string $schema_id, string $current_url ): ?array {
		$list_items = array();
		$position   = 1;

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$name = $this->normalize_name( $item['label'] ?? '' );

			if ( '' === $name ) {
				continue;
			}

			$url       = $this->normalize_url( $item['url'] ?? '' );
			$list_item = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => $name,
			);

			if ( '' === $url ) {
				$url = $current_url;
			}

			if ( '' !== $url ) {
				$list_item['item'] = $url;
			}

			$list_items[] = $list_item;
			++$position;
		}

		if ( array() === $list_items ) {
			return null;
		}

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $schema_id,
			'itemListElement' => $list_items,
		);
	}

	/**
	 * Normalizes a breadcrumb label to plain schema text.
	 *
	 * @param mixed $name Raw breadcrumb label.
	 */
	private function normalize_name( mixed $name ): string {
		if ( ! is_scalar( $name ) ) {
			return '';
		}

		$text = html_entity_decode( wp_strip_all_tags( (string) $name ), ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) );
		$text = preg_replace( '/\s+/', ' ', $text );

		return is_string( $text ) ? trim( $text ) : '';
	}

	/**
	 * Normalizes a breadcrumb URL.
	 *
	 * @param mixed $url Raw URL value.
	 */
	private function normalize_url( mixed $url ): string {
		if ( ! is_scalar( $url ) ) {
			return '';
		}

		$url = esc_url_raw( (string) $url );

		return $url;
	}
}
