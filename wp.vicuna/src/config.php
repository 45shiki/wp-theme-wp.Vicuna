<?php
/**
 * wp.Vicuna theme manager
 * http://wp.vicuna.jp/
 * http://spais.co.jp/wp.vicuna/
 * 
 * WordPress 2.8.3 and Newer
 * PHP Version 4 and 5
 * 
 * LICENSE: This source file is subject to version 2.0 of the GPL
 * that is available through the world-wide-web at the following URI:
 * http://wordpress.org/about/gpl/
 * 
 * @package wp.Vicuna
 * @version 2.0.3
 * @author HAYASHI Ryo <ryo@spais.co.jp> 
 * @copyright 2010 SPaiS Inc.
 * @license http://wordpress.org/about/gpl/ GPL 2.0
 * @link http://wp.vicuna.jp/
 * @link http://spais.co.jp/wp.vicuna/
 */

/**
 * Initial function
 * 
 * @return void 
 */
function vicuna_config()
{
    $config_page = &VicunaConfigPager :: i('VicunaConfigPager');
    $config_page -> display();
} 

/**
 * Config page class
 * 
 * @package wp.Vicuna
 * @author HAYASHI Ryo<ryo@spais.co.jp> 
 */
class VicunaConfigPager extends VicunaPager {
    /**
     * 
     * @var string Option group name
     */
    var $opt_group = 'vicuna_config';

    function get_element_eye_catch_image()
    {

        ?>
        <label><input type="radio" name="vicuna-eye_catch-image-type" value="file" /><?php _e('File', 'vicuna')?></label>
        <label><input type="radio" name="vicuna-eye_catch-image-type" value="file" /><?php _e('URL', 'vicuna')?></label>
        <label><input type="radio" name="vicuna-eye_catch-image-type" value="file" /><?php _e('Flickr', 'vicuna')?></label>
        <label><input type="radio" name="vicuna-eye_catch-image-type" value="file" /><?php _e('File', 'vicuna')?></label>
        <input type="file" name="vicuna-eye_catch-image" />
<?php
    } 

