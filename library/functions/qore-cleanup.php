<?php

// Code from the theme ROOTS
// https://github.com/retlehs/roots


// root relative URLs for everything
// inspired by http://www.456bereastreet.com/archive/201010/how_to_make_wordpress_urls_root_relative/
// thanks to Scott Walkinshaw (scottwalkinshaw.com)
function qore_root_relative_url($input) {
  $output = preg_replace_callback(
    '!(https?://[^/|"]+)([^"]+)?!',
    create_function(
      '$matches',
      // if full URL is site_url, return a slash for relative root
      'if (isset($matches[0]) && $matches[0] === site_url()) { return "/";' .
      // if domain is equal to site_url, then make URL relative
      '} elseif (isset($matches[0]) && strpos($matches[0], site_url()) !== false) { return $matches[2];' .
      // if domain is not equal to site_url, do not make external link relative
      '} else { return $matches[0]; };'
    ),
    $input
  );
  return $output;
}

if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {

    add_filter('bloginfo_url', 'qore_root_relative_url');
    add_filter('theme_root_uri', 'qore_root_relative_url');
    add_filter('stylesheet_directory_uri', 'qore_root_relative_url');
    add_filter('template_directory_uri', 'qore_root_relative_url');
    add_filter('script_loader_src', 'qore_root_relative_url');
    add_filter('style_loader_src', 'qore_root_relative_url');
    add_filter('plugins_url', 'qore_root_relative_url');
    add_filter('the_permalink', 'qore_root_relative_url');
    add_filter('wp_list_pages', 'qore_root_relative_url');
    add_filter('wp_list_categories', 'qore_root_relative_url');
    add_filter('wp_nav_menu', 'qore_root_relative_url');
    add_filter('the_content_more_link', 'qore_root_relative_url');
    add_filter('the_tags', 'qore_root_relative_url');
    add_filter('get_pagenum_link', 'qore_root_relative_url');
    add_filter('get_comment_link', 'qore_root_relative_url');
    add_filter('month_link', 'qore_root_relative_url');
    add_filter('day_link', 'qore_root_relative_url');
    add_filter('year_link', 'qore_root_relative_url');
    add_filter('tag_link', 'qore_root_relative_url');
    add_filter('the_author_posts_link', 'qore_root_relative_url');
}

// remove root relative URLs on any attachments in the feed
function qore_root_relative_attachment_urls() {
  if (!is_feed()) {
    add_filter('wp_get_attachment_url', 'qore_root_relative_url');
    add_filter('wp_get_attachment_link', 'qore_root_relative_url');
  }
}

add_action('pre_get_posts', 'qore_root_relative_attachment_urls');


// remove WordPress version from RSS feed
function qore_no_generator() { return ''; }
add_filter('the_generator', 'qore_no_generator');

// cleanup wp_head
function qore_noindex() {
  if (get_option('blog_public') === '0') {
    echo '<meta name="robots" content="noindex,nofollow">', "\n";
  }
}

function qore_rel_canonical() {
  if (!is_singular()) {
    return;
  }

  global $wp_the_query;
  if (!$id = $wp_the_query->get_queried_object_id()) {
    return;
  }

  $link = get_permalink($id);
  echo "\t<link rel=\"canonical\" href=\"$link\">\n";
}

// remove CSS from recent comments widget
function qore_remove_recent_comments_style() {
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  }
}

// remove CSS from gallery
function qore_gallery_style($css) {
  return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}

function qore_head_cleanup() {
  // http://wpengineer.com/1438/wordpress-header/
  //remove_action('wp_head', 'feed_links', 2);
  //remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('wp_head', 'rsd_link');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'index_rel_link');
  remove_action('wp_head', 'parent_post_rel_link', 10, 0);
  remove_action('wp_head', 'start_post_rel_link', 10, 0);
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  remove_action('wp_head', 'noindex', 1);
  add_action('wp_head', 'qore_noindex');
  add_action('wp_head', 'qore_remove_recent_comments_style', 1);
  add_filter('gallery_style', 'qore_gallery_style');

  if (!class_exists('WPSEO_Frontend')) {
    remove_action('wp_head', 'rel_canonical');
    add_action('wp_head', 'qore_rel_canonical');
  }
}

