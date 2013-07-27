    <p class="return"><a href="#header"><?php _e('Return to page top', 'vicuna')?></a></p>
</div><!--end content-->
<div id="footer">
    <ul class="support">
        <li>Powered by <a href="http://wordpress.org/">WordPress <?php bloginfo('version')?></a></li>
        <li class="template"><a href="http://vicuna.jp/">vicuna CMS <?php Vicuna :: get_version()?></a> - <a href="<?php Vicuna :: URI()?>" title="ver.<?php Vicuna :: get_version()?>">WordPress<?php _e('Theme')?></a></li>
        <li class="skin">Visualized by <?php vicuna_skinname()?></li>
        <?php vicuna_color_information()?>
    </ul>
    <address><?php printf(__('Copyright &copy; %s All Rights Reserved.', 'vicuna'), get_bloginfo('name'))?></address>
</div>
<?php wp_footer()?>
</body>
</html>
