<?php

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">
				<?php qore_begining_section() ?>
				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php get_template_part( 'content'); ?>

				<?php endwhile; ?>
				<?php qore_closing_section() ?>
			</div><!-- #content -->
		</div><!-- #primary -->


<?php get_footer(); ?>