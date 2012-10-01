<?php qore_before_doctype(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo get_qore_title()?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php qore_before_container(); ?>
<div id="container">
	<?php qore_before_header(); ?>
	<header>
		<?php echo get_qore_header(); ?>
	</header>
	<?php qore_after_header(); ?>