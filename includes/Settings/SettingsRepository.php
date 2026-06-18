<?php
/**
 * Plugin settings persistence.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Settings;

/**
 * Reads, validates, and exposes Aculect Blocks settings.
 */
final class SettingsRepository {
	public const OPTION_NAME = 'aculect_blocks_settings';

	/**
	 * Default plugin settings.
	 *
	 * @var array<string, bool>
	 */
	private const DEFAULTS = array(
		'block_styles_enabled'      => true,
		'editor_assets_enabled'     => true,
		'patterns_enabled'          => true,
		'faq_schema_enabled'        => true,
		'breadcrumb_schema_enabled' => true,
	);

	/**
	 * Registers the option with WordPress Settings API.
	 */
	public function register_setting(): void {
		register_setting(
			'aculect_blocks',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'default'           => self::DEFAULTS,
				'sanitize_callback' => array( $this, 'sanitize' ),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Gets all settings merged with defaults.
	 *
	 * @return array<string, bool>
	 */
	public function all(): array {
		$stored = get_option( self::OPTION_NAME, array() );

		return $this->sanitize( is_array( $stored ) ? $stored : array() );
	}

	/**
	 * Checks whether a feature flag is enabled.
	 *
	 * @param string $key Setting key.
	 */
	public function is_enabled( string $key ): bool {
		$settings = $this->all();

		return true === ( $settings[ $key ] ?? false );
	}

	/**
	 * Gets default settings.
	 *
	 * @return array<string, bool>
	 */
	public function defaults(): array {
		return self::DEFAULTS;
	}

	/**
	 * Sanitizes settings before storage or read use.
	 *
	 * @param mixed $value Raw setting value.
	 * @return array<string, bool>
	 */
	public function sanitize( mixed $value ): array {
		$raw       = is_array( $value ) ? $value : array();
		$sanitized = array();

		foreach ( self::DEFAULTS as $key => $default ) {
			$sanitized[ $key ] = isset( $raw[ $key ] ) ? rest_sanitize_boolean( $raw[ $key ] ) : $default;
		}

		return $sanitized;
	}
}
