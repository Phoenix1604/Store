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

	//Register Custom post type of store for adding the store details by the admin
	function custom_store_post_type() {

		include_once plugin_dir_path( __FILE__ ) . 'partials/store-custom-metaboxes.php';

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


	// Add meta boxes for store name and location for the admin
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

	//Add choose store option for customers in woocommerce checkout page
	function add_choose_store_section() {
		// Get all "store" custom post type posts
		$stores = get_posts( array(
			'post_type' => 'store',
			'numberposts' => -1,
		) );
	
		// If there are stores, display the "Choose Store" section
		if ( $stores) {
			echo '<div id="choose-store">';
			echo '<label for="pickup-store">Select Store</label>';
			echo '<select name="store_id" id="pickup-store">';
			echo '<option value="">' . __( 'Select a store', 'store' ) . '</option>';
			foreach ( $stores as $store ) {
				$store_id = $store->ID;
				$store_name = get_post_meta( $store_id, 'store_name', true );
				$store_location = get_post_meta( $store_id, 'store_location', true );
				echo '<option value="' . $store_id . '">' . $store_name . ' - ' . $store_location . '</option>';
			}
			echo '</select>';
			echo '<label for="pickup-date">Pickup Date</label>';
			echo '<input type="date" id="pickup-date" name="pickup-date">';
			echo '</div>';
		}
	}

	//Saved the choosed store and pickup date option for the order
	function save_store_to_order_meta( $order ) {
		if ( isset( $_POST['store_id']) && isset($_POST['pickup-date'])) {
			$store_id = sanitize_text_field( $_POST['store_id'] );
			$pickup_date = sanitize_text_field($_POST['pickup-date']);
			$order->update_meta_data( 'store_id', $store_id );
			$order->update_meta_data('pickup_date', $pickup_date);
		}
	}


	//Display the choosed store and pickup date on order confirmation page
	function display_store_information_on_confirmation_page( $order_id ) {
		// Get the order object
		$order = wc_get_order( $order_id );
	
		// Get the store ID from the order metadata
		$store_id = $order->get_meta( 'store_id' );
	
		// If a store ID is set, display the store information
		if ( $store_id ) {
			// Get the store name and location from the store custom post type
			$store_name = get_post_meta( $store_id, 'store_name', true );
			$store_location = get_post_meta( $store_id, 'store_location', true );
	
			// Get the pickup date from the order metadata
			$pickup_date = $order->get_meta( 'pickup_date' );
	
			// Display the store information and pickup date on the thank you page
			echo '<section class="wdmstore-woocommerce-storedetails">';
			echo '<h2 class="wdmstore-woocommerce-storedetails__title">' . __( 'Store Information', 'store' ) . '</h2>';
			echo '<p><strong>' . __( 'Store Name', 'your-textdomain' ) . ':</strong> ' . $store_name . '</p>';
			echo '<p><strong>' . __( 'Store Location', 'your-textdomain' ) . ':</strong> ' . $store_location . '</p>';
			echo '<p><strong>' . __( 'Pickup Date', 'your-textdomain' ) . ':</strong> ' . $pickup_date . '</p>';
			echo '</section>';
		}
	}

	//Display the choosed store and pickup date on ordert confirmation mail
	function display_store_information_in_order_email( $order, $sent_to_admin, $plain_text ) {
		// Get the store ID from the order metadata
		$store_id = $order->get_meta( 'store_id' );
	
		// If a store ID is set, display the store information
		if ( $store_id ) {
			// Get the store name and location from the store custom post type
			$store_name = get_post_meta( $store_id, 'store_name', true );
			$store_location = get_post_meta( $store_id, 'store_location', true );
	
			// Get the pickup date from the order metadata
			$pickup_date = $order->get_meta( 'pickup_date' );
	
			// Display the store information in the order meta section of the email
			echo '<h2>' . __( 'Store Information', 'store' ) . '</h2>';
			echo '<ul>';
			echo '<li><strong>' . __( 'Store Name', 'store' ) . ':</strong> ' . $store_name . '</li>';
			echo '<li><strong>' . __( 'Store Location', 'store' ) . ':</strong> ' . $store_location . '</li>';
			echo '<li><strong>' . __( 'Pickup Date', 'store' ) . ':</strong> ' . $pickup_date . '</li>';
			echo '</ul>';
		}
	}


	// Display store information in WooCommerce admin orders section

	function display_store_information_in_admin_order( $order ) {
		// Get the store ID from the order metadata
		$store_id = $order->get_meta( 'store_id' );

		// If a store ID is set, display the store information
		if ( $store_id ) {
			// Get the store name and location from the store custom post type
			$store_name = get_post_meta( $store_id, 'store_name', true );
			$store_location = get_post_meta( $store_id, 'store_location', true );

			// Get the pickup date from the order metadata
			$pickup_date = $order->get_meta( 'pickup_date' );
			
			// Display the store information and pickup date in the order details section
			echo '<p><strong>' . __( 'Store Name', 'store' ) . ':</strong> ' . $store_name . '</p>';
			echo '<p><strong>' . __( 'Store Location', 'store' ) . ':</strong> ' . $store_location . '</p>';
			echo '<p><strong>' . __( 'Pickup Date', 'store' ) . ':</strong> ' . $pickup_date . '</p>';
		}
	}


	//Register ready to pickup status option for orders
	function register_readytopickup_status() {
		register_post_status( 'wc-ready-to-pickup', array(
			'label'                     => _x( 'Ready To Pickup', 'Order status', 'store' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Ready To Pickup <span class="count">(%s)</span>', 'Ready To Pickup<span class="count">(%s)</span>', 'store' )
		) );
	}

	//Add ready to pickup with other statuses
	function add_ready_to_pickup_order_status($order_statuses)
	{
		$order_statuses['wc-ready-to-pickup'] = __('Ready to Pickup', 'Order status','store');
		return $order_statuses;
	}

	//Update order status in database
	function update_order_status_in_database($order_id, $old_status, $new_status, $order)
	{
		if ($new_status == 'ready-to-pickup' && $old_status != 'ready-to-pickup') {
			global $wpdb;
			$table_name = $wpdb->prefix . 'posts';
			$wpdb->update($table_name, array('post_status' => 'wc-ready-to-pickup'), array('ID' => $order_id));
		}
		return $new_status;
	}

	function add_custom_columns_to_orders_page( $columns ) {
		// Add store details and pickup date columns
		$columns['store_details'] = __( 'Store Details', 'store' );
		$columns['pickup_date'] = __( 'Pickup Date', 'store' );
		return $columns;
	}

	function populate_custom_columns_with_data( $column ) {
		global $post;
		$order_id = $post->ID;
		$order = wc_get_order( $order_id );
	
		// Get the store ID from the order metadata
		$store_id = $order->get_meta( 'store_id' );
	
		if ( $column === 'store_details' ) {
			
			if ( $store_id ) {
				// Get the store name and location from the store custom post type
				$store_name = get_post_meta( $store_id, 'store_name', true );
				$store_location = get_post_meta( $store_id, 'store_location', true );
	
				
				echo '<p><strong>' . __( 'Store Name', 'store' ) . ':</strong> ' . $store_name . '</p>';
				echo '<p><strong>' . __( 'Store Location', 'store' ) . ':</strong> ' . $store_location . '</p>';
			}
		}
	
		if ( $column === 'pickup_date' ) {
			// Get the pickup date from the order metadata
			$pickup_date = $order->get_meta( 'pickup_date' );
			echo $pickup_date;
		}
	}

	function send_next_day_pickup_emails(){
		$args = array(
			'post_type' => 'shop_order',
			'posts_per_page' => '-1',
			'post_status' => 'any'
		  );
	  
		$query = new WP_Query($args);
		$posts = $query->posts;

		// Calculate next day
		$next_day = strtotime( '+1 day', current_time( 'timestamp' ) );
		$next_day_date = date( 'Y-m-d', $next_day );

		foreach ( $posts as $post ) {
			// Get order and customer details
			$order_id = $post->ID;
			$order = wc_get_order( $order_id );
			$shipping_method = @array_shift($order->get_shipping_methods());
			$shipping_method_id = $shipping_method['method_id'];
			$pickup_date = $order->get_meta( 'pickup_date' );
	
			if($shipping_method_id == 'local_pickup' && strtotime($pickup_date) == strtotime($next_day_date)){
				$customer_email = $order->get_billing_email();
				$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

				$subject = 'Your order pickup is tomorrow!';
				$message = "Dear {$customer_name},\n\n";
				$message .= "This is a reminder that your order with order number #{$order->get_order_number()} is scheduled for pickup tomorrow, on " . $pickup_date . ".\n";
				$message .= "Thank you for choosing our store!\n\n";
				
				$headers = 'Content-Type: text/plain; charset=utf-8';
				wp_mail( $customer_email, $subject, $message, $headers );
				
			}
		}
	}
}
