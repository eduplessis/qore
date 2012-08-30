<?php

add_action( 'after_setup_theme', 'qore_constants' );

add_action( 'after_setup_theme', 'qore_functions' );

add_action( 'after_setup_theme', 'qore_extensions' );

add_action( 'after_setup_theme', 'qore_hooks' );

add_action( 'after_setup_theme', 'qore_filters' );

add_action( 'template_redirect', 'qore_enqueues' , 9);

add_action( 'admin_init', 'qore_version_init' );


function qore_version_init() {

	$qore_version = '0.5.3';

	if ( get_option( 'qore_version' ) <> $qore_version )
		update_option( 'qore_version', $qore_version );


}


function qore_constants() {

	/* Defining constant for qore */
	define( 'QORE_LIBRARY_URI', trailingslashit( get_template_directory_uri() ) . 'library/' );
	define( 'QORE_JS_PATH', trailingslashit( QORE_LIBRARY_URI ) . 'js/' );
	define( 'QORE_CSS_PATH', trailingslashit( QORE_LIBRARY_URI ) . 'css/' );
	define( 'QORE_IMAGES_PATH', trailingslashit( QORE_LIBRARY_URI ) . 'images/' );
	define( 'QORE_LIBRARY_ROOT', trailingslashit( get_template_directory() ) . 'library/' );

	/* Defining constant for the template */
	define( 'THEME_URI', trailingslashit( get_stylesheet_directory_uri() ) );
	define( 'THEME_JS_PATH', trailingslashit( THEME_URI ) . 'js/' );
	define( 'THEME_CSS_PATH', trailingslashit( THEME_URI ) . 'css/' );
	define( 'THEME_IMAGES_PATH', trailingslashit( THEME_URI ) . 'images/' );
}

function qore_extensions() {
	if( function_exists( 'icl_get_home_url' ) ){
		require_once ( QORE_LIBRARY_ROOT . 'extensions/wpml-integration.php');
	}
}

function qore_functions() {
//	require_once ( QORE_LIBRARY_ROOT . 'functions/theme-options.php');           // We don't need Admin page for now
	require_once ( QORE_LIBRARY_ROOT . 'functions/menus.php');
	require_once ( QORE_LIBRARY_ROOT . 'functions/qore-cleanup.php');
	require_once ( QORE_LIBRARY_ROOT . 'functions/seo.php');
}

function qore_hooks() {

	/* Defining list of hooks */

	function qore_before_doctype() {
		do_action( 'qore_before_doctype' );
	}

	function qore_before_container() {
		do_action( 'qore_before_container' );
	}

	function qore_before_header() {
		do_action( 'qore_before_header' );
	}

	function qore_inside_header() {
		do_action( 'qore_inside_header' );
	}

	function qore_after_header() {
		do_action( 'qore_after_header' );
	}

	function qore_begining_section() {
		do_action( 'qore_begining_section' );
	}

	function qore_closing_section() {
		do_action( 'qore_closing_section' );
	}

	function qore_before_footer() {
		do_action( 'qore_before_footer' );
	}

	function qore_inside_footer() {
		do_action( 'qore_inside_footer' );
	}

	function qore_after_footer() {
		do_action( 'qore_after_footer' );
	}

	function qore_after_container() {
		do_action( 'qore_after_container' );
	}

}

function qore_filters() {

	/* Defining list of filters */

	function get_qore_mainMenu($args='') {
		$defaults = array( 'theme_location' => 'mainMenu', 'container' => 'nav', 'container_id' => 'mainMenu', 'echo' => FALSE );
		$args = apply_filters( 'get_qore_mainMenu_args', $args );
		$qore_mainMenu = wp_parse_args( $args, $defaults );

		$qore_mainMenu = wp_nav_menu( $qore_mainMenu );
		return apply_filters( 'get_qore_mainMenu', $qore_mainMenu );
	}

	function get_qore_topMenu($args='') {
		$defaults = array( 'theme_location' => 'topMenu', 'container' => 'nav', 'container_id' => 'topMenu', 'echo' => FALSE );
		$args = apply_filters( 'get_qore_topMenu_args', $args );
		$qore_topMenu = wp_parse_args( $args, $defaults );

		$qore_topMenu = wp_nav_menu($qore_topMenu);
		return apply_filters( 'get_qore_topMenu', $qore_topMenu );
	}

	function get_qore_footerMenu($args='') {
		$defaults = array( 'theme_location' => 'footerMenu', 'container' => 'nav', 'container_id' => 'footerMenu', 'echo' => FALSE );
		$args = apply_filters( 'get_qore_footerMenu_args', $args );
		$qore_footerMenu = wp_parse_args( $args, $defaults );

		$qore_footerMenu = wp_nav_menu( $qore_footerMenu );
		return apply_filters( 'get_qore_footerMenu', $qore_footerMenu );
	}

	function get_qore_siteName() {
		$qore_siteName = '<h1 class="siteName"><a href="' . get_home_url() . '">' . get_bloginfo( 'name' ) . '</a></h1>';
		return apply_filters( 'get_qore_siteName', $qore_siteName );
	}

	function get_qore_header() {
		$qore_header = get_qore_siteName();
		$qore_header .= get_qore_topMenu();
		$qore_header .= get_qore_mainMenu();

		return apply_filters( 'get_qore_header', $qore_header );
	}

}

function qore_enqueues() {

	/* Defining js & css shortcut */
	if ( !is_admin() ) {
		//wp_deregister_script( 'jquery' );
		//wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js' );
		wp_register_script( 'cycle', trailingslashit( QORE_JS_PATH ) . 'cycle.js', array( 'jquery' ) );
		wp_register_script( 'cookie', trailingslashit( QORE_JS_PATH ) . 'cookie.js', array( 'jquery' ) );
		wp_register_script( 'gmap', 'http://maps.google.com/maps/api/js?sensor=false' );
		wp_register_script( 'pngFix', trailingslashit( QORE_JS_PATH ) . 'pngFix/jquery.pngFix.pack.js' );
		wp_register_script( 'mousewheel', trailingslashit( QORE_JS_PATH ) . 'mousewheel/jquery.mousewheel-3.0.4.pack.js', array( 'jquery' ) );
		wp_register_script( 'fancybox', trailingslashit( QORE_JS_PATH ) . 'fancybox/jquery.fancybox-1.3.4.pack.js', array( 'mousewheel' ) );
		wp_register_script( 'superfish', trailingslashit( QORE_JS_PATH ) . 'superfish.js', array( 'jquery' ) );
		wp_register_script( 'supersub', trailingslashit( QORE_JS_PATH ) . 'supersubs.js', array( 'superfish' ) );
		wp_register_script( 'html5', 'http://html5shiv.googlecode.com/svn/trunk/html5.js' );

		wp_enqueue_style( 'reset', trailingslashit( QORE_CSS_PATH ) . 'reset.css' );
		wp_register_style( 'fancybox', trailingslashit( QORE_CSS_PATH ) . 'fancybox/jquery.fancybox-1.3.4.css' );
		wp_register_style( 'supersub', trailingslashit( QORE_CSS_PATH )  . 'superfish.css' );
	}
	if ( is_admin() ) {
		wp_enqueue_style( 'qore-admin', trailingslashit( QORE_CSS_PATH ) . 'qore.css', false, NULL );
	}
}