add_action('init', 'qore_head_cleanup');

// cleanup gallery_shortcode()
function qore_gallery_shortcode($attr) {
  global $post, $wp_locale;

  static $instance = 0;
  $instance++;

  // Allow plugins/themes to override the default gallery template.
  $output = apply_filters('post_gallery', '', $attr);
  if ($output != '') {
    return $output;
  }

  // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
  if (isset($attr['orderby'])) {
    $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
    if (!$attr['orderby']) {
      unset($attr['orderby']);
    }
  }

  extract(shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post->ID,
    'icontag'    => 'li',
    'captiontag' => 'p',
    'columns'    => 3,
    'size'       => 'thumbnail',
    'include'    => '',
    'exclude'    => ''
  ), $attr));

  $id = intval($id);
  if ('RAND' == $order) {
    $orderby = 'none';
  }

  if (!empty($include)) {
    $include = preg_replace( '/[^0-9,]+/', '', $include );
    $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    $attachments = array();
    foreach ($_attachments as $key => $val) {
      $attachments[$val->ID] = $_attachments[$key];
    }
  } elseif (!empty($exclude)) {
    $exclude = preg_replace('/[^0-9,]+/', '', $exclude);
    $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
  } else {
    $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
  }

  if (empty($attachments)) {
    return '';
  }

  if (is_feed()) {
    $output = "\n";
    foreach ($attachments as $att_id => $attachment)
      $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
    return $output;
  }

  $captiontag = tag_escape($captiontag);
  $columns = intval($columns);
  $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
  $float = is_rtl() ? 'right' : 'left';

  $selector = "gallery-{$instance}";

  $gallery_style = $gallery_div = '';
  if (apply_filters('use_default_gallery_style', true)) {
    $gallery_style = "";
  }
  $size_class = sanitize_html_class($size);
  $gallery_div = "<ul id='$selector' class='thumbnails gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
  $output = apply_filters('gallery_style', $gallery_style . "\n\t\t" . $gallery_div);

  $i = 0;
  foreach ($attachments as $id => $attachment) {
    $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

    $output .= "
      <{$icontag} class=\"gallery-item\">
        $link
      ";
    if ($captiontag && trim($attachment->post_excerpt)) {
      $output .= "
        <{$captiontag} class=\"gallery-caption hidden\">
        " . wptexturize($attachment->post_excerpt) . "
        </{$captiontag}>";
    }
    $output .= "</{$icontag}>";
    if ($columns > 0 && ++$i % $columns == 0) {
      $output .= '';
    }
  }

  $output .= "</ul>\n";

  return $output;
}

remove_shortcode('gallery');
add_shortcode('gallery', 'qore_gallery_shortcode');

function qore_attachment_link_class($html) {
  $postid = get_the_ID();
  $html = str_replace('<a', '<a class="thumbnail"', $html);
  return $html;
}
add_filter('wp_get_attachment_link', 'qore_attachment_link_class', 10, 1);

// http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
function qore_caption($output, $attr, $content) {
  /* We're not worried abut captions in feeds, so just return the output here. */
  if ( is_feed()) {
    return $output;
  }

  /* Set up the default arguments. */
  $defaults = array(
    'id' => '',
    'align' => 'alignnone',
    'width' => '',
    'caption' => ''
  );

  /* Merge the defaults with user input. */
  $attr = shortcode_atts($defaults, $attr);

  /* If the width is less than 1 or there is no caption, return the content wrapped between the [caption]< tags. */
  if (1 > $attr['width'] || empty($attr['caption'])) {
    return $content;
  }

  /* Set up the attributes for the caption <div>. */
  $attributes = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '' );
  $attributes .= ' class="thumbnail wp-caption ' . esc_attr($attr['align']) . '"';
  $attributes .= ' style="width: ' . esc_attr($attr['width']) . 'px"';

  /* Open the caption <div>. */
  $output = '<div' . $attributes .'>';

  /* Allow shortcodes for the content the caption was created for. */
  $output .= do_shortcode($content);

  /* Append the caption text. */
  $output .= '<div class="caption"><p class="wp-caption-text">' . $attr['caption'] . '</p></div>';

  /* Close the caption </div>. */
  $output .= '</div>';

  /* Return the formatted, clean caption. */
  return $output;
}

