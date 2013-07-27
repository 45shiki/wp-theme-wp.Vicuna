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
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a><?php vicuna_page_relay()?> &gt; <span class="current"><?php the_title()?></span></p>
		<h1><?php the_title()?></h1>
		<div class="entry">
			<ul class="info">
				<?php vicuna_author()?>
				<li class="date"><?php the_date()?> <?php the_time()?></li>
				<?php edit_post_link(__('Edit', 'vicuna'), '<li class="admin">', '</li>')?>
			</ul>
			<div class="textBody">
<?php the_content(__('Continue reading', 'vicuna'))?>
			</div>
<?php comments_template()?>
		</div><!--end entry-->
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a><?php vicuna_page_relay()?> &gt; <span class="current"><?php the_title()?></span></p>
	</div><!-- end main-->

<?php get_sidebar()?>

<?php get_footer()?>