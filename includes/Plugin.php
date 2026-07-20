<?php
/**
 * Main plugin coordinator.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks;

use Aculect\Blocks\Admin\AdminPage;
use Aculect\Blocks\Assets\AdminAssets;
use Aculect\Blocks\Assets\BlockAssets;
use Aculect\Blocks\Blocks\CoreBlockPatterns;
use Aculect\Blocks\Blocks\CoreBlockStyles;
use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Integrations\CoreAccordionSchema;
use Aculect\Blocks\Integrations\CoreBreadcrumbSchema;
use Aculect\Blocks\Schema\SchemaOutput;
use Aculect\Blocks\Settings\SettingsRepository;

/**
 * Coordinates plugin integrations.
 */
final class Plugin {
	/**
	 * Shared plugin instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Shared schema output helper.
	 *
	 * @var SchemaOutput
	 */
	private SchemaOutput $schema_output;

	/**
	 * Creates the plugin composition root.
	 */
	private function __construct() {
		$this->settings      = new SettingsRepository();
		$this->schema_output = new SchemaOutput();
	}

	/**
	 * Gets the shared plugin instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers plugin hooks.
	 */
	public function register(): void {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		foreach ( $this->modules() as $module ) {
			$module->register();
		}
	}

	/**
	 * Loads translations.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'aculect-blocks',
			false,
			dirname( plugin_basename( ACULECT_BLOCKS_FILE ) ) . '/languages'
		);
	}

	/**
	 * Gets plugin modules.
	 *
	 * @return array<int, Module>
	 */
	private function modules(): array {
		return array(
			new AdminPage( $this->settings ),
			new AdminAssets(),
			new BlockAssets( $this->settings ),
			new CoreBlockStyles( $this->settings ),
			new CoreBlockPatterns( $this->settings ),
			new CoreBreadcrumbSchema( $this->settings, $this->schema_output ),
			new CoreAccordionSchema( $this->settings, $this->schema_output ),
		);
	}
}
