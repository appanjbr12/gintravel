<?php
/**
 * Init Configuration
 *
 * @author Jegstudio
 * @package tourze-lite
 */

namespace Tourze_Lite;

use WP_Query;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init Class
 *
 * @package tourze-lite
 */
class Init {

	/**
	 * Instance variable
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Class instance.
	 *
	 * @return Init
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->init_instance();
		$this->load_hooks();
	}

	/**
	 * Load initial hooks.
	 */
	private function load_hooks() {
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
		add_action( 'init', array( $this, 'register_block_patterns' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this, 'dashboard_scripts' ) );

		add_action( 'wp_ajax_tourze-lite_set_admin_notice_viewed', array( $this, 'notice_closed' ) );

		add_action( 'after_switch_theme', array( $this, 'update_global_styles_after_theme_switch' ) );
		add_filter( 'gutenverse_template_path', array( $this, 'template_path' ), null, 3 );
		add_filter( 'gutenverse_themes_template', array( $this, 'add_template' ), 10, 2 );
		add_filter( 'gutenverse_block_config', array( $this, 'default_font' ), 10 );
		add_filter( 'gutenverse_font_header', array( $this, 'default_header_font' ) );
		add_filter( 'gutenverse_global_css', array( $this, 'global_header_style' ) );

		add_filter( 'gutenverse_stylesheet_directory', array( $this, 'change_stylesheet_directory' ) );
		add_filter( 'gutenverse_themes_override_mechanism', '__return_true' );

		add_filter( 'gutenverse_themes_support_section_global_style', '__return_true' );
		add_filter( 'gutenverse_wporg_plus_mechanism', '__return_true' );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Update Global Styles After Theme Switch
	 */
	public function update_global_styles_after_theme_switch() {
		// Get the path to the current theme's theme.json file.
		$theme_json_path = get_template_directory() . '/theme.json';
		$theme_slug      = get_option( 'stylesheet' ); // Get the current theme's slug.
		$args            = array(
			'post_type'      => 'wp_global_styles',
			'post_status'    => 'publish',
			'name'           => 'wp-global-styles-' . $theme_slug,
			'posts_per_page' => 1,
		);

		$global_styles_query = new WP_Query( $args );
		// Check if the theme.json file exists.
		if ( file_exists( $theme_json_path ) && $global_styles_query->have_posts() ) {
			$global_styles_query->the_post();
			$global_styles_post_id = get_the_ID();
			// Step 2: Get the existing global styles (color palette).
			$global_styles_content = json_decode( get_post_field( 'post_content', $global_styles_post_id ), true );
			if ( isset( $global_styles_content['settings']['color']['palette']['theme'] ) ) {
				$existing_colors = $global_styles_content['settings']['color']['palette']['theme'];
			} else {
				$existing_colors = array();
			}

			// Step 3: Extract slugs from the existing colors.
			$existing_slugs = array_column( $existing_colors, 'slug' );
			// Step 4:Read the contents of the theme.json file.

			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
			}
			$theme_json_content = $wp_filesystem->get_contents( $theme_json_path );
			$theme_json_data    = json_decode( $theme_json_content, true );

			// Access the color palette from the theme.json file.
			if ( isset( $theme_json_data['settings']['color']['palette'] ) ) {

				$theme_colors = $theme_json_data['settings']['color']['palette'];

				// Step 5: Loop through theme.json colors and add them if they don't exist.
				foreach ( $theme_colors as $theme_color ) {
					if ( ! in_array( $theme_color['slug'], $existing_slugs ) ) {
						$existing_colors[] = $theme_color; // Add new color to the existing palette.
					}
				}
				foreach ( $theme_colors as $theme_color ) {
					$theme_slug = $theme_color['slug'];

					// Step 6: Use in_array to check if the slug already exists in the global palette.
					if ( ! in_array( $theme_slug, $existing_slugs ) ) {
						// If the slug does not exist, add the theme color to the global palette.
						$global_colors[] = $theme_color;
					}
				}
				// Step 6: Update the global styles content with the new colors.
				$global_styles_content['settings']['color']['palette']['theme'] = $existing_colors;

				// Step 7: Save the updated global styles back to the post.
				wp_update_post(
					array(
						'ID'           => $global_styles_post_id,
						'post_content' => wp_json_encode( $global_styles_content ),
					)
				);

			}
			wp_reset_postdata(); // Reset the query.
		}
	}

