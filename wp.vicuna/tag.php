<?php get_header()?>
<body class="category <?php vicuna_page_layout()?>">
<div id="header">
	<p class="siteName"><a href="<?php bloginfo('home')?>" title="<?php printf(__('Return to %s index', 'vicuna'), get_bloginfo('name'))?>"><?php bloginfo('name')?></a></p>
	<?php vicuna_page_description()?>
	<?php vicuna_global_navigation()?>
</div>
<div id="content">
	<div id="main">
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <?php _e('Tags', 'vicuna')?> &gt; <span class="current"><?php single_tag_title()?></span></p>
		<h1><?php single_cat_title()?></h1>
<?php while (have_posts()): the_post()?>
		<div class="section entry" id="entry<?php the_ID()?>">
			<h2><a href="<?php the_permalink()?>"><?php the_title()?></a></h2>
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
			<ul class="reaction">
<?php if ($post -> comment_count || comments_open()):?>
				<li class="comment"><a href="<?php the_permalink() ?>#comments" title="<?php printf(__('Comments on %s', 'vicuna'), get_the_title())?>" rel="nofollow"><?php comments_open() ? _e('Comments', 'vicuna'): _e('Comments (Close)', 'vicuna')?></a>: <span class="count"><?php echo $post -> comment_count?></span></li>
<?php else:?>
				<li><?php _e('Comments (Close)', 'vicuna')?>: <span class="count"><?php echo $post -> comment_count?></span></li>
<?php endif?>
<?php if ($post -> ping_count || pings_open()):?>
				<li class="trackback"><a href="<?php the_permalink() ?>#trackback" title="<?php printf(__('Trackbacks to %s', 'vicuna'), get_the_title())?>" rel="nofollow"><?php pings_open() ? _e('Trackbacks', 'vicuna'): _e('Trackbacks (Close)', 'vicuna')?></a>: <span class="count"><?php echo $post -> ping_count?></span></li>
<?php else:?>
				<li><?php _e('Trackbacks (Close)', 'vicuna')?>: <span class="count"><?php echo $post -> ping_count?></span></li>
<?php endif?>
			</ul>
		</div>
<?php endwhile?>
<?php vicuna_paging()?>
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <?php _e('Tags', 'vicuna')?> &gt; <span class="current"><?php single_cat_title()?></span></p>
	</div><!-- end main-->

<?php get_sidebar()?>

<?php get_footer()?>