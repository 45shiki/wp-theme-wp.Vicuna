<div id="utilities">
	<dl class="navi">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('navi')):?>
<?php if ($pages = &get_pages('')):?>
		<dt><?php _e('Pages', 'vicuna')?></dt>
		<dd class="pages">
			<ul><?php wp_list_pages('sort_column=menu_order&title_li=0')?></ul>
		</dd>
<?php endif?>
		<dt><?php _e('Recent Entries', 'vicuna')?></dt>
		<dd class="recentEntries">
			<ul><?php wp_get_archives('type=postbypost&limit=5')?></ul>
		</dd>
		<dt><?php _e('Categories', 'vicuna')?></dt>
		<dd class="categoryArchives">
			<ul><?php wp_list_categories('sort_column=name&optioncount=0&hierarchical=1&title_li=0')?></ul>
		</dd>
		<dt><?php _e('Archives', 'vicuna')?></dt>
		<dd class="monthlyArchives"><?php vicuna_archives_link()?></dd>
<?php if (function_exists('get_tags')):?>
		<dt><?php _e('Tag Cloud', 'vicuna')?></dt>
		<dd class="tagCloud"><?php vicuna_tag_cloud()?></dd>
<?php endif?>
<?php endif?>
	</dl>
	<dl class="others">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('others')):?>
		<dt><?php _e('Search', 'vicuna')?></dt>
		<dd class="search">
			<form method="get" action="<?php bloginfo('home')?>/">
				<fieldset>
					<legend><label for="searchKeyword"><?php printf(__('Search %s', 'vicuna'), get_bloginfo('name'))?></label></legend>
					<div>
						<script type="text/javascript">
						//<![CDATA[
							 var blankSearchKeyword = '<?php _e('Keyword(s)', 'vicuna')?>';
						//]]>
						</script>
						<input type="text" class="inputField" id="searchKeyword" name="s" size="10" value="<?php echo is_search()? wp_specialchars($s, 1): __('Keyword(s)', 'vicuna')?>" />
						<input type="submit" class="submit" id="submit" value="<?php _e('Search', 'vicuna')?>" />
					</div>
				</fieldset>
			</form>
		</dd>
		<dt><?php _e('Feeds', 'vicuna')?></dt>
		<dd class="feed">
			<ul>
				<li class="rss"><a href="<?php bloginfo('rss2_url')?>"><?php _e('All Entries', 'vicuna')?>(RSS2.0)</a></li>
				<li class="atom"><a href="<?php bloginfo('atom_url')?>"><?php _e('All Entries', 'vicuna')?>(Atom)</a></li>
				<li class="rss"><a href="<?php bloginfo('comments_rss2_url')?>"><?php _e('All Comments', 'vicuna')?>(RSS2.0)</a></li>
			</ul>
		</dd>
		<dt><?php _e('Meta', 'vicuna')?></dt>
		<dd class="meta">
			<ul>
				<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Strict', 'vicuna')?>" rel="nofollow"><?php printf(__('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>', 'vicuna'))?></a></li>
<?php wp_register()?>
				<li><?php wp_loginout()?></li>
<?php wp_meta()?>
			</ul>
		</dd>
<?php endif?>
	</dl>
</div>