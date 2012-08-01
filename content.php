<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_search() || is_archive() || is_home()) : // Only display Excerpts for search pages ?>
	<div class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	</div>
	<div class="entry-summary">
		<?php the_excerpt( __( 'Lire la suite <span class="meta-nav">&rarr;</span>', 'qore' ) ); ?>
	</div>
	<?php else : ?>
	<div class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</div>
	<div class="entry-content">
		<?php the_content( __( 'Lire la suite <span class="meta-nav">&rarr;</span>', 'qore' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'qore' ), 'after' => '</div>' ) ); ?>
	</div>
	<?php endif; ?>

</div>