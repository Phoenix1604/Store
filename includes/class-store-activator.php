<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Store
 * @subpackage Store/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Store
 * @subpackage Store/includes
 * @author     Subhajit Bera <subhajit.bera@wisdmlabs.com>
 */
class Store_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$now = time();
    	$scheduled_time = strtotime( '06:00:00', $now );
    	// Schedule the event to run every day at 6am 
    	if ( ! wp_next_scheduled( 'wdm_store_sendmail_for_pickup' ) ) {
        	wp_schedule_event( $scheduled_time, 'daily', 'wdm_store_sendmail_for_pickup' );
    	}
	}

}
