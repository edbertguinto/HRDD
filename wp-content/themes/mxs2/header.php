<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title><?php	if ( !is_home() ) : ?>	<link rel="start" href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?> Home" /><?php	endif; ?>
	<?php wp_head(); ?></head><body <?php body_class(); ?>><div id="header">
		<div class="container">
		<?php $mxs_options = get_option('MxS_theme_options'); ?>			<h1 class="siteName">
				<?php if ($mxs_options['logo_src']!='') { ?>
					<a class="logo_img" href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><img src="<?php echo $mxs_options['logo_src']; ?>" /></a>
				<?php } else { ?>
					<a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
				<?php } ?>
			</h1>
			<?php if ( !$mxs_options['desc']) {?>				<h2 class="description"><span>&#8226;</span><?php bloginfo('description'); ?></h2>
			<?php } ?>	
		</div>
		<div id="globalNavi">
			<div class="container">
					<?php wp_nav_menu( array( 'show_home' => 'Home','container' => 'globalNavi', 'theme_location' => 'primary') ); ?>	
				<?php get_search_form(); ?>
			</div>
		</div>		
	</div>
<div class="clear"></div><div id="content">
		<div class="clear"></div>

