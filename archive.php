<?php

get_header(); ?>

		<div id="primary">
			<?php qore_begining_section() ?>
			<div id="content" role="main">
				<?php if (condition): ?>

				<h1 class="archive-title"><?php single_term_title(); ?> </h1>

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'content', '' . get_post_type() . ''); ?>

					<?php endwhile; ?>
				<?php endif ?>
			</div><!-- #content -->
			<?php qore_closing_section() ?>
		</div><!-- #primary -->


<?php get_footer(); ?>