<?php get_header()?>
<body class="individual <?php vicuna_page_layout()?>">
<div id="header">
	<p class="siteName"><a href="<?php bloginfo('home')?>" title="<?php printf(__('Return to %s index', 'vicuna'), get_bloginfo('name'))?>"><?php bloginfo('name')?></a></p>
	<?php vicuna_page_description()?>
	<?php vicuna_global_navigation()?>
</div>
<div id="content">
	<div id="main">
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <span class="current"><?php _e('Error 404', 'vicuna')?></span></p>
		<h1><?php _e('Error 404', 'vicuna')?> - <?php _e('Not Found', 'vicuna')?></h1>
		<div class="entry">
			<div class="textBody">
				<p><?php _e("Sorry, but you are looking for something that isn't here.", 'vicuna')?></p>
			</div>
		</div><!--end entry-->
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <span class="current"><?php _e('Error 404', 'vicuna')?></span></p>
	</div><!-- end main-->

<?php get_sidebar()?>

<?php get_footer()?>