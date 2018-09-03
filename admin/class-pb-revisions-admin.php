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
	 * Reorder Menu
	 *
	 * @since    1.0.0
	 */
	public function reorder_admin_menu($menu_order){
		if ($key = array_search ( 'pb_export' , $menu_order )){
			$menu_order2 = array_splice($menu_order, $key);
			$menu_order[] = "pb_revisions";
			$menu_order = array_merge($menu_order, $menu_order2);
		}
		return $menu_order;
	}

	/**
	 * Return True
	 *
	 * @since    1.0.0
	 */
	public function return_true(){
		return true;
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

	/**
	 * Add version selecter UI to export page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_version_selecter_ui(){
		$store = new \PBRevisions\includes\Store();
		$versions = $store->get_versions();
		$active_version = $store->get_active_export_version_number();
		$active_web_version = $store->get_active_version_number();
		echo '<div class="clear"></div>';
		echo '<h3>'.__('Version', 'pb-revisions').'</h3>';
		_e('<p>Select which version you want to export</p>', 'pb-revisions');
		echo '<div class="clear">';
		echo '<select name="pb_revisions_version">';
		echo '<option value="working">'.__('Preview', 'pb-revisions').'</option>';
		foreach(array_reverse($versions) as $version){
			if(!$version->draft){
				$selected = $active_version==$version->number ? ' selected' : '';
				$active_web = $active_web_version==$version->number ? '*' : '';
				echo '<option value="'.esc_attr($version->ID).'"'.$selected.'>'.esc_html($version->number).$active_web.'</option>';
			}
		}
	  	echo '</select>';
		echo '</div>';
	}

	/**
	 * Change Export Version When Exporting
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function change_export_version_when_exporting(){
		if(isset($_GET['export']) && $_GET['export'] == "yes" && isset($_POST['pb_revisions_version'])){
			$controller = new \PBRevisions\admin\Menu_Page_Controller();
			if($controller->allowed()){
				$controller->action_activate_export_version();
			}
		}
	}

	/**
	 * Show Files in Export
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function pb_export_show_files(){
		require( plugin_dir_path( __FILE__ ) . 'partials/pb-revisions-export-file-section.php' );
		return false;
	}

	/**
	 * Removes wp_admin_bar_edit_menu view links and replaces them with previewlinks
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_edit_menu_replace( $wp_admin_bar ){
		if ( is_admin() ) {
			$current_screen = get_current_screen();
			$post           = get_post();
	
			if ( 'post' == $current_screen->base
				&& 'add' != $current_screen->action
				&& ( $post_type_object = get_post_type_object( $post->post_type ) )
				&& current_user_can( 'read_post', $post->ID )
				&& ( $post_type_object->public )
				&& ( $post_type_object->show_in_admin_bar ) ) {
				if ( 'draft' != $post->post_status ) {
					$wp_admin_bar->remove_menu('view');
					$preview_link = get_preview_post_link( $post );
					$wp_admin_bar->add_menu(
						array(
							'id'    => 'preview',
							'title' => $post_type_object->labels->view_item,
							'href'  => esc_url( $preview_link ),
							'meta'  => array( 'target' => 'wp-preview-' . $post->ID ),
						)
					);
				}
			}
		}
	}

	/**
	 * Replace Post Type messages with Previewlink
	 * 
	 * \Pressbooks\PostType\post_type_messages
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param Array $messages
	 */

	public function post_type_messages_replace($messages){
		global $post;

		$permalink = esc_url(get_permalink( $post ));
		$permalinkPreview = esc_url(get_preview_post_link( $post ));

		return  array_map(
			function($arr) use ($permalink, $permalinkPreview) {
				return str_replace($permalink, $permalinkPreview, $arr);
			},
			$messages
		);
	}

	/**
	 * Delete The Book Object Cache if exporting within the init hook
	 * 
	 * \Pressbooks\PostType\delete_book_object_Cache
	 *
	 * @since    1.0.0
	 * @access   public
	 */

	public function delete_book_object_Cache(){
		if(isset($_GET['export']) && $_GET['export'] == "yes" && isset($_POST['pb_revisions_version'])){
			\Pressbooks\Book::deleteBookObjectCache();
		}
	}

}
