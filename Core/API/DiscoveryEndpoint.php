<?php
namespace PressForward\Core\API;

use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;

use PressForward\Controllers\Metas;
use WP_REST_Posts_Controller;

use WP_Ajax_Response;

class DiscoveryEndpoint extends WP_REST_Posts_Controller implements HasActions {

	protected $basename;

	function __construct( Metas $metas, $vendor, $api_version ){
		$this->namespace = $vendor.'/'.$api_version;
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


}