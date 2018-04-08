<?php

namespace WeDevs\PM\Core\WP;

use WeDevs\PM\Core\WP\Menu;
use WeDevs\PM\Core\Upgrades\Upgrade;
use WeDevs\PM\Core\Notifications\Notification;
use WeDevs\PM\Core\WP\Register_Scripts;
use WeDevs\PM\Core\WP\Enqueue_Scripts as Enqueue_Scripts;
use WeDevs\PM\Core\File_System\File_System as File_System;
use WeDevs\PM\Core\Cli\Commands;

class Frontend {

	/**
     * Constructor for the PM class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
	public function __construct() {	
		$this->includes();
		
		// instantiate classes
        $this->instantiate();

		// Initialize the action hooks
        $this->init_actions();

        // Initialize the action hooks
        $this->init_filters();
	}

	/**
	 * All actions
	 * 
	 * @return void
	 */
	public function init_actions() {
		add_action( 'admin_menu', array( new Menu, 'admin_menu' ) );
        add_action( 'wp_ajax_pm_ajax_upload', array ( new File_System, 'ajax_upload_file' ) );
		add_action( 'init', array ( 'WeDevs\PM\Core\Notifications\Notification' , 'init_transactional_emails' ) );
		add_action( 'admin_enqueue_scripts', array ( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array ( $this, 'register_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}



	function load_plugin_textdomain() {
		load_plugin_textdomain( 'pm', false, basename( dirname( __FILE__ , 3) ) . '/languages/' );
	}

	public function includes() {
		//if ( ! wp_next_scheduled( 'pm_test_schedule' ) ) {
			//wp_schedule_event(time(), 'pm_schedule', 'pm_test_schedule');
		//}

		// cli command
        if ( defined('WP_CLI') && WP_CLI ) {
        	$file = config( 'frontend.patch' ) . '/core/cli/Commands.php';
        	
        	//if ( file_exists( $file ) ) {
        		new Commands();
        	//}
        }
	}

	/**
	 * All filters
	 * 
	 * @return void
	 */
	public function init_filters() {
		
	}

	function cron_interval( $schedules ) {
		// Adds every 5 minutes to the existing schedules.
		$schedules[ 'pm_schedule' ] = array(
			'interval' => MINUTE_IN_SECONDS * 1,
			'display'  => sprintf( __( 'Every %d Minutes PM schedule' ), 1 ),
		);

		return $schedules;
	}

	/**
	 * instantiate classes
	 * 
	 * @return void
	 */
	public function instantiate() {
        Notification::init_transactional_emails();
        new Upgrade();
	}

	public function register_scripts() {
		Register_Scripts::scripts();
		Register_Scripts::styles();
	}
}