<?php

namespace PressForward\Controllers;

use PressForward\Interfaces\Items as Items;

class PF_to_WP_Posts implements Items {

	public function insert_post( $post, $error ){
		return wp_insert_post( $post, $error );
	}

	public function update_post( $post, $error ){
		return wp_update_post( $post, $error );
	}

	public function delete_post( $postid, $force_delete = false ){
		return wp_delete_post( $postid, $force_delete );
	}

	public function get_post(  $post = null, $output = OBJECT, $filter = 'raw' ){
		return get_post( $post, $output, $filter );
	}

	public function get_posts( $query ){
		return get_posts( $query );
	}

	public function is_error( $post ){
		return is_wp_error( $post );
	}

}
