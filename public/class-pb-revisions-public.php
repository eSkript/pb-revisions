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
	 * Add endpoints
	 *
	 * @since    1.0.0
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( 'revisions', EP_ROOT );//TODO flush_rewrite_rules but where
	}

	/**
	 * Register query_vars
	 *
	 * @since    1.0.0
	 */
	public function add_query_vars( $query_vars ){
		$query_vars[] = 'revisions';
		return $query_vars;
	}

	/**
	 * Handle Revisions Detail Page
	 *
	 * @since    1.0.0
	 */
	public function handle_revisions_detail_page( &$wp ){
		if ( array_key_exists( 'revisions', $wp->query_vars ) ) {
			if (locate_template( array( 'revisions-page.php' ) ) != '') {
				// yep, load the page template
				get_template_part('revisions-page');
			} else {
				// nope, load the content
				require( plugin_dir_path( __FILE__ ) . 'partials/revisions-page.php' );
			}
			exit();
		}
		return;
	}

	/**
	 * Register shortcodes
	 *
	 * @since    1.0.0
	 */
	public function add_shortcodes() {
		add_shortcode( 'version', array( $this, 'handle_version_shortcode') );
		add_shortcode( 'publish-date', array( $this, 'handle_publish_date_shortcode') );
		add_shortcode( 'revisions', array( $this, 'handle_revisions_shortcode') );
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
		if($this->is_export()){
			$v = $store->get_active_export_version_number();
		}else{
			$v = $store->get_active_version_number();
		}
		
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
		
		if($this->is_export()){
			$v = $store->get_active_export_version();
		}else{
			$v = $store->get_active_version();
		}

		if(!empty($v)){
			$date = $v->date;
		}else{
			$date = current_time( 'mysql', 1 );
		}
		$date_format = get_option( 'date_format' );
		return get_date_from_gmt($date, $date_format);
	}

	/**
	 * Handle Revisions Shortcode
	 *
	 * @since    1.0.0
	 */
	public function handle_revisions_shortcode() {
		ob_start();
			if (locate_template( array( 'revisions-shortcode.php' ) ) != '') {
				// yep, load the page template
				get_template_part('revisions-shortcode');
			} else {
				// nope, load the content
				require( plugin_dir_path( __FILE__ ) . 'partials/revisions-shortcode.php' );
			}
		return ob_get_clean();
	}

	/**
	 * Change Tables
	 *
	 * @since    1.0.0
	 */
	public function change_tables() {
		$this->switch_to_right_table_names();
	}

	/**
	 * Change Tables Back if Preview
	 *
	 * @since    1.0.0
	 */
	public function change_tables_back() {
		$this->switch_to_right_table_names();
	}

	/**
	 * Blog switched
	 *
	 * @since    1.0.0
	 */
	public function blog_switched($id) {
		$this->switch_to_right_table_names();
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

	/**
	 * Should a Revisioned Version be shown
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return	boolean
	 */
	private function show_revisioned_version(){
		global $wp;
		if(is_admin() && !isset($_POST['export_formats'])) return false;
		if(isset($wp) && is_array($wp->query_vars) && array_key_exists( 'preview', $wp->query_vars ) && current_user_can( "edit_posts" )) return false;
		return true;
	}

	/**
	 * Switch to right table names
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return	boolean
	 */
	private function switch_to_right_table_names(){
		global $wpdb;
		$store = new \PBRevisions\includes\Store();
		if($this->show_revisioned_version()){
			global $wp;
			if($this->is_export()){
				$v = $store->get_active_export_version_number();
			}else{
				$v = $store->get_active_version_number();
			}
		}else{
			$v = false;
		}
		$wpdb->posts = esc_sql($store->posts_table_name($v));
		$wpdb->postmeta = esc_sql($store->postmeta_table_name($v));
	}

	/**
	 * Is Export
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return	boolean
	 */
	private function is_export(){
		if(is_admin() && isset($_POST['export_formats']) && current_user_can( "edit_posts" )) return true;
		global $wp;
		$exporter = new \Pressbooks\Modules\Export\WordPress\Wxr(array());
		$timestamp = absint( @$_REQUEST['timestamp'] );
		$hashkey = @$_REQUEST['hashkey'];
		if(isset($wp) && is_array($wp->query_vars) && array_key_exists( 'format', $wp->query_vars ) && $this->verifyNonce( $timestamp, $hashkey )) return true;
		return false;
	}

}
