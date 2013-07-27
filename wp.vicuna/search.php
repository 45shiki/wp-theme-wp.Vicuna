<?php get_header()?>
<body class="individual <?php vicuna_page_layout()?>" id="siteSearch">
<div id="header">
	<p class="siteName"><a href="<?php bloginfo('home')?>" title="<?php printf(__('Return to %s index', 'vicuna'), get_bloginfo('name'))?>"><?php bloginfo('name')?></a></p>
	<?php vicuna_page_description()?>
	<?php vicuna_global_navigation()?>
</div>
<div id="content">
	<div id="main">
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <span class="current"><?php _e('Search Result', 'vicuna')?></span></p>
		<h1><?php _e('Search Result', 'vicuna')?></h1>
<?php if (have_posts()):?>
		<div class="section entry">
    		<ul class="info">
    			<?php vicuna_search_result(__('<li>Search: <em>%s</em></li><li><em><span class="count">%s</span></em> Hits</li>', 'vicuna'))?>
    		</ul>
    		<dl class="headline">
<?php while (have_posts()): the_post()?>
				<dt>
					<a href="<?php the_permalink()?>"><?php the_title()?></a>
					<span class="date"> - <?php _e('Posted date: ', 'vicuna')?><?php the_date()?> <?php the_time()?></span>
				</dt>
				<dd>
					<ul>
					 <li class="textBody"><?php vicuna_the_content_oneliner()?></li>
					 <li class="author"><?php _e('Posted by: ', 'vicuna')?><span class="name"><?php the_author()?></span></li>
					 <li class="category"><?php _e('Category: ', 'vicuna')?><?php the_category(', ')?></li>
					 <li class="tag"><?php the_tags(__('Tag: ', 'vicuna'), ', ', '')?></li>
					</ul>
				</dd>
<?php endwhile?>
    		</dl>
		</div>
<?php else:?>
		<div class="section entry">
    		<ul class="info">
    			<?php vicuna_search_result(__('<li>Search: <em>%s</em></li><li><em><span class="count">%s</span></em> Hits</li>', 'vicuna'))?>
    		</ul>
			<h2><?php _e('Search Result', 'vicuna')?></h2>
			<div class="textBody">
				<p><?php printf(__('Your search - <em>%s</em> -- did not match any documents.', 'vicuna'), wp_specialchars($s, 1))?></p>
				<p><?php _e('Suggestions:', 'vicuna')?></p>
				<ul>
					<li><?php _e('Make sure all words are spelled correctly.', 'vicuna')?></li>
					<li><?php _e('Try different keywords.', 'vicuna')?></li>
					<li><?php _e('Try more general keywords.', 'vicuna')?></li>
					<li><?php _e('Try decreasing the number of keywords.', 'vicuna')?></li>
				</ul>
			</div>
		</div>
<?php endif?>
<?php vicuna_paging()?>
		<p class="topicPath"><a href="<?php bloginfo('home')?>"><?php _e('Home', 'vicuna')?></a> &gt; <span class="current"><?php _e('Search Result', 'vicuna')?></span></p>
	</div><!-- end main -->

<?php get_sidebar();

                    ?>

<?php get_footer();

                    ?>