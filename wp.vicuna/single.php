<?php get_header()?>
<?php the_post()?>
<body class="individual <?php vicuna_page_layout()?>" id="entry<?php the_ID()?>">
<div id="header">
	<p class="siteName"><a href="<?php bloginfo('home')?>" title="<?php printf(__('Return to %s index', 'vicuna'), get_bloginfo('name'))?>"><?php bloginfo('name')?></a></p>
	<?php vicuna_page_description()?>
	<?php vicuna_global_navigation()?>
</div>
<div id="content">
	<div id="main">
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <?php the_category(' | ') ?> &gt; <span class="current"><?php the_title()?></span></p>
		<ul class="flip" id="flip1">
<?php if ($newer_post = vicuna_newer_post_exists()):?>
		<li class="newer"><a href="<?php echo get_permalink($newer_post -> ID)?>" title="<?php echo apply_filters('the_title', $newer_post -> post_title, $newer_post)?>" rel="nofollow"><?php _e('Newer', 'vicuna')?></a></li>
<?php endif?>
<?php if ($older_post = vicuna_older_post_exists()):?>
		<li class="older"><a href="<?php echo get_permalink($older_post -> ID)?>" title="<?php echo apply_filters('the_title', $older_post -> post_title, $older_post)?>" rel="nofollow"><?php _e('Older', 'vicuna')?></a></li>
<?php endif?>
		</ul>
		<h1><?php the_title()?></h1>
		<div class="entry">
			<ul class="info">
				<?php vicuna_author()?>
				<li class="date"><?php the_date()?> <?php the_time()?></li>
				<li class="category"><?php the_category(' | ') ?></li>
				<?php the_tags('<li class="tags">', ' | ', '</li>')?>
				<?php edit_post_link(__('Edit', 'vicuna'), '<li class="admin">', '</li>')?>
			</ul>
			<div class="textBody">
<?php the_content(__('Continue reading', 'vicuna'))?>
			</div>
			<ul class="flip" id="flip2">
<?php if ($newer_post = vicuna_newer_post_exists()):?>
				<li class="newer"><?php _e('Newer', 'vicuna')?>: <a href="<?php echo get_permalink($newer_post -> ID)?>" title="<?php _e('a newer entry', 'vicuna')?>"><?php echo apply_filters('the_title', $newer_post -> post_title, $newer_post)?></a></li>
<?php endif?>
<?php if ($older_post = vicuna_older_post_exists()):?>
				<li class="older"><?php _e('Older', 'vicuna')?>: <a href="<?php echo get_permalink($older_post -> ID)?>" title="<?php _e('an older entry', 'vicuna')?>"><?php echo apply_filters('the_title', $older_post -> post_title, $older_post)?></a></li>
<?php endif?>
			</ul>
<?php comments_template()?>
		</div><!--end entry-->
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <?php the_category(' | ')?> &gt; <span class="current"><?php the_title()?></span></p>
	</div><!-- end main-->

<?php get_sidebar()?>

<?php get_footer()?>