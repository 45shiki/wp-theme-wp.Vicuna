<?php
// Do not delete. {{{
if (basename(__FILE__) === basename(getenv('SCRIPT_FILENAME'))) header('Location: /');
if (!vicuna_require_password()) return;
$reaction = vicuna_comments_and_trackpings($comments);
// }}} Do not delete.
?>
<?php if (!empty($reaction['comments']) || comments_open()):?>
			<div class="section" id="comments">
				<h2><?php comments_open()? _e('Comments', 'vicuna'): _e('Comments (Close)', 'vicuna')?>:<span class="count"><?php echo $post -> comment_count?></span></h2>
<?php if (!empty($reaction['comments'])):?>
				<dl class="log">
<?php foreach($reaction['comments'] as $comment):?>
					<dt id="comment<?php comment_ID()?>"><span class="name"><?php comment_author_link()?></span> <span class="date"><?php comment_date()?></span> <?php edit_comment_link(__('Edit', 'vicuna'), '<span class="admin">', '</span>')?></dt>
					<dd><?php comment_text()?></dd>
<?php endforeach?>
				</dl>
<?php endif?>
<?php if (comments_open()):?>
<?php if(vicuna_comment_is_needed_login()):?>
				<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url( get_permalink() )); ?></p>
<?php else:?>
				<form class="post" method="post" action="<?php echo get_option('siteurl')?>/wp-comments-post.php" id="commentsForm">
					<fieldset>
					<legend><?php _e('Comment Form', 'vicuna')?></legend>
					<div><input type="hidden" name="comment_post_ID" value="<?php echo $id?>" /></div>
					<dl id="name-email">
<?php if ($user_ID):?>
						<dt><?php _e('Logged in', 'vicuna')?></dt>
						<dd><?php echo $user_identity?> (<a href="<?php echo get_option('siteurl')?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', 'vicuna')?>"><?php _e('Logout')?></a>)</dd>
<?php else:?>
						<dt><label for="comment-author"><?php _e('Name', 'vicuna')?><?php if ($req) printf('(%s)', __('required', 'vicuna'))?></label></dt>
						<dd><input type="text" class="inputField" id="comment-author" name="author" size="20" value="" /></dd>
						<dt><label for="comment-email"><?php _e('Mail address', 'vicuna')?> (<?php _e('will not be published', 'vicuna')?>)<?php if ($req) printf('(%s)', __('required', 'vicuna'))?></label></dt>
						<dd><input type="text" class="inputField" size="20" id="comment-email" name="email" value="" /></dd>
<?php endif?>
					</dl>
					<dl>
<?php if (!$user_ID):?>
						<dt><label for="comment-url"><abbr title="Uniform Resource Identifer">URI</abbr></label></dt>
						<dd><input type="text" class="inputField" id="comment-url" name="url" size="20" value="http://" /></dd>
						<dt><?php _e('Remember personal info', 'vicuna')?></dt>
						<dd><input type="radio" class="radio" id="bakecookie" name="bakecookie" /> <label for="bakecookie"><?php _e('Yes')?></label><input type="radio" class="radio" id="forget" name="bakecookie" value="<?php _e('Forget Info', 'vicuna')?>" /> <label for="forget"><?php _e('No')?></label></dd>
<?php endif?>
						<dt><label for="comment-text"><?php _e('Comment', 'vicuna')?><?php if (allowed_tags()) _e('<span>You can use some <abbr title="Hyper Text Markup Language">HTML</abbr> tags for decorating.</span>', 'vicuna')?></label></dt>
						<dd><textarea id="comment-text" name="comment" rows="8" cols="50"><?php _e('Add Your Comment', 'vicuna')?></textarea></dd>
					</dl>
					<script type="text/javascript">
					//<![CDATA[
					var blankComment = '<?php _e('Add Your Comment', 'vicuna')?>';
					//]]>
					</script>
					<div class="action">
						<input type="submit" class="submit post" id="comment-post" name="post" value="<?php _e('Post')?>" />
					</div>
					</fieldset>
<?php if (!$user_ID):?>
					<script type="text/javascript">
					//<![CDATA[
					applyCookie('comments_form', '<?php echo COOKIEPATH?>', '<?php echo getenv('HTTP_HOST')?>');
					//]]>
					</script>
<?php endif?>
				</form>
<?php endif?>
<?php endif?>
			</div><!-- end div#comment -->
<?php endif?>
<?php if (!empty($reaction['trackpings']) || pings_open()):?>
			<div class="section" id="trackback">
				<h2><?php pings_open()? _e('Trackbacks', 'vicuna'): _e('Trackbacks (Close)', 'vicuna')?>:<span class="count"><?php echo count($reaction['trackpings'])?></span></h2>
<?php if (pings_open()):?>
				<dl class="info">
					<dt><?php _e('Trackback URL for this entry', 'vicuna')?></dt>
					<dd class="URL"><?php trackback_url()?></dd>
					<dt><?php _e('Listed below are links to weblogs that reference', 'vicuna')?></dt>
					<dd><?php printf(__('<a href="%s">%s</a> from <a href="%s">%s</a>', 'vicuna'), get_permalink(), get_the_title(), get_bloginfo('home'), get_bloginfo('name'))?></dd>
				</dl>
<?php endif?>
<?php if (!empty($reaction['trackpings'])):?>
				<dl class="log">
<?php foreach($reaction['trackpings'] as $comment):?>
					<dt id="ping<?php comment_ID()?>"><span class="name"><?php printf(__('%s from %s', 'vicuna'), get_comment_type(), get_comment_author_link())?></span> <span class="date"><?php comment_date()?></span></dt>
					<dd><?php comment_text()?></dd>
<?php endforeach?>
				</dl>
<?php endif?>
			</div><!-- end div#trackback -->
<?php endif?>