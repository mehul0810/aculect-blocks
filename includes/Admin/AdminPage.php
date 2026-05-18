<?php
/**
 * Admin settings page.
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks\Admin;

use Aculect\Blocks\Contracts\Module;
use Aculect\Blocks\Settings\SettingsRepository;

/**
 * Renders and registers the plugin settings page.
 */
final class AdminPage implements Module {
	public const MENU_SLUG = 'aculect-blocks';

	/**
	 * Settings repository.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings;

	/**
	 * Creates the admin page module.
	 *
	 * @param SettingsRepository $settings Settings repository.
	 */
	public function __construct( SettingsRepository $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Registers WordPress hooks.
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this->settings, 'register_setting' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Registers the settings menu.
	 */
	public function register_menu(): void {
		add_options_page(
			__( 'Aculect Blocks', 'aculect-blocks' ),
			__( 'Aculect Blocks', 'aculect-blocks' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render' )
		);
	}

	/**
	 * Renders the settings screen.
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage Aculect Blocks settings.', 'aculect-blocks' ) );
		}

		$settings = $this->settings->all();
		?>
		<div class="wrap aculect-blocks-admin">
			<div class="aculect-blocks-admin__header">
				<div>
					<p class="aculect-blocks-admin__eyebrow"><?php esc_html_e( 'Reusable block system', 'aculect-blocks' ); ?></p>
					<h1><?php esc_html_e( 'Aculect Blocks', 'aculect-blocks' ); ?></h1>
					<p class="aculect-blocks-admin__intro">
						<?php esc_html_e( 'Enable portable core-block enhancements without locking the site into a custom block library.', 'aculect-blocks' ); ?>
					</p>
				</div>
				<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>">
					<?php esc_html_e( 'Open Site Editor', 'aculect-blocks' ); ?>
				</a>
			</div>

			<?php settings_errors( SettingsRepository::OPTION_NAME ); ?>

			<div class="aculect-blocks-admin__layout">
				<form class="aculect-blocks-admin__panel" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
					<?php settings_fields( 'aculect_blocks' ); ?>
					<h2><?php esc_html_e( 'Modules', 'aculect-blocks' ); ?></h2>
					<p><?php esc_html_e( 'Keep modules independent so this plugin can be reused across block themes and client sites.', 'aculect-blocks' ); ?></p>

					<?php $this->render_toggle( 'block_styles_enabled', __( 'Core block style variations', 'aculect-blocks' ), __( 'Registers enterprise-ready style variations for Group, Columns, List, and Button blocks.', 'aculect-blocks' ), $settings ); ?>
					<?php $this->render_toggle( 'editor_assets_enabled', __( 'Editor authoring polish', 'aculect-blocks' ), __( 'Loads editor-only styles that make Aculect block variations easier to identify while editing.', 'aculect-blocks' ), $settings ); ?>
					<?php $this->render_toggle( 'faq_schema_enabled', __( 'Accordion FAQ schema', 'aculect-blocks' ), __( 'Adds valid FAQPage schema from core Accordion blocks through Rank Math when FAQ content is present.', 'aculect-blocks' ), $settings ); ?>

					<?php submit_button( __( 'Save module settings', 'aculect-blocks' ) ); ?>
				</form>

				<div class="aculect-blocks-admin__aside">
					<section class="aculect-blocks-admin__panel">
						<h2><?php esc_html_e( 'System status', 'aculect-blocks' ); ?></h2>
						<ul class="aculect-blocks-admin__status-list">
							<?php $this->render_status( __( 'Block theme', 'aculect-blocks' ), wp_is_block_theme() ? __( 'Active', 'aculect-blocks' ) : __( 'Not active', 'aculect-blocks' ), wp_is_block_theme() ); ?>
							<?php $this->render_status( __( 'Rank Math', 'aculect-blocks' ), defined( 'RANK_MATH_VERSION' ) ? __( 'Detected', 'aculect-blocks' ) : __( 'Not detected', 'aculect-blocks' ), defined( 'RANK_MATH_VERSION' ) ); ?>
							<?php $this->render_status( __( 'FAQ schema', 'aculect-blocks' ), $settings['faq_schema_enabled'] ? __( 'Enabled', 'aculect-blocks' ) : __( 'Disabled', 'aculect-blocks' ), $settings['faq_schema_enabled'] ); ?>
						</ul>
					</section>

					<section class="aculect-blocks-admin__panel">
						<h2><?php esc_html_e( 'Architecture', 'aculect-blocks' ); ?></h2>
						<p><?php esc_html_e( 'Aculect Blocks extends core blocks first. Custom blocks should only be introduced when block supports, styles, variations, and patterns are not enough.', 'aculect-blocks' ); ?></p>
					</section>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders a settings toggle.
	 *
	 * @param string              $key      Setting key.
	 * @param string              $label    Toggle label.
	 * @param string              $help     Toggle help text.
	 * @param array<string, bool> $settings Current settings.
	 */
	private function render_toggle( string $key, string $label, string $help, array $settings ): void {
		$field_id = SettingsRepository::OPTION_NAME . '_' . $key;
		?>
		<div class="aculect-blocks-admin__toggle">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $field_id ); ?>"
				name="<?php echo esc_attr( SettingsRepository::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]"
				value="1"
				<?php checked( $settings[ $key ] ?? false ); ?>
			/>
			<div>
				<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
				<p><?php echo esc_html( $help ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders one status row.
	 *
	 * @param string $label  Status label.
	 * @param string $value  Status value.
	 * @param bool   $is_ok  Whether status is positive.
	 */
	private function render_status( string $label, string $value, bool $is_ok ): void {
		?>
		<li>
			<span><?php echo esc_html( $label ); ?></span>
			<strong class="<?php echo esc_attr( $is_ok ? 'is-ok' : 'is-muted' ); ?>"><?php echo esc_html( $value ); ?></strong>
		</li>
		<?php
	}
}
