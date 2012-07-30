<?php

get_header(); ?>

		<div id="primary">
			<?php qore_begining_section() ?>
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content'); ?>

				<?php endwhile; ?>

			</div><!-- #content -->
			<?php qore_closing_section() ?>
		</div><!-- #primary -->


<?php get_footer(); ?>