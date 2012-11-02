<?php

/**
 * Base class for PressForward RSS modules
 */
class RSSPF_Module {
	var $id;
	var $module_dir;
	var $module_url;

	function start() {
		$this->setup_hooks();
	}

	function setup_hooks() {
		// Once modules are registered, set up some basic module info
		add_action( 'rsspf_setup_modules', array( $this, 'setup_module_info' ) );

		// Run at 15 to make sure the core menu is loaded first
		add_action( 'admin_menu', array( $this, 'setup_admin_menus' ), 15 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );	// There's no admin_enqueue_styles action

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_enqueue_styles',  array( $this, 'wp_enqueue_styles' ) );
		add_action( 'feeder_menu', array( $this, 'add_to_feeder' ) );
		add_filter('dash_widget_bar', array($this, 'add_dash_widgets_filter') );

		add_action( 'module_control', array($this, 'setup_module') );
	}

	/**
	 * Determine some helpful info about this module
	 *
	 * Sets the module ID based on the key used to register the module in
	 * the $rsspf global
	 *
	 * Also sets up the module_dir and module_url for use throughout
	 */
	function setup_module_info() {
		global $rsspf;

		// Determine the ID by checking which module this class belongs to
		$module_class = get_class( $this );
		foreach ( $rsspf->modules as $module_id => $module ) {
			if ( is_a( $module, $module_class ) ) {
				$this->id = $module_id;
				break;
			}
		}

		// If we've found an id, use it to create some paths
		if ( $this->id ) {
			$this->module_dir = trailingslashit( RSSPF_ROOT . '/modules/' . $this->id );
			$this->module_url = trailingslashit( RSSPF_URL . 'modules/' . $this->id );
		}
	}

	function setup_admin_menus( $admin_menus ) {
		foreach ( (array) $admin_menus as $admin_menu ) {
			$defaults = array(
				'page_title' => '',
				'menu_title' => '',
				'cap'        => 'edit_posts',
				'slug'       => '',
				'callback'   => '',
			);
			$r = wp_parse_args( $admin_menu, $defaults );

			// add_submenu_page() will fail if any arguments aren't passed
			if ( empty( $r['page_title'] ) || empty( $r['menu_title'] ) || empty( $r['cap'] ) || empty( $r['slug'] ) || empty( $r['callback'] ) ) {
				continue;
			}

			add_submenu_page( RSSPF_MENU_SLUG, $r['page_title'], $r['menu_title'], $r['cap'], $r['slug'], $r['callback'] );
		}
	}
/**
	function setup_dash_widgets( $dash_widgets ) {
		foreach ( (array) $dash_widgets as $dash_widget ) {
			$defaults = array(
				'widget_title' => '',
				'slug'       => '',
				'callback'   => '',
			);
			$r = wp_parse_args( $dash_widget, $defaults );

			// add_submenu_page() will fail if any arguments aren't passed
			if ( empty( $r['widget_title'] ) || empty( $r['slug'] ) || empty( $r['callback'] ) ) {
				continue;
			}

			//add_action( RSSPF_MENU_SLUG, $r['page_title'], $r['menu_title'], $r['cap'], $r['slug'], $r['callback'] );
		}
	}	
**/	
	// Fetch and return a formatted data object - optional
	function get_data_object() { return array(); }
	
	function pf_add_dash_widgets() {
		$array = array();
		return $array;
	}
	
	function add_dash_widgets_filter($filter_inc_array) {
		$client_widgets = $this->pf_add_dash_widgets();
		$all_widgets = array_merge($filter_inc_array, $client_widgets);
		return $all_widgets;
	}

	// Scripts and styles - optional
	function admin_enqueue_scripts() {}
	function admin_enqueue_styles() {}
	function wp_enqueue_scripts() {}
	function wp_enqueue_styles() {}
	function add_to_feeder() {}

}