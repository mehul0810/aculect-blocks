<?php
/**
 * Plugin Name: Aculect Blocks
 * Plugin URI: https://github.com/mehul0810/aculect-blocks
 * Description: Enterprise-ready block variations, patterns, and core block enhancements for WordPress block themes.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.2
 * Author: Mehul Gohil
 * Author URI: https://mehulgohil.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aculect-blocks
 *
 * @package AculectBlocks
 */

declare(strict_types=1);

namespace Aculect\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACULECT_BLOCKS_VERSION', '0.1.0' );
define( 'ACULECT_BLOCKS_FILE', __FILE__ );
define( 'ACULECT_BLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACULECT_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( ACULECT_BLOCKS_DIR . 'vendor/autoload.php' ) ) {
	require_once ACULECT_BLOCKS_DIR . 'vendor/autoload.php';
} else {
	spl_autoload_register(
		static function ( string $class_name ): void {
			$prefix = __NAMESPACE__ . '\\';

			if ( ! str_starts_with( $class_name, $prefix ) ) {
				return;
			}

			$relative = substr( $class_name, strlen( $prefix ) );
			$file     = ACULECT_BLOCKS_DIR . 'includes/' . str_replace( '\\', '/', $relative ) . '.php';

			if ( file_exists( $file ) ) {
				// phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable -- Scoped PSR-4 fallback autoloader for this plugin's includes directory.
				require_once $file;
			}
		}
	);
}

Plugin::instance()->register();