add_filter('img_caption_shortcode', 'qore_caption', 10, 3);


// http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
function qore_remove_dashboard_widgets() {
  remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
  remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
  remove_meta_box('dashboard_primary', 'dashboard', 'normal');
  remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
}

add_action('admin_init', 'qore_remove_dashboard_widgets');

// excerpt cleanup
function qore_excerpt_length($length) {
  if (defined('POST_EXCERPT_LENGTH')) {
    return POST_EXCERPT_LENGTH;
  }
  return 50;

}

function qore_excerpt($length_callback='', $more_callback='') {
  global $post;
  if(function_exists($length_callback)){
    add_filter('excerpt_length', $length_callback);
  }
  if(function_exists($more_callback)){
    add_filter('excerpt_more', $more_callback);
  }

  $output = get_the_excerpt();
  $output = apply_filters('wptexturize', $output);
  $output = apply_filters('convert_chars', $output);
  $output = '<p>'.$output.'</p>';
  echo $output;
}


add_filter('excerpt_length', 'qore_excerpt_length');


// we don't need to self-close these tags in html5:
// <img>, <input>
function qore_remove_self_closing_tags($input) {
  return str_replace(' />', '>', $input);
}

add_filter('get_avatar', 'qore_remove_self_closing_tags');
add_filter('comment_id_fields', 'qore_remove_self_closing_tags');
add_filter('post_thumbnail_html', 'qore_remove_self_closing_tags');

// check to see if the tagline is set to default
// show an admin notice to update if it hasn't been changed
// you want to change this or remove it because it's used as the description in the RSS feed
function qore_notice_tagline() {
    global $current_user;
    $user_id = $current_user->ID;

    if (!get_user_meta($user_id, 'ignore_tagline_notice')) {
      echo '<div class="error">';
      echo '<p>', sprintf(__('Please update your <a href="%s">site tagline</a> <a href="%s" style="float: right;">Hide Notice</a>', 'roots'), admin_url('options-general.php'), '?tagline_notice_ignore=0'), '</p>';
      echo '</div>';
    }
}

if ((get_option('blogdescription') === 'Just another WordPress site') && isset($_GET['page']) != 'theme_activation_options') {
  add_action('admin_notices', 'qore_notice_tagline');
}

function qore_notice_tagline_ignore() {
  global $current_user;
  $user_id = $current_user->ID;
  if (isset($_GET['tagline_notice_ignore']) && '0' == $_GET['tagline_notice_ignore']) {
    add_user_meta($user_id, 'ignore_tagline_notice', 'true', true);
  }
}

add_action('admin_init', 'qore_notice_tagline_ignore');

// set the post revisions to 5 unless the constant
// was set in wp-config.php to avoid DB bloat
if (!defined('WP_POST_REVISIONS')) { define('WP_POST_REVISIONS', 5); }

// allow more tags in TinyMCE including <iframe> and <script>
function qore_change_mce_options($options) {
  $ext = 'pre[id|name|class|style],iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src],script[charset|defer|language|src|type]';
  if (isset($initArray['extended_valid_elements'])) {
    $options['extended_valid_elements'] .= ',' . $ext;
  } else {
    $options['extended_valid_elements'] = $ext;
  }
  return $options;
}

add_filter('tiny_mce_before_init', 'qore_change_mce_options');

//clean up the default WordPress style tags
add_filter('style_loader_tag', 'qore_clean_style_tag');

function qore_clean_style_tag($input) {
  preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
  //only display media if it's print
  $media = $matches[3][0] === 'print' ? ' media="print"' : '';
  return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}


function qore_body_class( $class ) {
  if (!is_front_page()) {
    $class = array_merge($class, array('not-front'));
  }
  if (get_post_type()) {
    $class = array_merge($class, array(get_post_type()));
  }
  return $class;
}
add_filter( 'body_class', 'qore_body_class' );