<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<nav id="nav-above">
		<div class="nav-previous"><?php next_posts_link( __( 'Suivant', 'qore' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Précédent', 'qore' ) ); ?></div>
	</nav><!-- #nav-above -->
<?php endif; ?>