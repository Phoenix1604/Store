<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Store
 * @subpackage Store/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Store
 * @subpackage Store/admin
 * @author     Subhajit Bera <subhajit.bera@wisdmlabs.com>
 */
class Store_Admin {

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
		 * defined in Store_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Store_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/store-admin.css', array(), $this->version, 'all' );

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
		 * defined in Store_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Store_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/store-admin.js', array( 'jquery' ), $this->version, false );

	}

	function custom_store_post_type() {

		include_once plugin_dir_path( __FILE__ ) . 'partials/store-custome-metaboxes.php';

		$labels = array(
			'name' => __('Stores', 'Post Type General Name', 'store'),
			'singular_name' => __('Store', 'Post Type Singular Name', 'store'),
			'menu_name' => __('Stores', 'store'),
			'add_new' => 'Add New',
			'add_new_item' => __('Add New Item', 'store'),
			'edit_item' => __('Edit Store', 'store'),
			'new_item' =>  __('New Store', 'store'),
			'all_items' => __('All Stores', 'store'),
			'view_item' => __('View Store', 'store'),
			'view_items' => __('View Stores', 'store'),
			'search_items' => 'Search Stores',
			'not_found' =>  'No stores found',
			'not_found_in_trash' => 'No stores found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'Stores'
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'show_in_nav_menus' => true,
			'has_archive' => true,
			'menu_position' => 20,
			'menu_icon' => 'dashicons-cart',
			'supports' => array( 'title', 'author'),
			'rewrite' => array( 'slug' => 'store' ),
			'capability_type' => 'post',
			'register_meta_box_cb' => 'add_store_meta_boxes'
		);
		register_post_type( 'store', $args );
	}

	function save_store_meta_box_values( $post_id , $post ) {
        // // Check if nonce is set
        if (!isset( $_POST['store_location_nonce']) || !wp_verify_nonce($_POST['store_location_nonce'], 'store_meta_box_location')) {
			return $post_id;
		}
    
		if (!isset( $_POST['store_name_nonce']) || !wp_verify_nonce($_POST['store_name_nonce'], 'store_meta_box_name')) {
			return $post_id;
		}
        
        // Check if user has permissions to save data
        if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
         //Save store location & Name value
        $store_name = sanitize_text_field( $_POST['store_name'] );
		$store_location = sanitize_text_field( $_POST['store_location'] );
		update_post_meta( $post_id, 'store_name', $store_name );
		update_post_meta( $post_id, 'store_location', $store_location );

    }

}