	/**
	 * Setup theme.
	 */
	public function setup_theme() {
		load_theme_textdomain( 'tourze-lite', get_template_directory() . '/languages' );
	}

	/**
	 * Change Stylesheet Directory.
	 *
	 * @return string
	 */
	public function change_stylesheet_directory() {
		return TOURZE_LITE_DIR . 'gutenverse-files/';
	}

	/**
	 * Initialize Instance.
	 */
	public function init_instance() {
		new Asset_Enqueue();
		new Plugin_Notice();
	}

	/**
	 * Notice Closed
	 */
	public function notice_closed() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tourze-lite_admin_notice' ) ) {
			update_user_meta( get_current_user_id(), 'gutenverse_install_notice', 'true' );
		}
		die;
	}

	/**
	 * Generate Global Font
	 *
	 * @param string $value  Value of the option.
	 *
	 * @return string
	 */
	public function global_header_style( $value ) {
		$theme_name      = get_stylesheet();
		$global_variable = get_option( 'gutenverse-global-variable-font-' . $theme_name );

		if ( empty( $global_variable ) && function_exists( 'gutenverse_global_font_style_generator' ) ) {
			$font_variable = $this->default_font_variable();
			$value        .= \gutenverse_global_font_style_generator( $font_variable );
		}

		return $value;
	}

	/**
	 * Header Font.
	 *
	 * @param mixed $value  Value of the option.
	 *
	 * @return mixed Value of the option.
	 */
	public function default_header_font( $value ) {
		if ( ! $value ) {
			$value = array(
				array(
					'value'  => 'Alfa Slab One',
					'type'   => 'google',
					'weight' => 'bold',
				),
			);
		}

		return $value;
	}

	/**
	 * Alter Default Font.
	 *
	 * @param array $config Array of Config.
	 *
	 * @return array
	 */
	public function default_font( $config ) {
		if ( empty( $config['globalVariable']['fonts'] ) ) {
			$config['globalVariable']['fonts'] = $this->default_font_variable();

			return $config;
		}

		if ( ! empty( $config['globalVariable']['fonts'] ) ) {
			// Handle existing fonts.
			$theme_name   = get_stylesheet();
			$initial_font = get_option( 'gutenverse-font-init-' . $theme_name );

			if ( ! $initial_font ) {
				$result = array();
				$array1 = $config['globalVariable']['fonts'];
				$array2 = $this->default_font_variable();
				foreach ( $array2 as $item ) { // default font.
					$result[ $item['id'] ] = $item;
				}
				foreach ( $array1 as $item ) { // overwrite fonts.
					$result[ $item['id'] ] = $item;
				}
				$fonts = array();
				foreach ( $result as $key => $font ) {
					$fonts[] = $font;
				}
				$config['globalVariable']['fonts'] = $fonts;

				update_option( 'gutenverse-font-init-' . $theme_name, true );
			}
		}

		return $config;
	}

	/**
	 * Default Font Variable.
	 *
	 * @return array
	 */
	public function default_font_variable() {
		return array(
            array (
  'id' => 'gv-font-primary',
  'name' => 'Primary',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '100',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '94',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '72',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '500',
  ),
),array (
  'id' => 'gv-font-secondary',
  'name' => 'Secondary',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '52',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '52',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '46',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-feature',
  'name' => 'Feature',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.2',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '32',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '32',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '28',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-feature-secondary',
  'name' => 'Feature Secondary',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.2',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '32',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '26',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '26',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-meta',
  'name' => 'Meta',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.4',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '14',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '14',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '14',
      ),
    ),
    'font' => 
    array (
      'label' => 'Poppins',
      'value' => 'Poppins',
      'type' => 'google',
    ),
    'weight' => '400',
  ),
),array (
  'id' => 'gv-font-text',
  'name' => 'Text',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.6',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
    ),
    'font' => 
    array (
      'label' => 'Poppins',
      'value' => 'Poppins',
      'type' => 'google',
    ),
    'weight' => '400',
  ),
),array (
  'id' => 'gv-font-text-hero',
  'name' => 'Text Hero',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
    ),
    'font' => 
    array (
      'label' => 'Poppins',
      'value' => 'Poppins',
      'type' => 'google',
    ),
    'weight' => '500',
  ),
),array (
  'id' => 'gv-font-text-small',
  'name' => 'Text Small',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.6',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '14',
      ),
    ),
    'font' => 
    array (
      'label' => 'Poppins',
      'value' => 'Poppins',
      'type' => 'google',
    ),
    'weight' => '400',
  ),
),array (
  'id' => 'gv-font-subheading',
  'name' => 'Subheading',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '22',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '22',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '22',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-button-primary',
  'name' => 'Button Primary',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '20',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '20',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '500',
  ),
),array (
  'id' => 'gv-font-button-secondary',
  'name' => 'Button Secondary',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '18',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '500',
  ),
),array (
  'id' => 'gv-font-form-label',
  'name' => 'Form Label',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '14',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '500',
  ),
),array (
  'id' => 'gv-font-heading-404',
  'name' => 'Heading 404',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '120',
      ),
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '280',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '200',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '700',
  ),
),array (
  'id' => 'gv-font-funfact-number',
  'name' => 'Funfact Number',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
    ),
    'size' => 
    array (
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '48',
      ),
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '50',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-funfact-super',
  'name' => 'Funfact Super',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Mobile' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '46',
      ),
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '50',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-block-post-title',
  'name' => 'Block Post Title',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.3',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '28',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '26',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-heading-cta',
  'name' => 'Heading CTA',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '58',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '48',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-nav-menu',
  'name' => 'Nav Menu',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '16',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),array (
  'id' => 'gv-font-heading-inner-page',
  'name' => 'Heading Inner Page',
  'font' => 
  array (
    'lineHeight' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'em',
        'point' => '1.1',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
      ),
    ),
    'size' => 
    array (
      'Desktop' => 
      array (
        'unit' => 'px',
        'point' => '72',
      ),
      'Tablet' => 
      array (
        'unit' => 'px',
        'point' => '68',
      ),
      'Mobile' => 
      array (
        'unit' => 'px',
        'point' => '48',
      ),
    ),
    'font' => 
    array (
      'label' => 'Host Grotesk',
      'value' => 'Host Grotesk',
      'type' => 'google',
    ),
    'weight' => '600',
  ),
),
		);
	}



	/**
	 * Add Template to Editor.
	 *
	 * @param array $template_files Path to Template File.
	 * @param array $template_type Template Type.
	 *
	 * @return array
	 */
	public function add_template( $template_files, $template_type ) {
		if ( 'wp_template' === $template_type ) {
			$new_templates = array(
				'404',
				'archive',
				'blank-canvas',
				'full-width',
				'index',
				'page',
				'search',
				'single',
				'home'
			);

			foreach ( $new_templates as $template ) {
				$template_files[] = array(
					'slug'  => $template,
					'path'  => $this->change_stylesheet_directory() . "/templates/{$template}.html",
					'theme' => get_template(),
					'type'  => 'wp_template',
					'title' => ucfirst( str_replace( '-', ' ', $template ) ),
				);
			}
		}

		return $template_files;
	}

	/**
	 * Use gutenverse template file instead.
	 *
	 * @param string $template_file Path to Template File.
	 * @param string $theme_slug Theme Slug.
	 * @param string $template_slug Template Slug.
	 *
	 * @return string
	 */
	public function template_path( $template_file, $theme_slug, $template_slug ) {
		switch ( $template_slug ) {
            case '404':
					return $this->change_stylesheet_directory() . '/templates/404.html';
			case 'archive':
					return $this->change_stylesheet_directory() . '/templates/archive.html';
			case 'blank-canvas':
					return $this->change_stylesheet_directory() . '/templates/blank-canvas.html';
			case 'footer':
					return $this->change_stylesheet_directory() . '/parts/footer.html';
			case 'full-width':
					return $this->change_stylesheet_directory() . '/templates/full-width.html';
			case 'header':
					return $this->change_stylesheet_directory() . '/parts/header.html';
			case 'index':
					return $this->change_stylesheet_directory() . '/templates/index.html';
			case 'page':
					return $this->change_stylesheet_directory() . '/templates/page.html';
			case 'search':
					return $this->change_stylesheet_directory() . '/templates/search.html';
			case 'single':
					return $this->change_stylesheet_directory() . '/templates/single.html';
			case 'home':
					return $this->change_stylesheet_directory() . '/templates/home.html';
		}

		return $template_file;
	}

	/**
	 * Register Block Pattern.
	 */
	public function register_block_patterns() {
		new Block_Patterns();
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook_suffix Hook suffix.
	 */
	public function dashboard_scripts( $hook_suffix ) {
		if ( 'appearance_page_tourze-lite-dashboard' !== $hook_suffix && 'admin_page_gutenverse-onboarding-wizard' !== $hook_suffix ) {
			return;
		}
		if ( is_admin() ) {
			// enqueue css.
			
						wp_enqueue_style(
							'tourze-lite-dashboard',
							get_template_directory_uri() . '/assets/css/theme-dashboard.css',
							array(),
							TOURZE_LITE_VERSION
						);
					
		$dashboard_includes = include get_template_directory() . '/assets/dependencies/theme-dashboard.asset.php';
		
						wp_enqueue_script(
							'tourze-lite-dashboard',
							get_template_directory_uri() . '/assets/js/theme-dashboard.js',
							$dashboard_includes["dependencies"],
							TOURZE_LITE_VERSION,
							true
						);
					
		
					wp_enqueue_style(
						'tourze-lite-dashboard-inter-font',
						get_template_directory_uri() . '/assets/fonts/inter/inter.css',
						[],
						null
					);

			wp_enqueue_script('wp-api-fetch');

			wp_localize_script( 'wp-api-fetch', 'GutenThemeConfig', $this->theme_config() );
		}
	}

	/**
	 * Check if plugin is installed.
	 *
	 * @param string $plugin_slug plugin slug.
	 * 
	 * @return boolean
	 */
	public function is_installed( $plugin_slug ) {
		$all_plugins = get_plugins();
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			$plugin_dir = dirname($plugin_file);

			if ($plugin_dir === $plugin_slug) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Register static data to be used in theme's js file
	 */
	public function theme_config() {
		global $pagenow;
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$active_plugins = get_option( 'active_plugins' );
		$plugins = array();
		$installed_plugins = get_plugins();
		$installed_plugin_versions = array();
		foreach ( $active_plugins as $active ) {
			$plugin_name = explode( '/', $active )[0];
			$plugins[]   = $plugin_name;
			$installed_plugin_versions[ $plugin_name ] = isset( $installed_plugins[ $active ] ) ? $installed_plugins[ $active ]['Version'] : '1.0.0';
		}

		$config = array(
			'home_url'      => home_url(),
			'active_plugins'=> $active_plugins,
			'version'       => TOURZE_LITE_VERSION,
			'images'        => get_template_directory_uri() . '/assets/img/',
			'title'         => esc_html__( 'Tourze Lite', 'tourze-lite' ),
			'description'   => esc_html__( 'Tourze Lite is a Travel Agency and Tour Booking WordPress Block Theme designed for businesses that want to create a modern and engaging travel website. Built with full site editing and powered by Gutenverse, this theme provides a flexible and visually appealing platform to showcase tour packages, destinations, and travel experiences with a clean and professional design. Whether you manage a travel agency, tour operator service, or booking platform, this Travel Agency and Tour Booking theme helps you present your offerings effectively. It is ideal for travel consultants, tour guides, adventure organizers, holiday planners, and destination management companies looking to expand their online presence. With responsive layouts, customizable block patterns, and well-structured pages, you can easily highlight itineraries, pricing, testimonials, and booking information. Optimized for performance and ease of use, Tourze Lite allows you to build a professional website that reflects your brand and expertise in Travel Agency and Tour Booking services.', 'tourze-lite' ),
			'pluginTitle'   => esc_html__( 'Plugin Requirement', 'tourze-lite' ),
			'pluginDesc'    => esc_html__( 'This theme require some plugins. Please make sure all the plugin below are installed and activated.', 'tourze-lite' ),
			'note'          => '',
			'note2'         => '',
			'demo'          => '',
			'demoUrl'       => esc_url( 'https://gutenverse.com/demo?name=tourze-lite' ),
			'install'       => '',
			'installText'   => esc_html__( 'Install Gutenverse Plugin', 'tourze-lite' ),
			'activateText'  => esc_html__( 'Activate Gutenverse Plugin', 'tourze-lite' ),
			'doneText'      => esc_html__( 'Gutenverse Plugin Installed', 'tourze-lite' ),
			'dashboardPage' => admin_url( 'themes.php?page=tourze-lite-dashboard' ),
			'logo'          => trailingslashit( get_template_directory_uri() ) . 'assets/img/logo-tourze.webp',
			'slug'          => 'tourze-lite',
			'upgradePro'    => esc_url( 'https://gutenverse.com/pricing' ),
			'supportLink'   => esc_url( 'https://wordpress.org/support/theme/tourze-lite/ ' ),
			'libraryApi'    => esc_url( 'https://gutenverse.com//wp-json/gutenverse-server/v1' ),
			'docsLink'      => esc_url( 'https://gutenverse.com/docs/ ' ),
			'pages'         => array(
				
			),
			'plugins'       => array(
				array(
					'slug'       		=> 'gutenverse',
					'title'      		=> esc_html__( 'Gutenverse', 'tourze-lite' ),
					'short_desc' 		=> esc_html__( 'GUTENVERSE – GUTENBERG BLOCKS AND WEBSITE BUILDER FOR SITE EDITOR, TEMPLATE LIBRARY, POPUP BUILDER, ADVANCED ANIMATION EFFECTS, COMPLETE FEATURE ECOSYSTEM, 45+ FREE USER-FRIENDLY BLOCKS', 'tourze-lite' ),
					'active'    		=> in_array( 'gutenverse', $plugins, true ),
					'installed'  		=> $this->is_installed( 'gutenverse' ),
					'req_version'    	=> '3.4.3',
					'installed_version' => isset( $installed_plugins['gutenverse/gutenverse.php']['Version'] ) ? $installed_plugins['gutenverse/gutenverse.php']['Version'] : '',
					'icons'      		=> array (
  '1x' => 'https://ps.w.org/gutenverse/assets/icon-128x128.gif?rev=3132408',
  '2x' => 'https://ps.w.org/gutenverse/assets/icon-256x256.gif?rev=3132408',
),
					'download_url'      => '',
				),
				array(
					'slug'       		=> 'gutenverse-companion',
					'title'      		=> esc_html__( 'Gutenverse Companion', 'tourze-lite' ),
					'short_desc' 		=> esc_html__( 'A companion plugin designed specifically to enhance and extend the functionality of Gutenverse base themes. This plugin integrates seamlessly with the base themes, providing additional features, customization options, and advanced tools to optimize the overall user experience and streamline the development process.', 'tourze-lite' ),
					'active'    		=> in_array( 'gutenverse-companion', $plugins, true ),
					'installed'  		=> $this->is_installed( 'gutenverse-companion' ),
					'req_version'    	=> '2.0.2',
					'installed_version' => isset( $installed_plugins['gutenverse-companion/gutenverse-companion.php']['Version'] ) ? $installed_plugins['gutenverse-companion/gutenverse-companion.php']['Version'] : '',
					'icons'      		=> array (
  '1x' => 'https://ps.w.org/gutenverse-companion/assets/icon-128x128.png?rev=3162415',
),
					'download_url'      => '',
				),
				array(
					'slug'       		=> 'gutenverse-form',
					'title'      		=> esc_html__( 'Gutenverse Form', 'tourze-lite' ),
					'short_desc' 		=> esc_html__( 'GUTENVERSE FORM – FORM BUILDER FOR GUTENBERG BLOCK EDITOR, MULTI-STEP FORMS, CONDITIONAL LOGIC, PAYMENT, CALCULATION, 15+ FREE USER-FRIENDLY FORM BLOCKS', 'tourze-lite' ),
					'active'    		=> in_array( 'gutenverse-form', $plugins, true ),
					'installed'  		=> $this->is_installed( 'gutenverse-form' ),
					'req_version'    	=> '2.4.3',
					'installed_version' => isset( $installed_plugins['gutenverse-form/gutenverse-form.php']['Version'] ) ? $installed_plugins['gutenverse-form/gutenverse-form.php']['Version'] : '',
					'icons'      		=> array (
  '1x' => 'https://ps.w.org/gutenverse-form/assets/icon-128x128.png?rev=3135966',
),
					'download_url'      => '',
				)
			),
			'assign'        => array(
				
			),
			'dashboardData' => array(
				'lite_template_count' => 11,
'plus_template_count' => 10,
'lite_block_count' => 40,
'plus_block_count' => 80,
'lite_page_count' => 0,
'plus_page_count' => 10,
'lite_pattern_count' => 33,
'plus_pattern_count' => 43
			),
			'lite_plus_type' => 'wporg',
			'pro_preview' => trailingslashit( get_template_directory_uri() ) . 'assets/img/ss-cover-tourze-pro-home.webp',
			'pro_title' => esc_html__('Tourze Pro', 'tourze-lite'),
			'upgrade_required_license' => array('professional','agency','enterprise','ultimate')
		);

		if ( 'themes.php' === $pagenow && isset( $_GET['page'] ) && 'tourze-lite-dashboard' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			$admin_config = array(
				'system' => $this->system_status(),
			);
			$config = array_merge( $config, $admin_config );
		}

		if ( isset( $config['assign'] ) && $config['assign'] ) {
			$assign = $config['assign'];
			foreach ( $assign as $key => $value ) {
				$query = new \WP_Query(
					array(
						'post_type'      => 'page',
						'post_status'    => 'publish',
						'title'          => '' !== $value['page'] ? $value['page'] : $value['title'],
						'posts_per_page' => 1,
					)
				);

				if ( $query->have_posts() ) {
					$post                     = $query->posts[0];
					$page_template            = get_page_template_slug( $post->ID );
					$assign[ $key ]['status'] = array(
						'exists'         => true,
						'using_template' => $page_template === $value['slug'],
					);

				} else {
					$assign[ $key ]['status'] = array(
						'exists'         => false,
						'using_template' => false,
					);
				}

				wp_reset_postdata();
			}
			$config['assign'] = $assign;
		}

		return $config;
	}
	
						/**
						 * System Status.
						 *
						 * @return array
						 */
						public function system_status() {
							$status      = array();
							$active_demo = get_option( 'gutenverse_companion_template_options' );
							/** Themes */
							$theme                    = wp_get_theme();
							$parent                   = wp_get_theme( get_template() );
							$status['theme_name']     = $theme->get( 'Name' );
							$status['theme_version']  = $theme->get( 'Version' );
							$status['is_child_theme'] = is_child_theme();
							$status['parent_theme']   = $parent->get( 'Name' );
							$status['parent_version'] = $parent->get( 'Version' );

							$status['active_companion_demo'] = $active_demo['active_demo'] ?? esc_html__( 'You don\'t have any demo activated', 'tourze-lite' );

							/** WordPress Environment */
							$wp_upload_dir              = wp_upload_dir();
							$status['home_url']         = home_url( '/' );
							$status['site_url']         = site_url();
							$status['login_url']        = wp_login_url();
							$status['wp_version']       = get_bloginfo( 'version', 'display' );
							$status['is_multisite']     = is_multisite();
							$status['wp_debug']         = defined( 'WP_DEBUG' ) && WP_DEBUG;
							$status['memory_limit']     = ini_get( 'memory_limit' );
							$status['wp_memory_limit']  = WP_MEMORY_LIMIT;
							$status['wp_language']      = get_locale();
							$status['writeable_upload'] = wp_is_writable( $wp_upload_dir['basedir'] );
							$status['count_category']   = wp_count_terms( 'category' );
							$status['count_tag']        = wp_count_terms( 'post_tag' );

							/** Server Environment */
							$remote = get_transient( 'gutenverse_wp_remote_get_status_cache' );
							if ( ! $remote ) {
								$remote = wp_remote_get( home_url() );
								set_transient( 'gutenverse_wp_remote_get_status_cache', $remote, 30 * MINUTE_IN_SECONDS );
							}

							$gd_support = array();
							if ( function_exists( 'gd_info' ) ) {
								foreach ( gd_info() as $key => $value ) {
									$gd_support[ $key ] = $value;
								}
							}

							$status['server_info']        = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
							$status['php_version']        = PHP_VERSION;
							$status['post_max_size']      = ini_get( 'post_max_size' );
							$status['max_input_vars']     = ini_get( 'max_input_vars' );
							$status['max_execution_time'] = ini_get( 'max_execution_time' );
							$status['suhosin']            = extension_loaded( 'suhosin' );
							$status['imagick']            = extension_loaded( 'imagick' );
							$status['gd']                 = extension_loaded( 'gd' ) && function_exists( 'gd_info' );
							$status['gd_webp']            = extension_loaded( 'gd' ) && $gd_support['WebP Support'];
							$status['fileinfo']           = extension_loaded( 'fileinfo' ) && ( function_exists( 'finfo_open' ) || function_exists( 'mime_content_type' ) );
							$status['curl']               = extension_loaded( 'curl' ) && function_exists( 'curl_version' );
							$status['wp_remote_get']      = ! is_wp_error( $remote ) && $remote['response']['code'] >= 200 && $remote['response']['code'] < 300;

							/** Plugins */
							$status['plugins'] = $this->data_active_plugin();

							return $status;
						}
						/**
						 * Data active plugin
						 *
						 * @return array
						 */
						public function data_active_plugin() {
							$active_plugin = array();

							$plugins = array_merge(
								array_flip( (array) get_option( 'active_plugins', array() ) ),
								(array) get_site_option( 'active_sitewide_plugins', array() )
							);

							$plugins = array_intersect_key( get_plugins(), $plugins );

							if ( count( $plugins ) > 0 ) {
								foreach ( $plugins as $plugin ) {
									$item                = array();
									$item['uri']         = isset( $plugin['PluginURI'] ) ? esc_url( $plugin['PluginURI'] ) : '#';
									$item['name']        = isset( $plugin['Name'] ) ? $plugin['Name'] : esc_html__( 'unknown', 'tourze-lite' );
									$item['author_uri']  = isset( $plugin['AuthorURI'] ) ? esc_url( $plugin['AuthorURI'] ) : '#';
									$item['author_name'] = isset( $plugin['Author'] ) ? $plugin['Author'] : esc_html__( 'unknown', 'tourze-lite' );
									$item['version']     = isset( $plugin['Version'] ) ? $plugin['Version'] : esc_html__( 'unknown', 'tourze-lite' );

									$content = esc_html__( 'by', 'tourze-lite' );

									$active_plugin[] = array(
										'type'            => 'status',
										'title'           => $item['name'],
										'content'         => $content,
										'link'            => $item['author_uri'],
										'link_text'       => $item['author_name'],
										'additional_text' => $item['version'],
									);
								}
							}

							return $active_plugin;
						}
					
			
						/**
						 * Add Menu
						 */
						public function admin_menu() {
							add_theme_page(
								esc_html__('Tourze Lite Dashboard', 'tourze-lite'),
								esc_html__('Tourze Lite Dashboard', 'tourze-lite'),
								'edit_theme_options',
								'tourze-lite-dashboard',
								array( $this, 'load_dashboard' ),
								1
							);
						}

						/**
						 * Template page
						 */
						public function load_dashboard() {
							?>
								<div id='gutenverse-theme-dashboard'>
								</div>
							<?php
						}
					
}
