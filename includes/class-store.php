<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Store
 * @subpackage Store/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Store
 * @subpackage Store/includes
 * @author     Subhajit Bera <subhajit.bera@wisdmlabs.com>
 */
class Store {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Store_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'STORE_VERSION' ) ) {
			$this->version = STORE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'store';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Store_Loader. Orchestrates the hooks of the plugin.
	 * - Store_i18n. Defines internationalization functionality.
	 * - Store_Admin. Defines all hooks for the admin area.
	 * - Store_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-store-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-store-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-store-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-store-public.php';

		$this->loader = new Store_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Store_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Store_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Store_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action('init', $plugin_admin, 'custom_store_post_type');
		$this->loader->add_action( 'save_post_store', $plugin_admin,'save_store_meta_box_values', 10, 2 );
		$this->loader->add_filter('manage_edit-store_columns', $plugin_admin, 'add_custom_store_columns');
		$this->loader->add_action('manage_store_posts_custom_column', $plugin_admin,'add_custom_store_columns_content', 10, 2);
		$this->loader->add_filter('manage_edit-store_sortable_columns', $plugin_admin, 'make_custom_columns_sortable');


		$this->loader->add_action( 'woocommerce_review_order_before_payment', $plugin_admin, 'add_choose_store_section' );
		$this->loader->add_action('woocommerce_checkout_create_order', $plugin_admin, 'save_store_to_order_meta' );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_admin, 'display_store_information_on_confirmation_page' );
		$this->loader->add_action( 'woocommerce_view_order', $plugin_admin, 'display_store_information_on_confirmation_page' );
		$this->loader->add_action( 'woocommerce_email_order_meta', $plugin_admin, 'display_store_information_in_order_email', 10, 3 );

		$this->loader->add_action( 'init', $plugin_admin, 'register_readytopickup_status' );
		$this->loader->add_filter('wc_order_statuses', $plugin_admin, 'add_ready_to_pickup_order_status');
		$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_admin, 'update_order_status_in_database', 10, 4 );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_shipping_address',  $plugin_admin,'display_store_information_in_admin_order', 10, 1 );
		$this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'add_custom_columns_to_orders_page' );
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'populate_custom_columns_with_data' );
		$this->loader->add_action( 'wdm_store_sendmail_for_pickup', $plugin_admin, 'send_next_day_pickup_emails' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Store_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Store_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
