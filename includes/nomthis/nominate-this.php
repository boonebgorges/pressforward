<?php
/**
 * Nominate This Display and Handler.
 *
 * Based on the PressThis code.
 */

 //Orig. file called from wp-admin/ by the bookmarklet.

define('IFRAME_REQUEST' , true);
define('WP_ADMIN', false);
global $pagenow;
$wp_bootstrap = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))) );
#echo '<pre>'; var_dump($_POST); die();
$wp_bootstrap_d = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))) ));

if (is_dir($wp_bootstrap.'/wp-admin')){
   $wp_bootstrap = $wp_bootstrap.'/wp-admin';
} elseif (is_dir($wp_bootstrap.'/wordpress/wp-admin')){
   $wp_bootstrap = $wp_bootstrap.'/wordpress/wp-admin';
} elseif (is_dir($wp_bootstrap_d.'/wordpress/wp-admin')) {
	$wp_bootstrap = $wp_bootstrap_d.'/wordpress/wp-admin';
} elseif (is_dir($wp_bootstrap.'/data/current/wp-admin')) {
	$wp_bootstrap = $wp_bootstrap.'/data/current/wp-admin';
} else {
	echo 'Base directory attempt at: <pre>'; var_dump($wp_bootstrap);
  	echo 'Nominate This can not find your WP-Admin directory'; die();
}

/** WordPress Administration Bootstrap */
require_once( $wp_bootstrap . '/admin.php');
	//PF Correction - this will need to be changed to a constant later.
//require_once( dirname(dirname(dirname(__FILE__))) . "/lib/OpenGraph.php");
//	global $pf_nt;
//	$pf_nt = new PressForward();

header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( get_post_type_object( 'post' )->cap->edit_posts ) )
	wp_die( __( 'Cheatin&#8217; uh?', 'pf' ) );


if (4.2 > get_bloginfo('version')){
	require_once('nominate-this-old.php');
	exit;
}
if ( empty( $GLOBALS['pf_nom_this'] ) ) {
	require_once('class-pf-nom-this.php');
}

$GLOBALS['pf_nom_this']->html();