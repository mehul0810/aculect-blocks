<?php
/**
 * Minimal WordPress stubs for lightweight unit tests.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

$GLOBALS['aculect_blocks_test_options'] = array();
$GLOBALS['aculect_blocks_test_pattern_categories'] = array();
$GLOBALS['aculect_blocks_test_patterns'] = array();

function test_assert( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function test_assert_same( mixed $expected, mixed $actual, string $message ): void {
	if ( $expected !== $actual ) {
		throw new RuntimeException( $message . ' Expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true ) . '.' );
	}
}

function __( string $text, string $domain = 'default' ): string {
	unset( $domain );
	return $text;
}

function esc_html__( string $text, string $domain = 'default' ): string {
	unset( $domain );
	return htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function esc_attr__( string $text, string $domain = 'default' ): string {
	unset( $domain );
	return htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function get_bloginfo( string $show = '' ): string {
	unset( $show );
	return 'UTF-8';
}

function wp_strip_all_tags( string $text ): string {
	return strip_tags( $text );
}

function wp_kses( string $text, array $allowed_html ): string {
	unset( $allowed_html );
	return $text;
}

function render_block( array $block ): string {
	return isset( $block['innerHTML'] ) && is_string( $block['innerHTML'] ) ? $block['innerHTML'] : '';
}

function esc_url_raw( string $url ): string {
	return filter_var( $url, FILTER_SANITIZE_URL );
}

function wp_json_encode( mixed $value, int $flags = 0 ): string|false {
	return json_encode( $value, $flags );
}

function wp_get_inline_script_tag( string $data, array $attributes = array() ): string {
	$attr = '';
	foreach ( $attributes as $name => $value ) {
		$attr .= ' ' . htmlspecialchars( (string) $name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '="' . htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) . '"';
	}

	return '<script' . $attr . '>' . $data . '</script>';
}

function get_option( string $option, mixed $default = false ): mixed {
	return $GLOBALS['aculect_blocks_test_options'][ $option ] ?? $default;
}

function rest_sanitize_boolean( mixed $value ): bool {
	if ( is_string( $value ) ) {
		$value = strtolower( $value );
		return in_array( $value, array( '1', 'true', 'yes', 'on' ), true );
	}

	return (bool) $value;
}

function register_block_pattern_category( string $name, array $properties ): bool {
	$GLOBALS['aculect_blocks_test_pattern_categories'][ $name ] = $properties;

	return true;
}

function register_block_pattern( string $name, array $properties ): bool {
	$GLOBALS['aculect_blocks_test_patterns'][ $name ] = $properties;

	return true;
}
