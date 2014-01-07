<?php get_header(); ?>
	<div id="main">
	<div class="section entry">
	<h1 class="entry-title"><?php _e('Search Results &raquo;', 'MxS');echo ':'.get_search_query(); ?></h1>
		<?php if(have_posts()) : ?>
		<?php while(have_posts()) : the_post(); ?>
		<div class="post">
			<h2 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
			<div class="entry">
					<?php the_excerpt(); ?>
			</div>
				<?php wp_link_pages(); ?>
		</div>
			<?php endwhile; ?>
	<?php else : ?>
		<h3><?php _e('No posts found. Try a different search?', 'MxS'); ?></h3>
		<?php get_search_form(); ?>
		<?php endif; ?>
	</div><!-- section entry -->
	<?php if (  $wp_query->max_num_pages > 1 ) : ?>
			<?php if (function_exists('MxS_pagenavi')) : ?><div class="page_navi"><?php MxS_pagenavi(5); ?></div>
		<?php else: ?>
			<div id="page_navi">
				<?php next_posts_link(); ?>
				<?php previous_posts_link(); ?>
			</div>
		<?php endif;  ?>
	<?php endif;  ?><!-- page navi -->
	</div><!-- main -->
	<?php	get_sidebar(); ?>
	<?php	get_footer(); ?>