<?php
/**
 * Theme Functions
 *
 * @author Jegstudio
 * @package tourze-lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'TOURZE_LITE_VERSION' ) || define( 'TOURZE_LITE_VERSION', '1.0.5' );
defined( 'TOURZE_LITE_DIR' ) || define( 'TOURZE_LITE_DIR', trailingslashit( get_template_directory() ) );

defined( 'GUTENVERSE_COMPANION_REQUIRED_VERSION' ) || define( 'GUTENVERSE_COMPANION_REQUIRED_VERSION', '2.0.2' );
defined( 'GUTENVERSE_LIBRARY_SERVER' ) || define( 'GUTENVERSE_LIBRARY_SERVER', 'https://gutenverse.com' );

require get_parent_theme_file_path( 'inc/autoload.php' );

Tourze_Lite\Init::instance();
