<?php
/**
 * Block Pattern Class
 *
 * @author Jegstudio
 * @package tourze-lite
 */
namespace Tourze_Lite;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init Class
 *
 * @package tourze-lite
 */
class Asset_Enqueue {
	/**
	 * Class constructor.
	 */
	public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
	}

    /**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'tourze-lite-style', get_stylesheet_uri(), array(), TOURZE_LITE_VERSION );

				wp_enqueue_style( 'tourze-lite-preset', trailingslashit( get_template_directory_uri() ) . '/assets/css/tourze-lite-preset.css', array(), TOURZE_LITE_VERSION );
		wp_enqueue_script( 'tourze-lite-preset-script', trailingslashit( get_template_directory_uri() ) . '/assets/js/tourze-lite-preset-script.js', array(), TOURZE_LITE_VERSION, true );
		wp_enqueue_style( 'tourze-lite-custom-styling', trailingslashit( get_template_directory_uri() ) . '/assets/css/tourze-lite-custom-styling.css', array(), TOURZE_LITE_VERSION );


        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
    }

	/**
	 * Enqueue admin scripts and styles.
	 */
	public function admin_scripts() {
		
    }
}
