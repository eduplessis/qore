<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<nav id="nav-above">
		<div class="nav-next"><?php next_posts_link( __( 'Suivant', 'qore' ) ); ?></div>
		<div class="nav-previous"><?php previous_posts_link( __( 'Précédent', 'qore' ) ); ?></div>
	</nav><!-- #nav-above -->
<?php endif; ?>