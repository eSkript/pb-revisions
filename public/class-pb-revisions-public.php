<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/lukaiser
 * @since      1.0.0
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/public
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */
class Pb_Revisions_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		self::register_tables();
		$this->add_shortcodes();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pb_Revisions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pb_Revisions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pb-revisions-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pb_Revisions_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pb_Revisions_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pb-revisions-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register shortcodes
	 *
	 * @since    1.0.0
	 */
	public function add_shortcodes() {
		add_shortcode( 'version', array( $this, 'handle_version_shortcode') );
		add_shortcode( 'publish-date', array( $this, 'handle_publish_date_shortcode') );
	}

	/**
	 * Handle Version Shortcode
	 *
	 * @since    1.0.0
	 */
	public function handle_version_shortcode($atts) {
		$a = shortcode_atts( array(
			'working_version_title' => "Working Version" //TODO
		), $atts );

		$store = new \PBRevisions\includes\Store();
		$v = $store->get_active_version_number();
		if(!empty($v)){
			return esc_html( $v );
		}else{
			return esc_html( $a['working_version_title'] );
		}
	}

	/**
	 * Handle Publish Date Shortcode
	 *
	 * @since    1.0.0
	 */
	public function handle_publish_date_shortcode() {
		$store = new \PBRevisions\includes\Store();
		$v = $store->get_active_version();
		if(!empty($v)){
			$date = $v->date;
		}else{
			$date = current_time( 'mysql', 1 );
		}
		$date_format = get_option( 'date_format' );
		return get_date_from_gmt($date, $date_format);
	}

	/**
	 * Register Tables
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public static function register_tables(){
		global $wpdb;
  
		if ( !in_array( 'pb_revisions_version', $wpdb->tables, true ) ) {
			$wpdb->pb_revisions_version = $wpdb->prefix . 'pb_revisions_version';
			$wpdb->tables[] = 'pb_revisions_version';
		}

		if ( !in_array( 'pb_revisions_chapter', $wpdb->tables, true ) ) {
			$wpdb->pb_revisions_chapter = $wpdb->prefix . 'pb_revisions_chapter';
			$wpdb->tables[] = 'pb_revisions_chapter';
		}
		
		
	}

}
