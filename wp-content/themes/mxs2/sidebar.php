<div id="sidebar">
<ul>
<?php if ( ! dynamic_sidebar( 'primary-widget-area' ) ) : ?>
			<li id="archives" class="widget-container">
				<span class="widgettitle"><?php _e('Archive', 'MxS'); ?></span>
				<ul>
					<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</li>

			<li id="meta" class="widget-container">
				<span class="widgettitle"><?php _e('Meta', 'MxS'); ?></span>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</li>
<?php endif; ?>
</ul>
</div>