    function get_element_color_information()
    {
        $config = &Vicuna :: config();
        $t = $config -> get_option('vicuna-color');

        ?>
        <input type="hidden" id="vicuna-color" name="vicuna-color" value="<?php printf('themeid*%s,title*%s,hex*%s|%s|%s|%s|%s',
            $t['themeid'], $t['title'], $t['hex'][0], $t['hex'][1], $t['hex'][2], $t['hex'][3], $t['hex'][4])?>" />
        <div class="colorTheme" id="currentColor"></div>
        <div id="vicunaColorEditor"><div class="fieldset">
            <label><?php _e('Title', 'vicuna')?>:<input type="text" class="title" /></label>
            <div class="colorTheme"></div>
            <div class="submit">
                <input type="button" class="done" value="<?php _e('Done', 'vicuna')?>" />
                <input type="button" class="reset" value="<?php _e('Reset', 'vicuna')?>" />
            </div>
        </div></div>
<?php
    } 

    function get_element_color_search()
    {

        ?>
		<input type="text" id="vicunaColorSearchText" /><input type="button" id="vicunaColorSearchButton" value="<?php _e('Search Kuler', 'vicuna')?>" />
		<span id="vicunaColorSearchPaging"><span class="prev disable">&lt;-</span><span class="next disable">-&gt;</span></span>
		<ul id="vicunaColorSearchResults"></ul>
<?php
    } 

    function get_element_popular_color()
    {

        ?>
		<ul id="popularColors"><li class="loading"><?php _e('Loading...', 'vicuna')?></li></ul>
<?php
    } 

    function get_element_recent_upload()
    {

        ?>
		<ul id="recentUploads"><li class="loading"><?php _e('Loading...', 'vicuna')?></li></ul>
<?php
    } 

    function get_element_color_list()
    {

        ?>
		<ul id="colorThemes"></ul>
<?php
    } 

    /**
     * Generate title option element
     * 
     * @return void 
     */
    function get_element_title()
    {
        $config = &Vicuna :: config();
        $option = $config -> get_option('vicuna-title-front');
        $sels = $opts = array();
        foreach($config -> enable_titles as $key => $value) {
            $selected = $option === $key? ' selected="selected"': null;
            $opts[] = sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
        } 
        $sels[] = sprintf('<select name="vicuna-title-front">%s</select>', implode("\n", $opts));

        $option = $config -> get_option('vicuna-title-separator');
        $opts = array();
        foreach($config -> enable_separators as $key => $value) {
            $selected = $option === $key? ' selected="selected"': null;
            $opts[] = sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
        } 
        $sels[] = sprintf('<select name="vicuna-title-separator">%s</select>', implode("\n", $opts));

        $option = $config -> get_option('vicuna-title-rear');
        $opts = array();
        foreach($config -> enable_titles as $key => $value) {
            $selected = $option === $key? ' selected="selected"': null;
            $opts[] = sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
        } 
        $sels[] = sprintf('<select name="vicuna-title-rear">%s</select>', implode("\n", $opts));

        echo implode("\n", $sels);
    } 

    /**
     * Generate layout option element
     * 
     * Enable args
     * - template => Template type
     * 
     * @param  $args array
     * @return void 
     */
    function get_element_layout($args)
    {
        $config = &Vicuna :: config();
        if (!array_key_exists($args['template'], $config -> enable_templates)) return null;
        $option = $config -> get_option("vicuna-layout-{$args['template']}");
        $rows = array();
        foreach($config -> enable_layouts as $value => $label) {
            if (is_integer($value)) $value = $label;
            $selected = $value === $option? ' selected="selected"': null;
            $rows[] = sprintf('<option value="%s"%s>%s</option>', $value, $selected, $label);
        } 
        printf('<select name="vicuna-layout-%1$s" id="vicuna-layout-%1$s">%2$s</select>', $args['template'], implode("\n", $rows));
    } 

    /**
     * (non-PHPdoc)
     * 
     * @see wp-content/themes/wp.vicuna/src/VicunaPager#display()
     */
    function display()
    {
        $ct = current_theme_info();
        $preview_link = esc_url(get_option('home') . '/');
        if (is_ssl()) $preview_link = str_replace('http://', 'https://', $preview_link);
        $preview_link = htmlspecialchars(add_query_arg(array('preview' => 0, 'template' => $ct -> template,
                    'stylesheet' => $ct -> stylesheet, 'TB_iframe' => 'true'), $preview_link));
        $config = &Vicuna :: config();
        $config_option = $config -> get_option('config');
        $layout_option = $config -> get_option('layout');

        $file = basename(__FILE__);
        add_settings_section('general', __('General', 'vicuna'), array(&$this, 'get_section'), $file);
        add_settings_field('language', __('Language', 'vicuna'), array(&$this, '_get_element_select'), $file, 'general', array('vicuna-language', $config -> enable_languages));
        add_settings_field('title', __('Title', 'vicuna'), array(&$this, 'get_element_title'), $file, 'general');
        add_settings_field('paging', __('Paging', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-paging', $config -> enable_pagings));
        add_settings_field('author', __('From the posted', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-author', $config -> enable_authors));
        add_settings_field('g_navi_display', __('Global navigation display', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-g_navi-display', $config -> enable_g_navi_display));
        add_settings_field('g_navi_home', __('Global navigation "Home" to display', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-g_navi-home', $config -> enable_g_navi_home));
        add_settings_field('g_navi_pos', __('Global navigation position', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-g_navi-pos', $config -> enable_g_navi_pos));
        add_settings_field('description', __('Description display', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-description', $config -> enable_description));
        add_settings_field('nocenter', __('Centering', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'general', array('vicuna-nocenter', $config -> enable_nocenter));

        add_settings_section('skin', __('Skin', 'vicuna'), array(&$this, 'get_section'), $file);
        add_settings_field('skin', __('Skin', 'vicuna'), array(&$this, '_get_element_select'), $file, 'skin', array('vicuna-skin', $config -> enable_skins));
        add_settings_field('fixed_width', __('Fixed width', 'vicuna'), array(&$this, '_get_element_select'), $file, 'skin', array('vicuna-fixed_width', $config -> enable_fixed_widths));
        add_settings_field('eye_catch', __('Eye catch type', 'vicuna'), array(&$this, '_get_element_select'), $file, 'skin', array('vicuna-eye_catch', $config -> enable_eye_catches)); 
        // add_settings_field('eye_catch_image', __('Eye catch image', 'vicuna'), array(&$this, 'get_element_eye_catch_image'), $file, 'skin');
        add_settings_section('layout', __('Layout', 'vicuna'), array(&$this, 'get_section'), $file);
        add_settings_field('index_layout', __('Index layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'index'));
        add_settings_field('category_layout', __('Category layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'category'));
        add_settings_field('archive_layout', __('Archive layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'archive'));
        add_settings_field('tag_layout', __('Tag layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'tag'));
        add_settings_field('page_layout', __('Page layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'page'));
        add_settings_field('single_layout', __('Single layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'single'));
        add_settings_field('search_layout', __('Search layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => 'search'));
        add_settings_field('404_layout', __('404 layout', 'vicuna'), array(&$this, 'get_element_layout'), $file, 'layout', array('template' => '404'));

        add_settings_section('enable_color', __('Enable color', 'vicuna'), array(&$this, 'get_section'), $file);
        add_settings_field('enable_color', __('Enable color', 'vicuna'), array(&$this, '_get_element_radio'), $file, 'enable_color', array('vicuna-enable_color', $config -> enable_enable_color));

        add_settings_section('color', __('Color', 'vicuna'), array(&$this, 'get_section'), $file);
        add_settings_field('color', __('Color information', 'vicuna'), array(&$this, 'get_element_color_information'), $file, 'color');
        add_settings_field('search', __('Search', 'vicuna'), array(&$this, 'get_element_color_search'), $file, 'color');
        add_settings_field('popular', __('Popular color', 'vicuna'), array(&$this, 'get_element_popular_color'), $file, 'color');
        add_settings_field('recent', __('Recent upload', 'vicuna'), array(&$this, 'get_element_recent_upload'), $file, 'color');
        add_settings_field('list', __('Color list', 'vicuna'), array(&$this, 'get_element_color_list'), $file, 'color');

        ?><div class="wrap">
    <h2><?php _e('Vicuna Theme Manager', 'vicuna')?></h2>
    <?php $this -> notices()?>
    <ul id="tabs1" class="tabs">
        <li><a href="#" id="tab0" class="current"><?php _e('General', 'vicuna')?></a></li>
        <li><a href="#" id="tab1"><?php _e('Skin', 'vicuna')?></a></li>
        <li><a href="#" id="tab2"><?php _e('Layout', 'vicuna')?></a></li>
        <li><a href="#" id="tab3"><?php _e('Color', 'vicuna')?></a></li>
    </ul>
    <form method="post" action="<?php echo getenv('REQUEST_URI')?>" id="themeManagerForm">
        <?php settings_fields($this -> opt_group)?>
        <table class="form-table"><?php do_settings_fields($file, 'general')?></table>
        <table class="form-table"><?php do_settings_fields($file, 'skin')?></table>
        <table class="form-table"><?php do_settings_fields($file, 'layout')?></table>
        <div class="form-div">
        	<table><?php do_settings_fields($file, 'enable_color')?></table>
        	<table><?php do_settings_fields($file, 'color')?></table>
        </div>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'vicuna')?>" />
            <?php if (get_option('vicuna_config')):?>
            <input type="button" class="button" id="transition" value="<?php _e('wp.Vicuna.ext take over the settings', 'vicuna')?>" />
        	<?php endif?>
        </p>
    </form>
    <h3><?php _e('Sample', 'vicuna')?></h3>
    <p><?php _e('Please click the template you want.', 'vicuna')?></p>
    <ul id="sampleChanger" class="tabs">
        <li><a href="index" class="current"><?php _e('Home', 'vicuna')?></a></li>
        <li><a href="category"><?php _e('Category', 'vicuna')?></a></li>
        <li><a href="archive"><?php _e('Archives', 'vicuna')?></a></li>
        <li><a href="tag"><?php _e('Tag', 'vicuna')?></a></li>
        <li><a href="page"><?php _e('Page', 'vicuna')?></a></li>
        <li><a href="single"><?php _e('Article', 'vicuna')?></a></li>
        <li><a href="search"><?php _e('Search result', 'vicuna')?></a></li>
        <li><a href="404"><?php _e('Page not found', 'vicuna')?></a></li>
    </ul>
    <a id="vicunaThemeSampler" class="iframe" href="<?php echo $preview_link?>"><?php _e('Please enable JavaScript.', 'vicuna')?></a>
    <script type="text/javascript">
    //<![CDATA[
    var transitionConfirm = '<?php _e('Previous settings will be deleted. Are you sure?', 'vicuna')?>';
    var transitionFinished = '<?php _e('Took over.', 'vicuna')?>';
    var skin_data = <?php echo json_encode($config -> get_option('vicuna-skin_data'))?>;
    var waitMessage = '<?php _e('Wait...', 'vicuna')?>';
    var themes = <?php echo json_encode($config -> get_option('vicuna-color-themes'))?>;
    var colorSkins = <?php echo json_encode($config -> enable_color_skins)?>;
    //]]>
    </script>
</div><?php
        } 

        /**
         * (non-PHPdoc)
         * 
         * @see wp-content/themes/wp.vicuna/src/VicunaPager#validate($vp)
         */
        function validate($vp)
    {
        $config = &Vicuna :: config();
        foreach($vp as $name => $value) {
            switch ($name) {
            case 'vicuna-language':
                if (!array_key_exists($value, $config -> enable_languages))
                    $this -> errors[] = __('Choose "Language" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-title-front':
            case 'vicuna-title-rear':
                if (!array_key_exists($value, $config -> enable_titles))
                    $this -> errors[] = __('Choose "Title" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-title-separator':
                if (!array_key_exists($value, $config -> enable_separators))
                    $this -> errors[] = __('Choose "Title separator" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-paging':
                if (!array_key_exists($value, $config -> enable_pagings))
                    $this -> errors[] = __('Choose "Paging" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-author':
                if (!array_key_exists($value, $config -> enable_authors))
                    $this -> errors[] = __('Choose "Author" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-skin':
                if (!array_key_exists($value, $config -> enable_skins)) {
                    $this -> errors[] = __('Choose "Skin" is invalid.', 'vicuna');
                } else {
                    $this -> valid_values[$name] = $value;
                    if (isset($config -> skin_data[$value]['Special']))
                        $this -> valid_values['vicuna-special'] = $config -> skin_data[$value]['Special'];
                } 
                break;
            case 'vicuna-eye_catch':
                if (!in_array($value, $config -> enable_eye_catches))
                    $this -> errors[] = __('Choose "Eye catch" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-g_navi-display':
                if (!in_array($value, array('0', '1', '2')))
                    $this -> errors[] = __('Choose "Global navigation display" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-g_navi-home':
                if (!in_array($value, array('0', '1')))
                    $this -> errors[] = __('Choose "Global navigation "Home" to display" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-g_navi-pos':
                if (!in_array($value, array('0', 'gt')))
                    $this -> errors[] = __('Choose "Global navigation position" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-description':
                if (!in_array($value, array('0', '1')))
                    $this -> errors[] = __('Choose "Description display" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-nocenter':
                if (!in_array($value, array('0', 'al')))
                    $this -> errors[] = __('Choose "Centering" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-fixed_width':
                if (!in_array($value, $config -> enable_fixed_widths))
                    $this -> errors[] = __('Choose "Fixed width" is invalid.', 'vicuna');
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-layout-index';
            case 'vicuna-layout-category';
            case 'vicuna-layout-archive';
            case 'vicuna-layout-tag';
            case 'vicuna-layout-page';
            case 'vicuna-layout-single';
            case 'vicuna-layout-search';
            case 'vicuna-layout-404';
                if (!in_array($value, $config -> enable_layouts))
                    $this -> errors[] = sprintf(__('Choose "%s layout" is invalid.', 'vicuna'), $config -> enable_templates[substr($name, 14)]);
                else
                    $this -> valid_values[$name] = $value;
                break;
            case 'vicuna-enable_color':
                $this -> valid_values[$name] = array_key_exists($value, array('1', '0'))? $value: '0';
                break;
            case 'vicuna-color':
                if (!preg_match('/^themeid\*([tm0-9]+),title\*([^,]+),hex\*([0-9a-f]{6})\|([0-9a-f]{6})\|([0-9a-f]{6})\|([0-9a-f]{6})\|([0-9a-f]{6})/i', $value, $matches)) {
                    $this -> valid_values[$name] = array();
                } else {
                    $this -> valid_values[$name] = array('themeid' => $matches[1], 'title' => $matches[2],
                        'hex' => array($matches[3], $matches[4], $matches[5], $matches[6], $matches[7]));
                } 
            } 
        } 
        return empty($this -> errors);
    } 

    /**
     * (non-PHPdoc)
     * 
     * @see wp-content/themes/wp.vicuna/src/VicunaPager#after_update()
     */
    function after_update()
    {
        update_option('vicuna-skin_styles', array());
        update_option('vicuna-color-style', null);
        if (empty($this -> valid_values['vicuna-color'])) return null;
        $config = &Vicuna :: config();
        if ($this -> valid_values['vicuna-color']['themeid'] === 'm0') {
            $last_id = $config -> get_option('vicuna-color-themes-last_id');
            $this -> valid_values['vicuna-color']['themeid'] = 'm' . (++$last_id);
            update_option('vicuna-color-themes-last_id', $last_id);
        } 
        $color_list = $config -> get_option('vicuna-color-themes');
        $color_list[$this -> valid_values['vicuna-color']['themeid']] = $this -> valid_values['vicuna-color'];
        update_option('vicuna-color-themes', $color_list);
    } 
    // Create form element {{{
    /**
     * Get section
     * 
     * @return void 
     */
    function get_section()
    {
    } 

    function _get_element_select($args)
    {
        $config = &Vicuna :: config();
        $current = $config -> get_option($args[0]);
        $rows = array();
        foreach($args[1] as $value => $label) {
            if (is_integer($value)) $value = $label;
            $selected = $current === (string) $value? ' selected="selected"': null;
            $rows[] = sprintf('<option value="%s"%s>%s</option>', $value, $selected, $label);
        } 
        printf('<select name="%1$s" id="%1$s">%2$s</select>', $args[0], implode("\n", $rows));
    } 

    function _get_element_radio($args)
    {
        $config = &Vicuna :: config();
        $current = $config -> get_option($args[0]);
        $rows = array();
        foreach($args[1] as $value => $label) {
            $checked = $current === (string) $value? ' checked="checked"': null;
            printf('<label><input type="radio" name="%s" value="%s"%s />%s</label>',
                $args[0], $value, $checked, $label);
        } 
    } 
    // }}} Create form element
} 
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
?>