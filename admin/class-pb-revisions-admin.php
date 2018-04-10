<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/lukaiser
 * @since      1.0.0
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pb_Revisions
 * @subpackage Pb_Revisions/admin
 * @author     Lukas Kaiser <reg@lukaiser.com>
 */
class Pb_Revisions_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pb-revisions-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pb-revisions-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add admin menu
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		$icon = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgaGVpZ2h0PSIxMDI0IiB3aWR0aD0iODk2IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Ik0wIDcwNGgxMjh2LTY0SDY0VjM4NGg2NHYtNjRIMFY3MDR6TTM4NCAxOTJ2NjQwaDUxMlYxOTJIMzg0ek03NjggNzA0SDUxMlYzMjBoMjU2VjcwNHpNMTkyIDc2OGgxMjh2LTY0aC02NFYzMjBoNjR2LTY0SDE5MlY3Njh6Ii8+PC9zdmc+";
		add_menu_page(__('Revisions', 'pb-revisions'), __('Revisions', 'pb-revisions'), 'edit_posts', 'pb_revisions', array($this, 'render_admin_menu'), $icon, 12);
	}

	/**
	 * Render the admin menu
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function render_admin_menu(){
		$controller = new \PBRevisions\admin\Menu_Page_Controller();
		$controller->actions();
		$controller->render();
	}

}
