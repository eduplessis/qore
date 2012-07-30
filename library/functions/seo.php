<?php


/* Print the <title> tag based on what is being viewed. */
function get_qore_title() {
	$qore_title = wp_title( '|', false, 'right' );
	$qore_title .= get_bloginfo( 'name' );
	
	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description');
	if ( $site_description && ( is_home() || is_front_page() ) )
		$qore_title .= " | ".$site_description;
	
	return $qore_title;
}

function get_qore_metaDescription() {
	$qore_metaDescription =  get_bloginfo('description');
	return apply_filters('get_qore_metaDescription', $qore_metaDescription );
}

function get_qore_metaAuthor(){
	$qore_metaAuthor = get_bloginfo('name');
	return apply_filters('get_qore_metaAuthor', $qore_metaAuthor);
}

function get_qore_favicon($qore_path='') {
	if ($qore_path) {
		$qore_favicon = '<link rel="shortcut icon" href"'.$qore_path.'">';
	}elseif (file_exists(STYLESHEETPATH . '/favicon.ico')){
		$qore_favicon = '<link rel="shortcut icon" href="'.THEME_URI.'/favicon.ico">';
	}else{
		$qore_favicon = null;
	}
	
	return apply_filters('get_qore_favicon', $qore_favicon );
}

/*
<meta name="description" content="">
16.  <meta name="author" content="">
17. 
18.  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
19.  <meta name="viewport" content="width=device-width, initial-scale=1.0">
20. 
21.  <!-- Place favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
22.  <link rel="shortcut icon" href="/favicon.ico">
23.  <link rel="apple-touch-icon" href="/apple-touch-icon.png"

*/