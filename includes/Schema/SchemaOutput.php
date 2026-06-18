<?php
/**
 * Shared JSON-LD schema output helpers.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Schema;

/**
 * Provides common schema graph detection and JSON-LD rendering.
 */
final class SchemaOutput {
	/**
	 * Appends a schema node when the graph does not already contain its type.
	 *
	 * @param array<mixed>         $nodes Schema graph nodes.
	 * @param array<string, mixed> $node  Schema node to append.
	 * @param string               $type  Schema @type to check.
	 *
	 * @return array<mixed>
	 */
	public function append_if_missing( array $nodes, array $node, string $type ): array {
		if ( $this->contains_type( $nodes, $type ) ) {
			return $nodes;
		}

		$nodes[] = $node;

		return $nodes;
	}

	/**
	 * Checks whether schema nodes already include a type.
	 *
	 * @param array<mixed> $nodes Schema nodes.
	 * @param string       $type  Schema type to find.
	 */
	public function contains_type( array $nodes, string $type ): bool {
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}

			if ( $this->node_has_type( $node, $type ) ) {
				return true;
			}

			if ( isset( $node['@graph'] ) && is_array( $node['@graph'] ) && $this->contains_type( $node['@graph'], $type ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Renders a JSON-LD script tag for one schema node.
	 *
	 * @param array<string, mixed> $node       Schema node.
	 * @param string               $class_name Script class name.
	 */
	public function render_script( array $node, string $class_name ): string {
		$json = wp_json_encode( $node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		if ( ! is_string( $json ) ) {
			return '';
		}

		return wp_get_inline_script_tag(
			$json,
			array(
				'type'  => 'application/ld+json',
				'class' => $class_name,
			)
		);
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
