<?php get_header(); ?>
	<div id="main">
	<div id="entry<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
		<div class="post">
			<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry"><?php the_content(); ?></div><div class="clear"></div>
				<?php wp_link_pages(); ?>
		</div>
		<?php comments_template(); ?>
			<?php endwhile; ?>
	<?php else : ?>
	<div id="main">
	 <h2><?php _e('Not Found', 'MxS'); ?></h2>
	 <?php get_search_form(); ?>
	</div>
		<?php endif; ?>
	</div><!-- section entry -->
	</div><!-- main -->
	<?php	get_sidebar(); ?>
	<?php	get_footer(); ?>