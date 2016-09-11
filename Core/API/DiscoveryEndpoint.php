<?php
namespace PressForward\Core\API;

use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;

use PressForward\Controllers\Metas;

use WP_REST_Controller;
use WP_REST_Server;

use WP_Ajax_Response;

class DiscoveryEndpoint extends WP_REST_Controller implements HasActions {

	protected $basename;

	function __construct( $metas, $vendor, $api_version ){
		$this->version = 'v1';
		$this->namespace = 'pf';
		//$this->version = $api_version;
		//$this->namespace = $vendor.'/'.$api_version;
	}


	public function action_hooks() {
		$actions = array(
			array(
				'hook' => 'rest_api_init',
				'method' => 'register_rest_namespace',
			)
		);
		return $actions;
	}

	public function register_rest_namespace(){
		register_rest_route( $this->namespace, '/', array( 'test' => 'go' ) );
	}

	public function register_routes() {
	$version = $this->version;
	$namespace = $this->namespace;
	$base = 'route';
	register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => array(

				),
			)
		));
	}


}