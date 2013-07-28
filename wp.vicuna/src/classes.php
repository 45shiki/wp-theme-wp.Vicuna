<?php
/**
 * wp.Vicuna theme classes
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
 * Abstract class for Vicuna configuration page class
 *
 * @package wp.Vicuna
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class VicunaPager {
    /**
     *
     * @var boolean Flag-treated
     */
    var $was_action = false;

    /**
     *
     * @var string Option group name
     */
    var $opt_group = '';

    /**
     *
     * @var array Validation error messages
     */
    var $errors = array();

    /**
     *
     * @var array Validated values
     */
    var $valid_values = array();

    /**
     * Form display
     *
     * @return void
     */
    function display()
    {
    }

    /**
     * Validation
     *
     * @param  $vp array POST parameter
     * @return boolean
     */
    function validate($vp)
    {
    }

    /**
     * Select element create
     *
     * @param  $name string name attribute
     * @param  $options array options
     * @param  $current mixed OPTIONAL current selection
     * @return string select element
     */
    function create_element_select($name, $options, $current = null)
    {
        $rows = array();
        foreach($options as $value => $label) {
            if (is_integer($value)) $value = $label;
            $selected = $current === $value? ' selected="selected"': null;
            $rows[] = sprintf('<option value="%s"%s>%s</option>', $value, $selected, $label);
        }
        return empty($rows)? false: sprintf('<select name="%1$s" id="%1$s">%2$s</select>',
            $name, implode("\n", $rows));
    }

    /**
     * Notification
     *
     * @return void
     */
    function notices()
    {
        if ($this -> was_action !== true) return null;
        if (empty($this -> errors)) {
            printf('<div id="message" class="updated fade"><p>%s</p></div>', __('Saved.'));
        } else {
            printf('<div id="message" class="error fade"><ul><li>%s</li></ul></div>',
                implode('</li><li>', $this -> errors));
        }
    }

    /**
     * Pager action
     *
     * @return void
     */
    function action()
    {
        $config = &Vicuna :: config();
        $config -> set_default();
        if (isset($_POST['action']) && $_POST['action'] === 'update' &&
                wp_verify_nonce($_POST['_wpnonce'], "{$this->opt_group}-options")) {
            if ($this -> validate(option_update_filter($_POST)) === true) {
                $this -> update();
            }
            $this -> was_action = true;
        }
    }

    /**
     * Settings update
     *
     * @return void
     */
    function update()
    {
        $this -> before_update();
        $config = &Vicuna :: config();
        foreach($this -> valid_values as $name => $value) {
            if (in_array($name, $config -> enable_options))
                update_option($name, $value);
        }
        $this -> after_update();
    }

    /**
     * Callback method called before update
     *
     * @return void
     */
    function before_update()
    {
    }

    /**
     * Callback method called after update
     *
     * @return void
     */
    function after_update()
    {
    }

    /**
     * SINGLETON Get instance
     *
     * @return object
     */
    function &i($class)
    {
        static $i;
        if (empty($i)) {
            $i = new $class;
        }
        return $i;
    }

    /**
     * Constructor for PHP4
     *
     * @return void
     */
    function VicunaPager()
    {
        $this -> __construct();
    }

    /**
     * Constructor
     *
     * @return void
     */
    function __construct()
    {
        $this -> action();
    }
}

/**
 * Widget controller
 *
 * @package wp.Vicuna
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class VicunaWidget {
    /**
     *
     * @var array Enable sidebars
     */
    var $enable_sidebars = array('navi', 'others');

    /**
     *
     * @var string HTML elements in before of the widget.
     */
    var $before_widget = '<dt>';

    /**
     *
     * @var string HTML elements in after of the widget.
     */
    var $after_widget = "</dd>\n";

    /**
     *
     * @var string HTML elements in before of the widget title.
     */
    var $before_title = '';

    /**
     *
     * @var string HTML elements in after of the widget title.
     */
    var $after_title = "</dt>\n<dd class=\"%s\">";
    // {{{ Actions
    /**
     * Action hooked for _widget_init
     *
     * @return void
     */
    function action_widget_init()
    {
        register_widget('Vicuna_Widget_Search');
        register_widget('Vicuna_Widget_Calendar');
        register_widget('Vicuna_Widget_Meta');
        register_widget('Vicuna_Widget_Tag_Cloud');
        register_widget('Vicuna_Widget_Recent_Reactions');
        register_widget('Vicuna_Widget_Recent_Comments');
        register_widget('Vicuna_Widget_Recent_Pings');
        register_widget('Vicuna_Widget_Pages');
    }
    // }}} Actions
    /**
     * Constructor
     *
     * @return void
     */
    function VicunaWidget()
    {
        add_action('widgets_init', array($this, 'action_widget_init'));
        foreach($this -> enable_sidebars as $sidebar)
        register_sidebar(array('name' => $sidebar, 'id' => $sidebar,
                'before_widget' => $this -> before_widget,
                'after_widget' => $this -> after_widget,
                'before_title' => $this -> before_title,
                'after_title' => $this -> after_title,
                ));
    }
}
// {{{ Widget classes
/**
 * Search form widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Search extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        $title = empty($instance['title']) ? __('Search', 'vicuna') : $instance['title'];
        $args['after_title'] = sprintf($args['after_title'], 'search');
        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];

        ?>
			<form method="get" action="<?php bloginfo('home')?>/">
				<fieldset>
					<legend><label for="searchKeyword"><?php printf(__('Search %s', 'vicuna'), get_bloginfo('name'))?></label></legend>
					<div>
						<input type="text" class="inputField" id="searchKeyword"  name="s" size="10" />
						<input type="submit" class="submit" id="submit" value="Search" />
					</div>
				</fieldset>
			</form>
<?php
        echo $args['after_widget'];
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%s">%s <input class="widefat" id="%s" name="%s" type="text" value="%s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'), $this -> get_field_id('title'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Search()
    {
        parent :: WP_Widget(false, __('Search (Vicuna)', 'vicuna'));
        unregister_widget('WP_Widget_Search');
    }
}

/**
 * Calendar widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Calendar extends WP_Widget {
    /**
     *
     * @var array Weekday names
     */
    var $weekday = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        global $wpdb, $m, $monthnum, $year, $timedifference, $wp_locale, $posts;
        $args['after_title'] = sprintf($args['after_title'], 'calendar');
        if (!$wpdb -> get_var("SELECT COUNT(`ID`) from `{$wpdb->posts}` WHERE `post_type` = 'post' AND `post_status` = 'publish'"))
            return;

        $w = isset($_GET['w'])? (string) intval($_GET['w']): null;
        if (!empty($monthnum) && !empty($year)) {
            $current = sprintf('%04d-%02d', $year, $monthnum);
        } elseif (!empty($w)) {
            $current = date('Y-m', strtotime("+{$w} week", mktime(0, 0, 0, 1, 1, substr($m, 0, 4))));
        } elseif (!empty($m)) {
            $current = date('Y-m', strtotime("{$m}01"));
        } else {
            $current = gmdate('Y-m', current_time('timestamp'));
        }
        list($cyear, $cmonth) = explode('-', $current);

        if (!$widget = Vicuna :: cache_get($current, 'calendar')) {
            $prev = $wpdb -> get_var($wpdb -> prepare("SELECT MAX(`post_date`) FROM `{$wpdb->posts}`
            	WHERE DATE_FORMAT(`post_date`, '%%Y-%%m') < %s AND `post_type` = 'post' AND
            	`post_status` = 'publish'", $current));
            if (empty($prev)) {
                $prev = '&laquo;';
            } else {
                $prev = explode('-', $prev);
                $prev = sprintf('<a href="%s" title="%%2$s" rel="nofollow">&laquo;</a>',
                    get_month_link($prev[0], $prev[1]));
            }

            $next = $wpdb -> get_var($wpdb -> prepare("SELECT MAX(`post_date`) FROM `{$wpdb->posts}`
            	WHERE DATE_FORMAT(`post_date`, '%%Y-%%m') > %s AND `post_type` = 'post' AND
            	`post_status` = 'publish'", $current));
            if (empty($next)) {
                $next = '&raquo;';
            } else {
                $next = explode('-', $next);
                $next = sprintf('<a href="%s" title="%%3$s" rel="nofollow">&raquo;</a>',
                    get_month_link($next[0], $next[1]));
            }

            $caption = sprintf('<caption>%1$s %%1$s %2$s</caption>', $prev, $next);

            $start_of_week = get_option('start_of_week', 0);
            $weekday = $theads = array();
            for ($i = 0; $i <= 6; $i++) {
                $current_week = $this -> weekday[($i + $start_of_week) % 7];
                $theads[] = sprintf("\t\t\t\t\t<th class=\"%s\" title=\"%s\">%s</th>",
                    strtolower(substr($current_week, 0, 3)), __($current_week, 'vicuna'), substr($current_week, 0, 1));
            }

            $rows = $wpdb -> get_results($wpdb -> prepare("SELECT `post_title`,
        		DAYOFMONTH(`post_date`) as `day` FROM `wp_posts` WHERE DATE_FORMAT(
        		`post_date`, '%%Y-%%m') = %s AND `post_type` = 'post' AND
        		`post_status` = 'publish' ORDER BY `post_date`", $current));
            $separator = strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari')? "\n": ', ';
            $title4days = array();
            foreach ((array) $rows as $row) {
                $title4days[$row -> day] = (isset($title4days[$row -> day])? $title4days[$row -> day] . $separator: null)
                 . str_replace('"', '&quot;', wptexturize($row -> post_title));
            }

            $current_time = strtotime($current . '-1');
            $last_time = strtotime('-1 day', strtotime('+1 month', $current_time));
            $tbody = array();
            $num_space = calendar_week_mod(date('w', $current_time) - $start_of_week);
            for ($i = 0; $i < $num_space; $i++) $tbody[] = "\t\t\t\t\t<td>&nbsp;</td>";

            $now = gmdate('j', time() + (get_option('gmt_offset') * 3600));
            $tbodies = array();
            do {
                if (count($tbody) >= 7) {
                    $tbodies[] = sprintf('<tr>%s</tr>', implode("\n", $tbody));
                    $tbody = array();
                }
                $day = date('j', $current_time);
                $label = isset($title4days[$day])? sprintf('<a href="%s" title="%s">%s</a>',
                    get_day_link(date('Y', $current_time), date('m', $current_time), $day),
                    $title4days[$day], $day): $day;
                $tbody[] = sprintf("\t\t\t\t\t<td%s>%s</td>", ($day === $now? ' class="today"': null), $label);
                $current_time = strtotime('+1 day', $current_time);
            } while ($current_time <= $last_time);
            while (count($tbody) < 7) $tbody[] = "\t\t\t\t\t<td>&nbsp;</td>";
            $tbodies[] = sprintf('<tr>%s</tr>', implode("\n", $tbody));

            $widget = sprintf('<table class="calendar" cellpadding="0" cellspacing="0" summary="%%4$s">%s<tr>%s</tr>%s</table>%s',
                $caption, implode("\n", $theads), implode("\n", $tbodies), $args['after_widget']);
            Vicuna :: cache_add($current, $widget, 'calendar');
        }
        $title = empty($instance['title']) ? __('Calendar', 'vicuna') : $instance['title'];
        echo $args['before_widget'], $args['before_title'], $title, $args['after_title'] ;
        printf($widget, __($current, 'vicuna'),
            __('Older', 'vicuna'), __('Newer', 'vicuna'), __('Monthly calendar', 'vicuna'));
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%s">%s <input class="widefat" id="%s" name="%s" type="text" value="%s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'), $this -> get_field_id('title'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Calendar()
    {
        parent :: WP_Widget(false, __('Calendar (Vicuna)', 'vicuna'));
        unregister_widget('WP_Widget_Calendar');
    }
}

/**
 * Meta information widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Meta extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        $title = empty($instance['title']) ? __('Meta', 'vicuna') : $instance['title'];
        $args['after_title'] = sprintf($args['after_title'], 'meta');
        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];

        ?>
            <ul>
                <li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Strict" rel="nofollow">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
<?php wp_register()?>
                <li><?php wp_loginout()?></li>
<?php wp_meta()?>
            </ul>
<?php
        echo $args['after_widget'];
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%s">%s <input class="widefat" id="%s" name="%s" type="text" value="%s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'), $this -> get_field_id('title'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Meta()
    {
        parent :: WP_Widget(false, __('Meta (Vicuna)', 'vicuna'));
        unregister_widget('WP_Widget_Meta');
    }
}

/**
 * Tag cloud widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Tag_Cloud extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        $title = empty($instance['title']) ? __('Tag cloud', 'vicuna') : $instance['title'];
        $args['after_title'] = sprintf($args['after_title'], 'tagCloud');
        $widget = sprintf('%s%s%s%s%s%s', $args['before_widget'], $args['before_title'],
            $title, $args['after_title'], vicuna_tag_cloud('echo=0'), $args['after_widget']);
        echo $widget;
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%s">%s <input class="widefat" id="%s" name="%s" type="text" value="%s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'), $this -> get_field_id('title'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Tag_Cloud()
    {
        parent :: WP_Widget(false, __('Tag cloud (Vicuna)', 'vicuna'));
        unregister_widget('WP_Widget_Tag_Cloud');
    }
}

/**
 * Recent reactions widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Recent_Reactions extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        if (empty($instance['number'])) $instance['number'] = 5;
        elseif ($instance['number'] < 1) $instance['number'] = 1;
        elseif ($instance['number'] > 15) $instance['number'] = 15;
        $args['after_title'] = sprintf($args['after_title'], 'recentReactions');
        $title = empty($instance['title']) ? __('Recent reactions', 'vicuna') : $instance['title'];
        $theme = &Vicuna :: theme();
        $widget = sprintf('%s%s%s%s%s%s', $args['before_widget'], $args['before_title'],
            $title, $args['after_title'], $theme -> recent_comments_list('echo=0&id=recent_reactions&number=' . $instance['number']), $args['after_widget']);
        echo $widget;
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['number'])) $instance['number'] = 5;
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%1$s">%2$s <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
        printf('<p><label for="%1$s">%2$s <input style="width: 25px; text-align: center;" id="%1$s" name="%3$s" type="text" value="%4$s" /></label> %5$s</p>',
            $this -> get_field_id('number'), __('Number of reactions to show:', 'vicuna'),
            $this -> get_field_name('number'), esc_attr($instance['number']), __('(at most 15)'));
        printf('<p>%s</p>', __('Display recent comments and pingbacks and trackback.', 'vicuna'));
    }

    /**
     *
     * @see WP_Widget::update
     */
    function update($new_instance, $old_instance)
    {
        Vicuna :: cache_del('recent_reactions', 'recent_comments_list');
        return $new_instance;
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Recent_Reactions()
    {
        parent :: WP_Widget(false, __('Recent reactions (Vicuna)', 'vicuna'));
    }
}

/**
 * Recent comment widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Recent_Comments extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        if (empty($args['number'])) $args['number'] = 5;
        elseif ($args['number'] < 1) $args['number'] = 1;
        elseif ($args['number'] > 15) $args['number'] = 15;
        $args['after_title'] = sprintf($args['after_title'], 'recentComments');
        $title = empty($instance['title']) ? __('Recent comments', 'vicuna') : $instance['title'];
        $theme = &Vicuna :: theme();
        $widget = sprintf('%s%s%s%s%s%s', $args['before_widget'], $args['before_title'],
            $title, $args['after_title'], $theme -> recent_comments_list('echo=0&id=recent_comments&type=comment&number=' . $args['number']), $args['after_widget']);
        echo $widget;
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['number'])) $instance['number'] = 5;
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%1$s">%2$s <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
        printf('<p><label for="%1$s">%2$s <input style="width: 25px; text-align: center;" id="%1$s" name="%3$s" type="text" value="%4$s" /></label> %5$s</p>',
            $this -> get_field_id('number'), __('Number of comments to show:', 'vicuna'),
            $this -> get_field_name('number'), esc_attr($instance['number']), __('(at most 15)'));
    }

    /**
     *
     * @see WP_Widget::update
     */
    function update($new_instance, $old_instance)
    {
        Vicuna :: cache_del('recent_comments', 'recent_comments_list');
        return $new_instance;
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Recent_Comments()
    {
        parent :: WP_Widget(false, __('Recent comments (Vicuna)', 'vicuna'));
        unregister_widget('Widget_Recent_Comments');
    }
}

/**
 * recent pings widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Recent_Pings extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        if (empty($args['number'])) $args['number'] = 5;
        elseif ($args['number'] < 1) $args['number'] = 1;
        elseif ($args['number'] > 15) $args['number'] = 15;
        $args['after_title'] = sprintf($args['after_title'], 'recentPings');
        $title = empty($instance['title']) ? __('Recent pings', 'vicuna') : $instance['title'];
        $theme = &Vicuna :: theme();
        $widget = sprintf('%s%s%s%s%s%s', $args['before_widget'], $args['before_title'],
            $title, $args['after_title'],
            $theme -> recent_comments_list('echo=0&id=recent_pings&type=pingback&number=' . $args['number']),
            $args['after_widget']);
        echo $widget;
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['number'])) $instance['number'] = 5;
        if (!isset($instance['title'])) $instance['title'] = null;
        printf('<p><label for="%1$s">%2$s <input class="widefat" id="%1$s" name="%3$s" type="text" value="%4$s" /></label></p>',
            $this -> get_field_id('title'), __('Title:'),
            $this -> get_field_name('title'), esc_attr($instance['title']));
        printf('<p><label for="%1$s">%2$s <input style="width: 25px; text-align: center;" id="%1$s" name="%3$s" type="text" value="%4$s" /></label> %5$s</p>',
            $this -> get_field_id('number'), __('Number of pings to show:', 'vicuna'),
            $this -> get_field_name('number'), esc_attr($instance['number']), __('(at most 15)'));
    }

    /**
     *
     * @see WP_Widget::update
     */
    function update($new_instance, $old_instance)
    {
        Vicuna :: cache_del('recent_pings', 'recent_comments_list');
        return $new_instance;
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Recent_Pings()
    {
        parent :: WP_Widget(false, __('Recent pings (Vicuna)', 'vicuna'));
    }
}

/**
 * Page list widget
 *
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class Vicuna_Widget_Pages extends WP_Widget {
    /**
     *
     * @see WP_Widget::widget
     */
    function widget($args, $instance)
    {
        $title = empty($instance['title']) ? __('Pages', 'vicuna') : $instance['title'];
        $args['after_title'] = sprintf($args['after_title'], 'pages');
        $widget = sprintf('%s%s%s%s<ul class="pages">%s</ul>%s', $args['before_widget'], $args['before_title'],
            $title, $args['after_title'],
            wp_list_pages(array('sort_column' => $instance['sortby'], 'title_li' => 0, 'exclude' => $instance['exclude'], 'echo' => 0)),
            $args['after_widget']);
        echo $widget;
    }

    /**
     *
     * @see WP_Widget::form
     */
    function form($instance)
    {
        if (!isset($instance['title'])) $instance['title'] = null;
        if (!isset($instance['exclude'])) $instance['exclude'] = null;
        if (!isset($instance['sortby'])) $instance['sortby'] = 'menu_order';

        ?>
        <p><label for="<?php echo $this -> get_field_id('title')?>"><?php _e('Title:')?> <input class="widefat" id="<?php echo $this -> get_field_id('title')?>" name="<?php echo $this -> get_field_name('title')?>" type="text" value="<?php echo esc_attr($instance['title'])?>" /></label></p>
        <p><label for="pages-sortby"><?php _e('Sort by:');

        ?>
                <select name="<?php echo $this -> get_field_name('sortby')?>" id="<?php echo $this -> get_field_id('sortby')?>">
                        <option value="post_title"<?php selected($instance['sortby'], 'post_title')?>><?php _e('Page title')?></option>
                        <option value="menu_order"<?php selected($instance['sortby'], 'menu_order')?>><?php _e('Page order')?></option>
                        <option value="ID"<?php selected($instance['sortby'], 'ID')?>><?php _e('Page ID')?></option>
                </select></label></p>
        <p><label for="<?php echo $this -> get_field_id('exclude')?>"><?php _e('Exclude:')?> <input type="text" value="<?php echo $instance['exclude']?>" name="<?php echo $this -> get_field_name('exclude')?>" id="<?php echo $this -> get_field_id('exclude')?>" class="widefat" /></label><br />
        <small><?php _e('Page IDs, separated by commas.')?></small></p>
<?php
    }

    /**
     * constructor
     */
    function Vicuna_Widget_Pages()
    {
        parent :: WP_Widget(false, __('Pages (Vicuna)', 'vicuna'));
        unregister_widget('WP_Widget_Pages');
    }
}
// }}} Widget classes
/**
 * Theme controller
 *
 * @package wp.Vicuna
 * @author HAYASHI Ryo <ryo@spais.co.jp>
 */
class VicunaTheme {
    /**
     * XML declaration
     *
     * @return void
     */
    function xml_declaration()
    {
        printf("<?xml version=\"1.0\" encoding=\"%s\" ?>\n", get_bloginfo('charset'));
    }

    /**
     * Return search result text
     *
     * @param  $text string Search result text template
     * @return string HTML Element
     */
    function search_result($text)
    {
        global $s;
        $allsearch = &new WP_Query("s=$s&showposts=-1");
        printf($text, wp_specialchars($s, 1), $allsearch -> post_count);
    }

    /**
     * Return first line from the_content()
     *
     * @return string
     */
    function the_content_oneliner()
    {
        global $post;
        $contents = explode("\n", $post -> post_content);
        echo $contents[0];
    }

    /**
     * Paging
     *
     * @param  $args mixed OPTIONAL Paging args
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return string Paging HTML elements
     */
    function paging($args = null, $echo = true)
    {
        $config = &Vicuna :: config();
        $type = $config -> get_option('vicuna-paging');
        $paging = method_exists($this, "paging_{$type}")? call_user_func(array($this, "paging_{$type}"), $args): nulll;
        if ($echo === true) echo $paging;
        return $paging;
    }

    /**
     * Older and newer paging
     *
     * @param  $args mixed OPTIONAL Paging args
     * @return string Paging HTML Elements
     */
    function paging_around($args = null)
    {
        global $wpdb, $wp_query;
        if (is_single()) return null;
        $args = wp_parse_args($args, array('next_label' => __('Newer Entries', 'vicuna'),
                'prev_label' => __('Older Entries', 'vicuna'), 'max_page' => $wp_query -> max_num_pages,
                'paged' => 1, 'class' => 'flip pager', 'id' => 'flip2'));

        if (!empty($wp_query -> query_vars['paged'])) $args['paged'] = $wp_query -> query_vars['paged'];

        $paging = array();
        if ($args['paged'] > 1)
            $paging[] = sprintf("\t<li class=\"newer\"><a href=\"%s\">%s</a></li>",
                previous_posts(false), $args['next_label']);
        if (empty($args['paged']) || $args['paged'] + 1 <= $args['max_page'])
            $paging[] = sprintf("\t<li class=\"older\"><a href=\"%s\">%s</a></li>",
                next_posts($args['max_page'], false), $args['prev_label']);
        if ($args['max_page'] > 1) {
            array_unshift($paging, sprintf('<ul class="%s" id="%s">', $args['class'], $args['id']));
            $paging[] = '</ul>';
        }
        return implode("\n", $paging);
    }

    /**
     * Number paging
     *
     * @deprecated
     * @param  $args mixed OPTIONAL Paging args
     * @return string Paging HTML Elements
     */
    function _paging_numbers($args = null)
    {
        global $wpdb, $wp_query;
        if (is_single()) return null;
        $args = wp_parse_args($args, array('class' => 'flip number_pager', 'id' => 'flip2'));
        $rows = array();
        $current = 0;
        $front = $rear = false;
        $paged = empty($wp_query -> query_vars['paged'])? 1: $wp_query -> query_vars['paged'];
        if ($paged > 1)
            $rows[] = sprintf('<li class="prev"><a href="%s">%s</a></li>',
                get_pagenum_link($wp_query -> query_vars['paged'] - 1), __('&lt; Previous pages', 'vicuna'));
        while (++$current <= $wp_query -> max_num_pages) {
            if ($current < 4 || $current > $wp_query -> max_num_pages - 3 || ($current > $wp_query -> query_vars['paged'] - 3 && $current < $wp_query -> query_vars['paged'] + 3)) {
                $class = $paged === $current? ' class="current"': null;
                $rows[] = sprintf('<li%s><a href="%s">%s</a></li>', $class, get_pagenum_link($current), $current);
            } elseif ($current > $wp_query -> query_vars['paged'] && $front === false) {
                $rows[] = '<li>&nbsp;...</li>';
                $front = true;
            } elseif ($current < $wp_query -> query_vars['paged'] && $rear === false) {
                $rows[] = '<li>&nbsp;...</li>';
                $rear = true;
            }
        }
        if ($paged < $wp_query -> max_num_pages) {
            $next = empty($wp_query -> query_vars['paged'])? 2: $wp_query -> query_vars['paged'] + 1;
            $rows[] = sprintf('<li class="next"><a href="%s">%s</a></li>',
                get_pagenum_link($next), __('Next pages &gt;', 'vicuna'));
        }
        return count($rows) > 1? sprintf('<ul class="%s" id="%s">%s</ul>', $args['class'], $args['id'], implode("\n", $rows)): null;
    }

    /**
     * Number paging
     *
     * @param  $args mixed OPTIONAL Paging args
     * @return string Paging HTML Elements
     */
    function paging_numbers()
    {
        global $wp_rewrite, $wp_query, $paged;
        if (strpos(($paginate_base = get_pagenum_link(1)), '?') || ! $wp_rewrite -> using_permalinks()) {
            $paginate_format = '';
            $paginate_base = add_query_arg('paged', '%#%');
        } else {
            $paginate_format = (substr($paginate_base, -1 , 1) === '/'? '': '/') . user_trailingslashit('page/%#%/', 'paged');
            $paginate_base .= '%_%';
        }
        echo paginate_links(array('base' => $paginate_base,
                'format' => $paginate_format,
                'total' => $wp_query -> max_num_pages,
                'mid_size' => 5,
                'current' => ($paged ? $paged : 1)));
    }

    function paging_pagenavi($args = null)
    {
        if (function_exists('wp_pagenavi')) wp_pagenavi();
        else $this -> paging_numbers($args);
    }

    /**
     * Return page layout classes
     *
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return string Body classes
     */
    function page_layout($echo = true)
    {
        $config = &Vicuna :: config();
        $template = $config -> template_type();
        $layout = $config -> get_option("vicuna-layout-{$template}");
        if (preg_match('/^special([0-9]+)$/', $layout, $matches)) {
            $skin = $config -> get_option('vicuna-skin');
            $skin_data = $config -> get_option('vicuna-skin_data');
            if (isset($skin_data[$skin]['Special'][(int) $matches[1] - 1]))
                $layout = $skin_data[$skin]['Special'][(int) $matches[1] - 1];
            $eye_catch = null;
        } else {
            $eye_catch = $config -> get_option('vicuna-eye_catch');
            if ($eye_catch === 'default') $eye_catch = null;
        }
        $g_navi_pos = $config -> get_option('vicuna-g_navi-pos');
        $nocenter = $config -> get_option('vicuna-nocenter');
        $fixed_width = $config -> get_option('vicuna-fixed_width');
        if ($fixed_width === 'default') $fixed_width = null;

        $classes = array();
        if (!empty($layout)) $classes[] = $layout;
        if (!empty($eye_catch)) $classes[] = $eye_catch;
        if (!empty($g_navi_pos)) $classes[] = $g_navi_pos;
        if (!empty($nocenter)) $classes[] = $nocenter;
        if (!empty($fixed_width)) $classes[] = $fixed_width;
        $classes = implode(' ', $classes);

        if ($echo === true) echo $classes;
        return $classes;
    }

    /**
     * TITLE Element string
     *
     * @return void
     */
    function page_head_title()
    {
        $config = &Vicuna :: config();
        $title_front = $config -> get_option('vicuna-title-front');
        $title_rear = $config -> get_option('vicuna-title-rear');
        $page_title = null;
        if (is_404()) {
            $page_title = __('Error 404', 'vicuna');
        } elseif (is_singular()) {
            $page_title = get_the_title();
        } elseif (is_category()) {
            $page_title = sprintf(__('%s Archive', 'vicuna'), single_cat_title('', false));
        } elseif (is_tag()) {
            $page_title = single_cat_title(null, false);
        } elseif (is_search()) {
            global $s;
            $page_title = wp_specialchars($s, ENT_QUOTES, get_bloginfo('charset'));
        } elseif (is_archive()) {
            $page_title = $this -> archive_title(false);
        }

        if (empty($page_title)) {
            bloginfo('name');
        } else {
            $separator = $config -> enable_separators[$config -> get_option('vicuna-title-separator')];
            $front = $title_front === 'blog_name'? get_bloginfo('name'): $page_title;
            $rear = $title_rear === 'blog_name'? get_bloginfo('name'): $page_title;
            printf('%s %s %s', $front, $separator, $rear);
        }
    }

    /**
     * Blog description HTML element
     *
     * @return void
     */
    function page_description()
    {
        $config = &Vicuna :: config();
        $option = $config -> get_option('vicuna-description');
        if (empty($option)) return null;
        $description = get_bloginfo('description');
        if (!empty($description)) {
            echo '<p class="description">' . $description . '</p>';
        }
    }

    /**
     * Display to global navigation
     *
     * @return void
     */
    function global_navigation()
    {
        $config = &Vicuna :: config();
        $g_navi_display = $config -> get_option('vicuna-g_navi-display');
        $g_navi_home = $config -> get_option('vicuna-g_navi-home');
        $g_navi_pos = $config -> get_option('vicuna-g_navi-pos');
        if (empty($g_navi_display)) return null;

        $list = null;
        if ($g_navi_display === '1') {
            $list = wp_list_pages('sort_column=menu_order&title_li=0&depth=1&echo=0');
        } else if ($g_navi_display === '2') {
            $list = wp_list_categories('title_li=0&hierarchical=0&echo=0');
        }
        if (!empty($g_navi_home))
            $list = sprintf('<li><a href="%1$s" title="%2$s">%2$s</a></li>', get_bloginfo('home'), __('Home')) . $list;
        printf('<ul id="globalNavi">%s</ul>', $list);
    }

    /**
     * Topic path for Category
     *
     * @param  $args mixed OPTIONAL Get category args
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return string Topic path HTML Element
     */
    function category_relay($args = null, $echo = true)
    {
        static $relays = array();
        $args = wp_parse_args($args, array('cat_id' => 0, 'separator' => ' | '));
        if (empty($args['cat_id'])) {
            global $cat;
            $args['cat_id'] = $cat;
        }
        if (!isset($relays[$args['cat_id']])) {
            $cate = &get_category($args['cat_id']);
            $categories = array();
            while ($cate -> category_parent) {
                $cate = &get_category($cate -> category_parent);
                $categories[] = sprintf('<a href="%s">%s</a>', get_category_link($cate -> cat_ID), $cate -> cat_name);
            }
            if (empty($categories)) {
                $relays[$args['cat_id']] = false;
                return null;
            }
            $relays[$args['cat_id']] = sprintf(' &gt; %s', implode($args['separator'], $categories));
        }
        if ($echo === true) echo $relays[$args['cat_id']];
        return $relays[$args['cat_id']];
    }

    /**
     * Topic path for Page
     *
     * @param  $args mixed OPTIONAL Get page args
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return string Topic path HTML Element
     */
    function page_relay($args = '', $echo = true)
    {
        static $relays = array();
        if (!is_page()) return null;
        $key = sha1($args);
        if (!isset($relays[sha1($key)])) {
            $args = wp_parse_args($args, array('depth' => 0, 'show_date' => '', 'date_format' => get_option('date_format'),
                    'child_of' => 0, 'exclude' => '', 'echo' => 1, 'authors' => '', 'separator' => ' | '));
            $args['exclude'] = preg_replace('[^0-9,]', '', $args['exclude']);
            $args['exclude'] = implode(',', apply_filters('wp_list_pages_excludes', explode(',', $args['exclude'])));

            if (!$_pages = get_pages($args)) {
                $relays[$key] = false;
                return null;
            }
            global $wp_query;
            $current_page_id = $wp_query -> get_queried_object_id();
            $pages = $_pages;
            $relay = array();
            while (!is_null($page = array_shift($pages))) {
                if ($current_page_id !== $page -> ID) continue;
                $relay[] = sprintf('<a href="%s">%s</a>', get_permalink($page -> ID), $page -> post_title);
                $current_page_id = $page -> post_parent;
                $pages = $_pages;
            }
            array_shift($relay);
            if (empty($relay)) {
                $relays[$key] = false;
                return null;
            }
            $relays[$key] = sprintf(' &gt; %s', implode($args['separator'], $relay));
        }
        if ($echo === true) echo $relays[$key];
        return $relays[$key];
    }

    /**
     * Date archives list
     *
     * @param  $limit integer OPTIONAL Maximum number of list
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return string List HTML Element
     */
    function archives_link($limit = null, $echo = true)
    {
        $key = sha1($limit . get_locale());
        if (!$archives = Vicuna :: cache_get($key, 'archives_link')) {
            global $wp_locale, $wpdb;

            if (!empty($limit))
                $limit = ' LIMIT ' . (int) $limit;

            $rows = $wpdb -> get_results("SELECT DISTINCT YEAR(`post_date`) AS `year`,
    	       MONTH(`post_date`) AS `month`, count(`ID`) as `count` FROM `{$wpdb->posts}`
    	       WHERE `post_type` = 'post' AND `post_status` = 'publish' GROUP BY `year`,
    	       `month` ORDER BY `post_date` DESC{$limit}");
            if (empty($rows)) return null;
            $archives = array();
            foreach ($rows as $row) {
                $archives[] = sprintf('%s<li><a href="%s" title="%s" rel="nofollow">%04d-%02d</a></li>',
                    "\t", get_month_link($row -> year, $row -> month), $row -> count,
                    $row -> year, $row -> month);
            }
            if (!count($archives)) return null;
            array_unshift($archives, '<ul>');
            $archives[] = '</ul>';
            $archives = implode("\n", $archives);
            Vicuna :: cache_add($key, $archives, 'archives_link');
        }
        if ($echo === true) echo $archives;
        return $archives;
    }

    /**
     * Generate tag cloud
     *
     * @param  $args mixed OPTIONAL get_tags() function args
     * @return string Tag cloud HTML Element
     */
    function tag_cloud($args = null)
    {
        $args = wp_parse_args($args, array('levels' => 6, 'orderby' => 'name',
                'order' => 'ASC', 'exclude' => '', 'include' => '', 'echo' => 1));
        if (!$html = Vicuna :: cache_get(1, 'tag_cloud')) {
            global $wp_rewrite;
            $tags = get_tags(array_merge($args, array('orderby' => 'count', 'order' => 'ASC')));
            if (empty($tags)) return null;

            $tag_counts = $tag_links = $tag_ids = array();
            foreach ((array) $tags as $tag) {
                $tag_counts[$tag -> name] = $tag -> count;
                $tag_links[$tag -> name] = get_tag_link($tag -> term_id);
                if (is_wp_error($tag_links[$tag -> name])) return $tag_links[$tag -> name];
                $tag_ids[$tag -> name] = $tag -> term_id;
            }

            $min_count = min($tag_counts);
            if (($difference = (int) ((max($tag_counts) - $min_count) / $args['levels']) + 1) <= 1) $difference = 1;

            if ($args['orderby'] === 'name')
                uksort($tag_counts, 'strnatcasecmp');
            else
                asort($tag_counts);

            if ($args['order'] === 'DESC')
                $counts = array_reverse($counts, true);

            $rel = (is_object($wp_rewrite) && $wp_rewrite -> using_permalinks()) ? ' rel="tag"' : '';
            $html = array();
            foreach ($tag_counts as $tag_name => $tag_count) {
                $html[] = sprintf('%s<li class="level%d"><a href="%s" title="%s"%s>%s</a></li>',
                    "\t", $args['levels'] - (int) (($tag_count - $min_count) / $difference),
                    clean_url($tag_links[$tag_name]),
                    attribute_escape(sprintf(__('%d Entries', 'vicuna'), $tag_count)),
                    $rel, str_replace(' ', '&nbsp;', wp_specialchars($tag_name))
                    );
            }
            array_unshift($html, '<ul class="tagCloud">');
            $html[] = '</ul>';
            $html = apply_filters('vicuna_tag_cloud', implode("\n", $html), $tags, $args);
            Vicuna :: cache_add(1, $html, 'tag_cloud');
        }
        if (!empty($args['echo'])) echo $html;
        return $html;
    }

    /**
     * Return older post
     *
     * @return mixed If not NULL
     */
    function older_post_exists()
    {
        static $older;
        if (empty($older)) {
            if (is_attachment()) {
                global $post;
                $older = &get_post($post -> post_parent);
            } else {
                $older = &get_previous_post();
            }
        }
        return $older;
    }

    /**
     * Return newer post
     *
     * @return mixed If not NULL
     */
    function newer_post_exists()
    {
        static $newer;
        if (empty($newer)) $newer = &get_next_post();
        return $newer;
    }

    /**
     * Return from the posted
     *
     * @param  $echo TRUE is echo
     * @return mixed If not NULL
     */
    function from_the_posted($echo = true)
    {
        $config = &Vicuna :: config();
        if ($config -> get_option('vicuna-author') === '0') return null;
        $html = printf('<li class="author">%s <span class="name">%s</span></li>', __('Posted by: ', 'vicuna'), get_the_author());
        if ($echo === true) echo $html;
        return $html;
    }

    /**
     * Return current archive page title
     *
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return string Archive page title
     */
    function archive_title($echo = true)
    {
        if (is_day()) $title = get_the_time(__('Y-m-d', 'vicuna'));
        elseif (is_month()) $title = get_the_time(__('Y-m', 'vicuna'));
        elseif (is_year()) $title = get_the_time(__('Y', 'vicuna'));
        elseif (is_author()) $title = __('Author', 'vicuna');
        else return null;
        $title = sprintf(__('%s Archive', 'vicuna'), $title);
        if ($echo === true) echo $title;
        return $title;
    }

    /**
     * Separate comments and trackbacks
     *
     * @param  $comments array Comments and trackbacks
     * @return array Separated comments and trackbacks
     */
    function comments_and_trackpings($comments)
    {
        $reaction = array('comments' => array(), 'trackpings' => array());
        foreach ((array)$comments as $comment) {
            if ($comment -> comment_approved === 'spam') continue;
            $reactionType = $comment -> comment_type === 'trackback' || $comment -> comment_type === 'pingback'? 'trackpings': 'comments';
            $reaction[$reactionType][] = $comment;
        }
        return $reaction;
    }

    /**
     * Return comments list
     *
     * Enable args
     * - number => Interger Number of comments
     * - echo => Integer Echo element
     * - type => Mixed List comment type(comment, trackback, pingback, NULL)(NULL = all type)
     *
     * @param  $args mixed OPTIONAL
     * @return string HTML Element
     */
    function recent_comments_list($args = null)
    {
        $args = wp_parse_args($args, array('number' => 5, 'echo' => 1, 'type' => null, 'id' => 'recent_comments'));
        if (!$html = Vicuna :: cache_get($args['id'], 'recent_comments_list')) {
            global $wpdb, $comments, $comment;
            $args['number'] = (int) $args['number'];
            if (empty($args['type']))
                $comments = $wpdb -> get_results("SELECT `comment_author`, `comment_author_url`,
                    `comment_ID`, `comment_post_ID`, `comment_date` FROM `{$wpdb->comments}`
                    WHERE `comment_approved` = '1' ORDER BY `comment_date_gmt` DESC LIMIT {$args['number']}");
            else {
                if ($args['type'] === 'comment') $args['type'] = '';
                $comments = $wpdb -> get_results($wpdb -> prepare("SELECT `comment_author`,
                    `comment_author_url`, `comment_ID`, `comment_post_ID`, `comment_date`
                    FROM `{$wpdb->comments}` WHERE `comment_approved` = '1' AND `comment_type` = %s
                    ORDER BY `comment_date_gmt` DESC LIMIT {$args['number']}", $args['type']));
            }
            $post_ID = -1;
            $rows = array(sprintf('<ul id="%s">', $args['id']));
            foreach ($comments as $comment) {
                if ($comment -> comment_post_ID !== $post_ID) {
                    if ($post_ID >= 0) $rows[] = '</ul></li>';
                    $post_ID = $comment -> comment_post_ID;
                    $rows[] = sprintf('<li class="comment_on"><a href="%s#comments">%s</a><ul>',
                        get_permalink($post_ID), get_the_title($post_ID));
                }
                $rows[] = sprintf('<li class="comment_author"><a href="%s#%s%s">%s %s</a></li>',
                    get_permalink($post_ID), $args['type'], $comment -> comment_ID,
                    get_comment_time('Y-m-d'), get_comment_author());
            }
            $rows[] = '</ul></li></ul>';
            $html = implode("\n", $rows);
            Vicuna :: cache_add($args['id'], $html, 'recent_comments_list');
        }
        if (!empty($args['echo'])) echo $html;
        return $html;
    }

    /**
     * Include footer Kuler information
     */
    function color_information()
    {
        $config = &Vicuna :: config();
        if (!$enable_color = $config -> get_option('vicuna-enable_color')) return null;
        if (!$theme = $config -> get_option('vicuna-color')) return null;
        printf('<li class="colorTheme">Colored by <a href="http://kuler.adobe.com/">kuler</a> - <a href="http://kuler.adobe.com/#themeID/%d">%s</a></li>',
            $theme['themeid'], $theme['title']);
    }

    /**
     * Structuring color property
     *
     * Style sheets can specify the color property
     * - blend_type (multiplication, overlay, screen, softlight, hardlight, darken, lighten, difference, exclusion)
     * - blend_color (Compliance to the style sheet -> #FFF, #f5f5f5, rgb(128,128,128), rgb(50%,50%,50%))
     * - blend_density (0% - 100%)
     * - blend_index (0 or 1 -> If the foreground color 0 is based)
     *
     * Property format (No space -> / *{}* /)
     * / *{COLOR1}* / / *{COLOR1 blend_type:overlay blend_color:#000 blend_density:50%}* /
     *
     * @param  $prop_string string Property string
     * @return array Formated properties
     */
    function color_property($prop_string, $theme)
    {
        $prop = array('blend_type' => null, 'blend_density' => 1, 'blend_color' => null, 'blend_index' => 0);
        $enable_types = array('multiplication', 'overlay', 'screen', 'softlight', 'hardlight',
            'darken', 'lighten', 'difference', 'exclusion');
        foreach(explode(' ', $prop_string) as $arg) {
            $arg = explode(':', trim($arg));
            if (count($arg) < 2) continue;
            $arg[0] = strtolower(trim($arg[0]));
            $arg[1] = strtolower(trim($arg[1]));
            if ($arg[0] === 'blend_color') {
                if (preg_match('/^COLOR([1-5])$/i', $arg[1], $matches))
                    $prop[$arg[0]] = $this -> color_calc($theme['hex'][$matches[1]-1]);
                else
                    $prop[$arg[0]] = $this -> color_calc($arg[1]);
            } elseif ($arg[0] === 'blend_density') {
                $prop[$arg[0]] = (int) preg_replace('/%/', null, $arg[1]);
                if ($prop[$arg[0]] > 0) $prop[$arg[0]] = $prop[$arg[0]] / 100;
            } elseif ($arg[0] === 'blend_index') {
                $prop[$arg[0]] = $arg[1] === '1'? 1: 0;
            } elseif ($arg[0] === 'blend_type' && in_array($arg[1], $enable_types)) {
                $prop[$arg[0]] = $arg[1];
            }
        }
        if (empty($prop['blend_color'])) $prop['blend_type'] = null;
        return $prop;
    }

    /**
     * Color calculation
     *
     * FFFFFF -> array(255,255,255)
     * array(127,127,127) -> 7f7f7f
     *
     * @param  $arg mixed Color
     * @return mixed Color
     */
    function color_calc($arg)
    {
        if (is_array($arg)) {
            $return = sprintf('%02x%02x%02x', $arg[0], $arg[1], $arg[2]);
        } elseif (preg_match('/^#?([0-9a-f])([0-9a-f])([0-9a-f])$/i', $arg, $matches)) {
            $return = array(hexdec($matches[1] . $matches[1]), hexdec($matches[2] . $matches[2]), hexdec($matches[3] . $matches[3]));
        } elseif (preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $arg, $matches)) {
            $return = array(hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
        } elseif (preg_match('/^rgb\([\s]*([0-9]{1,3})[\s]*,[\s]*([0-9]{1,3})[\s]*,[\s]*([0-9]{1,3})[\s]*\)$/i', $arg, $matches)) {
            $return = array((int) $matches[1], (int) $matches[2], (int) $matches[3]);
            if ($return[0] > 255) $return[0] = 255;
            if ($return[1] > 255) $return[1] = 255;
            if ($return[2] > 255) $return[2] = 255;
        } elseif (preg_match('/^rgb\([\s]*([0-9]{1,3})%[\s]*,[\s]*([0-9]{1,3})%[\s]*,[\s]*([0-9]{1,3})%[\s]*\)$/i', $arg, $matches)) {
            $return = array((int) (255 * ($matches[1] / 100)), (int) (255 * ($matches[3] / 100)), (int) (255 * ($matches[3] / 100)));
            if ($return[0] > 255) $return[0] = 255;
            if ($return[1] > 255) $return[1] = 255;
            if ($return[2] > 255) $return[2] = 255;
        } else {
            $return = null;
        }
        return $return;
    }

    /**
     * Color blend calculation
     *
     * @param  $color string Base color
     * @param  $args array Blend property
     * @return string Blended color
     */
    function color_blend_calc($color, $args)
    {
        $color = $this -> color_calc($color);
        for($i = 0; $i < 3; $i++) {
            if ($args['blend_index'] === 1) {
                $back = $args['blend_color'][$i] > 0? $args['blend_color'][$i] / 255: 0;
                $fore = $color[$i] > 0? $color[$i] / 255: 0;
            } else {
                $back = $color[$i] > 0? $color[$i] / 255: 0;
                $fore = $args['blend_color'][$i] > 0? $args['blend_color'][$i] / 255: 0;
            }

            switch ($args['blend_type']) {
            case 'multiplication':
                $dist = $back * $fore;
                break;
            case 'overlay':
                $dist = $back < 0.5? 2 * $back * $fore: 1 - 2 * (1 - $back) * (1 - $fore);
                break;
            case 'screen':
                $dist = 1 - (1 - $back) * (1 - $fore);
                break;
            case 'softlight':
                $dist = $fore < 0.5? $back + ($back - $back ^ 2) * (2 * $fore - 1):
                ($back <= (32 / 255)?
                    $back + ($back - $back ^ 2) * (2 * $fore - 1) * (3 - 8 * $back):
                    $back + ($back ^ 0.5 - $back) * (2 * $fore - 1));
                break;
            case 'hardlight':
                $dist = $fore < 0.5? 2 * $back * $fore: 1 - 2 * (1 - $back) * (1 - $fore);
                break;
            case 'darken':
                $dist = $back > $fore? $fore: $back;
                break;
            case 'lighten':
                $dist = $back < $fore? $fore: $back;
                break;
            case 'difference':
                $dist = abs($back - $fore);
                break;
            case 'exclusion':
                $dist = (1 - $back) * $fore + (1 - $fore) * $back;
                break;
            default:
                continue;
            }

            $color[$i] = 255 * (($dist * $args['blend_density']) + ($back * (1 - $args['blend_density'])));
        }
        return $color;
    }
    // {{{ Filter and hooks
    /**
     * Action hooked for wp
     *
     * @return void
     */
    function action_wp()
    {
        wp_enqueue_style('vicuna', get_bloginfo('stylesheet_url'));
        wp_enqueue_script('vicuna-common', get_bloginfo('template_url')
             . '/js/common.js', array(), false, true);
        if (is_singular()) {
            wp_enqueue_script('comment-reply');
            wp_enqueue_script('vicuna-coookie', get_bloginfo('template_url')
                 . '/js/cookie.js', array(), false, true);
        }

        $config = &Vicuna :: config();
        if (!$config -> get_option('vicuna-enable_color')) return null;

        $skin = $config -> get_option('vicuna-skin');
        $color_stylesheet = dirname(dirname(__FILE__)) . DS . 'skins' . DS . $skin . DS . 'color.css';
        if (!is_readable($color_stylesheet)) return null;
        $style = get_option('vicuna-color-style');

        if (!isset($style[$skin]) || filemtime($color_stylesheet) > $style[$skin]['filemtime'] || VICUNA_IS_PREVIEW || WP_DEBUG) {
            $style[$skin] = array('style' => file_get_contents($color_stylesheet),
                'filemtime' => filemtime($color_stylesheet));
            if (preg_match_all('/(\/\*\{COLOR([1-5])([^\}]*)\}\*\/)/im', $style[$skin]['style'], $matches)) {
                if (!$theme = $config -> get_option('vicuna-color')) return null;
                $replace = array();
                $i = -1;
                while (isset($matches[1][++$i])) {
                    $color = $theme['hex'][$matches[2][$i] - 1];
                    $matches[3][$i] = trim($matches[3][$i]);
                    if (!empty($matches[3][$i]))
                        $color = $this -> color_calc($this -> color_blend_calc($color, $this -> color_property($matches[3][$i], $theme)));
                    $style[$skin]['style'] = preg_replace('/' . preg_quote($matches[0][$i], '/') . '/', '#' . $color, $style[$skin]['style']);
                }
            }
            if (!VICUNA_IS_PREVIEW)
                update_option('vicuna-color-style', $style);
        }
        $this -> style = $style[$skin]['style'];
    }

    /**
     * Action hooked for wp_head
     *
     * @return void
     */
    function action_wp_head()
    {
        if ($description = get_bloginfo('description')) {
            printf("\t<meta name=\"description\" content=\"%s\" />\n", $description);
        }
        if (!is_home()) {
            printf("\t<link rel=\"start\" href=\"%s\" title=\"%s Home\" />\n",
                get_bloginfo('home'), get_bloginfo('name'));
        }
        if (is_single()) {
            if ($older_post = $this -> older_post_exists())
                printf('<link rel="prev" href="%s" title="%s" />', get_permalink($older_post -> ID),
                    apply_filters('the_title', $older_post -> post_title, $older_post));
            if ($newer_post = $this -> newer_post_exists())
                printf('<link rel="next" href="%s" title="%s" />', get_permalink($newer_post -> ID),
                    apply_filters('the_title', $newer_post -> post_title, $newer_post));
        }

        if (empty($this -> style)) return null;

        ?>
        <style type="text/css">
        <!--
        <?php echo $this -> style?>
        -->
        </style>
<?php
    }

    /**
     * Filter hooked for posts_fields_request
     *
     * @see WP_Query::get_posts()
     * @param  $fields string Fields
     * @return string Fields
     */
    function filter_posts_fields_request($fields)
    {
        global $wpdb;
        $fields .= ", (SELECT COUNT(`comment_ID`) AS `count` FROM `{$wpdb->comments}`
        	WHERE `comment_post_ID` = `{$wpdb->posts}`.`ID` AND `comment_approved` =
        	'1' AND `comment_type` != '') AS `ping_count`";
        return $fields;
    }

    /**
     * Filter hooked for the_posts
     *
     * @return array posts
     */
    function filter_the_posts($posts)
    {
        $i = -1;
        while (isset($posts[++$i]))
        $posts[$i] -> comment_count -= $posts[$i] -> ping_count;
        return $posts;
    }

    function filter_wp_list_pages($var)
    {
        return preg_replace('/(current[^ "\']+)/', '$1 current', $var);
    }

    function filter_wp_list_categories($var)
    {
        return preg_replace('/(current[^ "\']+)/', '$1 current', $var);
    }

    /**
     * Constructor
     *
     * @return void
     */
    function VicunaTheme()
    {
        load_textdomain('vicuna', dirname(__FILE__) . '/languages/' . get_locale() . '.mo');
        add_action('wp', array($this, 'action_wp'));
        add_action('wp_head', array($this, 'action_wp_head'));
        add_filter('posts_fields_request', array($this, 'filter_posts_fields_request'));
        add_filter('the_posts', array($this, 'filter_the_posts'));
        add_filter('page_head_title', array($this, 'filter_page_head_title'));
        add_filter('wp_list_pages', array($this, 'filter_wp_list_pages'));
        add_filter('wp_list_categories', array($this, 'filter_wp_list_categories'));
    }
}

/**
 * Vicuna configure
 *
 * @package wp.Vicuna
 * @version 0.1.0
 * @author HAYASHI Ryo <ryo@spais.co.jp>
 */
class VicunaConfig {
    var $color_api = 'http://vicuna.spais.co.jp/api/color';
    var $enable_color_api_actions = array('all', 'search', 'popular', 'recent');

    /**
     *
     * @var array Enable vicuna options
     */
    var $enable_options = array('vicuna-skin', 'vicuna-eye_catch', 'vicuna-g_navi-display',
        'vicuna-g_navi-home', 'vicuna-g_navi-pos', 'vicuna-description', 'vicuna-nocenter',
        'vicuna-fixed_width', 'vicuna-layout-index', 'vicuna-layout-category',
        'vicuna-layout-archive', 'vicuna-layout-tag', 'vicuna-layout-page', 'vicuna-layout-single',
        'vicuna-layout-search', 'vicuna-layout-404', 'vicuna-language', 'vicuna-title-front',
        'vicuna-title-rear', 'vicuna-title-separator', 'vicuna-paging', 'vicuna-author',
        'vicuna-skin_data', 'vicuna-color-themes', 'vicuna-color', 'vicuna-enable_color',
        'vicuna-color-themes-last_id', 'vicuna-eye_catch-image');

    /**
     *
     * @var array Enable language options
     */
    var $enable_languages = array();

    /**
     *
     * @var array Enable eye-catch options
     */
    var $enable_eye_catches = array();

    /**
     *
     * @var array Enable layout options
     */
    var $enable_layouts = array();

    /**
     *
     * @var array Enable templates
     */
    var $enable_templates = array();

    /**
     *
     * @var array Enable fixed width options
     */
    var $enable_widths = array();

    /**
     *
     * @var array Enable title options
     */
    var $enable_titles = array();

    /**
     *
     * @var array Enable separator options
     */
    var $enable_separators = array('pipe' => '|', 'hyphen' => '-', 'greater' => '>', 'less' => '<');

    /**
     *
     * @var array Enable paging options
     */
    var $enable_pagings = array();

    /**
     *
     * @var array Enable skins
     */
    var $enable_skins = array();

    /**
     *
     * @var array Enable authors
     */
    var $enable_authors = array();

    var $enable_g_navi_display = array();

    var $enable_g_navi_home = array();

    var $enable_g_navi_pos = array();

    var $enable_description = array();

    var $enable_nocenter = array();

    var $enable_enable_color = array();

    var $enable_color_skins = array();

    var $default_enable_layouts = array();

    var $default_enable_eye_catches = array();

    var $default_enable_fixed_widths = array();

    /**
     *
     * @var array Default options
     */
    var $default_options = array('vicuna-language' => '0', 'vicuna-skin' => 'style-vega', 'vicuna-eye_catch' => '0',
        'vicuna-g_navi-display' => '0', 'vicuna-g_navi-home' => '0', 'vicuna-g_navi-pos' => '0',
        'vicuna-description' => '1', 'vicuna-nocenter' => '0', 'vicuna-fixed_width' => '0',
        'vicuna-layout-index' => 'double', 'vicuna-layout-category' => 'double',
        'vicuna-layout-archive' => 'double', 'vicuna-layout-tag' => 'double',
        'vicuna-layout-page' => 'single', 'vicuna-layout-single' => 'single',
        'vicuna-layout-search' => 'double', 'vicuna-layout-404' => 'single', 'vicuna-language' => 'ja',
        'vicuna-title-front' => 'entry_title', 'vicuna-title-rear' => 'blog_name',
        'vicuna-title-separator' => 'hyphen', 'vicuna-paging' => 'around',
        'vicuna-author' => '1', 'vicuna-skin_data' => array(),
        'vicuna-color' => array('themeid' => 'm1', 'title' => 'Sweat stain', 'hex' => array('f0f0f0', 'e8eadd' ,'333333' ,'34bfed','ea7323')),
        'vicuna-color-themes' => array(), 'vicuna-enable_color' => '0', 'vicuna-color-themes-last_id' => 0,
        'vicuna-eye_catch-image');

    /**
     *
     * @var array Transition targets
     */
    var $transitions = array('vicuna_config' => array('skin' => 'vicuna-skin',
            'eye_catch' => 'vicuna-eye_catch', 'language' => 'vicuna-language',
            'feed_type' => 'vicuna-feed-type', 'feed_url' => 'vicuna-feed-uri',
            'g_navi' => 'vicuna-g_navi-display', 'g_navi_home' => 'vicuna-g_navi-home',
            'description_display' => 'vicuna-description'),
        'vicuna_layout' => array('index_layout' => 'vicuna-layout-index',
            'category_layout' => 'vicuna-layout-category', 'archive_layout' => 'vicuna-layout-archive',
            'tag_layout' => 'vicuna-layout-tag', 'page_layout' => 'vicuna-layout-page',
            'single_layout' => 'vicuna-layout-single', 'search_layout' => 'vicuna-layout-search',
            '404_layout' => 'vicuna-layout-404'));

    var $skin_headers = array('Title' => 'Title', 'Author' => 'Author', 'Modified' => 'Modified by',
        'Last_modify' => 'Last modify', 'Licence' => 'Licence', 'Layout' => 'Layout',
        'Eye_catch' => 'Eye catch', 'Fixed_width' => 'Fixed width', 'Special' => 'Special');

    /**
     * Set default options
     *
     * @return void
     */
    function set_default()
    {
        if (!empty($this -> enable_skins)) return null;
        $this -> enable_languages = array('default' => __('Default', 'vicuna'), 'ja' => __('Japanese', 'vicuna'),
            'zh_TW' => __('Taiwanese', 'vicuna'), 'zh_CN' => __('Chinese', 'vicuna'),
            'en_US' => __('English', 'vicuna'));
        $this -> enable_titles = array('blog_name' => __('Blog name', 'vicuna'),
            'entry_title' => __('Entry title', 'vicuna'));
        $this -> enable_pagings = array('around' => __('Around', 'vicuna'), 'numbers' => __('Numbers', 'vicuna'),
            'pagenavi' => __('wp-pagenavi', 'vicuna'));
        $this -> enable_authors = array('1' => __('Display', 'vicuna'), '0' => __('Not displayed', 'vicuna'));
        $this -> enable_g_navi_display = array('1' => __('Page', 'vicuna'), '2' => __('Category', 'vicuna'), '0' => __('No display', 'vicuna'));
        $this -> enable_g_navi_home = array('1' => __('Yes', 'vicuna'), '0' => __('No', 'vicuna'));
        $this -> enable_g_navi_pos = array('gt' => __('Top', 'vicuna'), '0' => __('Normal', 'vicuna'));
        $this -> enable_description = array('1' => __('Yes', 'vicuna'), '0' => __('No', 'vicuna'));
        $this -> enable_nocenter = array('al' => __('Not', 'vicuna'), '0' => __('Do', 'vicuna'));
        $this -> enable_enable_color = array('1' => __('Enable', 'vicuna'), '0' => __('Disable', 'vicuna'));
        $this -> enable_templates = array('index' => __('Home', 'vicuna'),
            'category' => __('Category archive', 'vicuna'),
            'archive' => __('Archives', 'vicuna'),
            'tag' => __('Tag archive', 'vicuna'),
            'page' => __('Page', 'vicuna'), 'single' => __('Entry', 'vicuna'),
            'search' => __('Search result', 'vicuna'),
            '404' => __('Page not found', 'vicuna'));

        $this -> default_enable_eye_catches = array('default' => __('None', 'vicuna'),
            'eye-h' => __('Header area', 'vicuna'),
            'eye-hb' => __('Header area (Banner type)', 'vicuna'),
            'eye-c' => __('Contents area', 'vicuna'),
            'eye-m' => __('Main area', 'vicuna'));
        $this -> default_enable_layouts = array('single' => __('Single column', 'vicuna'),
            'double-l' => __('2 columns (Right main)', 'vicuna'),
            'double' => __('2 columns (Left main)', 'vicuna'),
            'multi' => __('3 columns (Center main)', 'vicuna'),
            'multi2-l' => __('3 columns (Right main)', 'vicuna'),
            'multi2' => __('3 columns (Left main)', 'vicuna'),
            'special1' => __('Special layout 1', 'vicuna'),
            'special2' => __('Special layout 2', 'vicuna'));
        $this -> default_enable_fixed_widths = array('default' => __('Not fixed', 'vicuna'),
            'f800' => __('Fixed 800 px', 'vicuna'), 'f850' => __('Fixed 850 px', 'vicuna'),
            'f900' => __('Fixed 900 px', 'vicuna'), 'f950' => __('Fixed 950 px', 'vicuna'),
            'f1000' => __('Fixed 1000 px', 'vicuna'));

        $this -> enable_skins = array();
        $dir = dir(dirname(dirname(__FILE__)) . DS . 'skins');
        $current_skin = $this -> get_option('vicuna-skin');
        $this -> skin_data = $this -> get_option('vicuna-skin_data');
        while (false !== ($entry = $dir -> read())) {
            if ($entry !== '.' && $entry !== '..' && is_dir($dir -> path . DS . $entry) && (($pos = strpos($entry, '.')) === false || $pos > 0)) {
                $stylesheet = $dir -> path . DS . $entry . DS . 'layouts.css';
                if (!file_exists($stylesheet)) continue;
                $color_stylesheet = $dir -> path . DS . $entry . DS . 'color.css';
                if (file_exists($color_stylesheet)) $this -> enable_color_skins[] = $entry;
                if (!isset($this -> skin_data[$entry]) || (isset($this -> skin_data[$entry]) && $this -> skin_data[$entry]['timestamp'] < filemtime($stylesheet))) {
                    $skin_data = get_file_data($stylesheet, $this -> skin_headers, 'skin');
                    $skin_data['timestamp'] = filemtime($stylesheet);
                    foreach(array('Layout', 'Eye_catch', 'Fixed_width', 'Special') as $name) {
                        if (empty($skin_data[$name])) {
                            if ($name === 'Layout') continue 2;
                            $skin_data[$name] = array();
                        } else {
                            $skin_data[$name] = preg_replace('/,[ ]*/', ',', $skin_data[$name]);
                            $skin_data[$name] = explode(',', $skin_data[$name]);
                            if ($name === 'Eye_catch' || $name === 'Fixed_width')
                                array_unshift($skin_data[$name], 'default');
                        }
                    }
                    $this -> skin_data[$entry] = $skin_data;
                }
                if ($current_skin === $entry) {
                    $this -> enable_layouts = $this -> skin_data[$entry]['Layout'];
                    $this -> enable_eye_catches = $this -> skin_data[$entry]['Eye_catch'];
                    $this -> enable_fixed_widths = $this -> skin_data[$entry]['Fixed_width'];
                }
                $this -> enable_skins[$entry] = $entry;
            }
        }
        update_option('vicuna-skin_data', $this -> skin_data);
        if (empty($this -> enable_skins)) wp_die(__('Nothing Vicuna theme skin.', 'vicuna'));
        $dir -> close();

        $this -> default_options['vicuna-color'] = unserialize('a:3:{s:7:"themeid";s:2:"m1";s:5:"title";s:11:"Sweat stain";s:3:"hex";a:5:{i:0;s:6:"f0f0f0";i:1;s:6:"e8eadd";i:2;s:6:"333333";i:3;s:6:"34bfed";i:4;s:6:"ea7323";}}');
        $this -> default_options['vicuna-color-themes'] = unserialize('a:1:{s:2:"m1";a:3:{s:7:"themeid";s:2:"m8";s:5:"title";s:11:"Sweat stain";s:3:"hex";a:5:{i:0;s:6:"f0f0f0";i:1;s:6:"e8eadd";i:2;s:6:"333333";i:3;s:6:"34bfed";i:4;s:6:"ea7323";}}}');
    }

    /**
     * Get theme preview args
     *
     * @param  $name string argument name
     * @return string argument value
     */
    function preview_arg($name)
    {
        if (!isset($_GET['template']) || !isset($_GET['preview'])) return null;
        switch ($name) {
        case 'vicuna-skin':
            return isset($_GET['cs'])? $_GET['cs']: null;
        case 'vicuna-eye_catch':
            return isset($_GET['ce'])? $_GET['ce']: null;
        case 'vicuna-g_navi-display':
            return isset($_GET['cg_d'])? $_GET['cg_d']: null;
        case 'vicuna-g_navi-home':
            return isset($_GET['cg_h'])? $_GET['cg_h']: null;
        case 'vicuna-g_navi-pos':
            return isset($_GET['cg_p'])? $_GET['cg_p']: null;
        case 'vicuna-description':
            return isset($_GET['cd'])? $_GET['cd']: null;
        case 'vicuna-nocenter':
            return isset($_GET['cn'])? $_GET['cn']: null;
        case 'vicuna-fixed_width':
            return isset($_GET['cf'])? $_GET['cf']: null;
        case 'vicuna-layout-index':
            return isset($_GET['cl_i'])? $_GET['cl_i']: null;
        case 'vicuna-layout-archive':
            return isset($_GET['cl_a'])? $_GET['cl_a']: null;
        case 'vicuna-layout-category':
            return isset($_GET['cl_c'])? $_GET['cl_c']: null;
        case 'vicuna-layout-tag':
            return isset($_GET['cl_t'])? $_GET['cl_t']: null;
        case 'vicuna-layout-page':
            return isset($_GET['cl_p'])? $_GET['cl_p']: null;
        case 'vicuna-layout-single':
            return isset($_GET['cl_e'])? $_GET['cl_e']: null;
        case 'vicuna-layout-search':
            return isset($_GET['cl_s'])? $_GET['cl_s']: null;
        case 'vicuna-layout-404':
            return isset($_GET['cl_n'])? $_GET['cl_n']: null;
        case 'template':
            return isset($_GET['ct'])? $_GET['ct']: null;
        case 'vicuna-enable_color':
            return isset($_GET['cc_e'])? $_GET['cc_e']: null;
        case 'vicuna-color':
            if (isset($_GET['cc_t']) && !empty($_GET['cc_t'])) {
                $theme = array();
                foreach(explode(',', $_GET['cc_t']) as $c) {
                    $c = explode('*', $c);
                    if ($c[0] === 'hex') $c[1] = explode('|', $c[1]);
                    $theme[$c[0]] = $c[1];
                }
                return $theme;
            } else {
                return array();
            }
        }
    }

    /**
     * Enqueue stylesheets of the skin
     *
     * @return void
     */
    function skin_styles()
    {
        if (is_admin()) return null;
        $config = &Vicuna :: config();
        $skin = $config -> get_option('vicuna-skin');
        $skin_styles = get_option('vicuna-skin_styles', array());
        if (empty($skin_styles)) {
            $dir = dir(dirname(dirname(__FILE__)) . DS . 'skins' . DS . $skin);
            while (false !== ($entry = $dir -> read())) {
                if ($entry === '.' || $entry === '..' || strpos($entry, '.css') === false) continue;
                $skin_styles[] = $entry;
            }
            $dir -> close();
            if (empty($preview_skin))
                update_option('vicuna-skin_styles', $skin_styles);
        }
        foreach($skin_styles as $style) {
            wp_enqueue_style("vicuna-{$style}", sprintf('%s/skins/%s/%s',
                    get_bloginfo('template_url'), $skin, $style));
        }
    }

    /**
     * Get Option
     *
     * @param  $name string Option name
     * @return mixed Option value
     */
    function get_option($name)
    {
        if (!isset($this -> default_options[$name])) return null;
        $option = get_option($name, $this -> default_options[$name]);
        if (is_null($preview_option = $this -> preview_arg($name)) === false) $option = $preview_option;
        return $option;
    }

    /**
     * Return the current page template
     *
     * @return mixed Template type
     */
    function template_type()
    {
        if (is_404()) $template = '404';
        else if (is_search()) $template = 'search';
        else if (is_home()) $template = 'index';
        else if (is_attachment()) $template = 'single';
        else if (is_single()) $template = 'single';
        else if (is_page()) $template = 'page';
        else if (is_category()) $template = 'category';
        else if (is_tag()) $template = 'tag';
        else if (is_author()) $template = 'single';
        else if (is_date()) $template = 'archive';
        else if (is_archive()) $template = 'archive';
        else if (is_paged()) $template = 'page';
        else $template = false;
        return $template;
    }

    /**
     * Action hooked for wp
     *
     * @return void
     */
    function action_wp()
    {
        $this -> skin_styles();
    }

    /**
     * Action hooked for admin_menu
     *
     * @return void
     */
    function action_admin_menu()
    {
        require_once('config.php');
        add_menu_page(__('Vicuna Theme Manager', 'vicuna'), 'Vicuna', 'switch_themes', 'config.php', 'vicuna_config');
        add_submenu_page('config.php', __('Vicuna Theme manager', 'vicuna'), __('Configure'), 'switch_themes', 'config.php', 'vicuna_config');
    }

    /**
     * Action hooked for admin_init
     *
     * @return void
     */
    function action_admin_init()
    {
        $this -> set_default();
        if (is_admin() && isset($_GET['page'])) {
            if ($_GET['page'] === 'config.php') {
                wp_enqueue_script('jquery-iframe', get_bloginfo('template_url') . '/js/jquery.iframe.js', array('jquery'), null, true);
                wp_enqueue_script('jquery-colorpicker', get_bloginfo('template_url') . '/js/colorpicker.js', array('jquery'), null, true);
                wp_enqueue_script('vicuna-layoutManager', get_bloginfo('template_url') . '/js/theme_manager.js', array('jquery-iframe', 'jquery-colorpicker'), null, true);
                wp_enqueue_script('vicuna-transition', get_bloginfo('template_url') . '/js/transition.js', array(), null, true);
            }
            wp_enqueue_style('vicuna-admin', get_bloginfo('template_url') . '/admin.css');
        }
    }

    /**
     * Constructor
     *
     * @return void
     */
    function VicunaConfig()
    {
        add_action('wp', array($this, 'action_wp'));
        add_action('admin_menu', array($this, 'action_admin_menu'));
        add_action('admin_init', array($this, 'action_admin_init'));
    }
}

/**
 * For AJAX hooks
 *
 * @package wp.Vicuna
 * @version 0.1.0
 * @author HAYASHI Ryo<ryo@spais.co.jp>
 */
class VicunaAjax {
    /**
     * Generate preview URI
     *
     * @return void
     */
    function action_sample_url()
    {
        $ct = current_theme_info();
        $preview_link = esc_url(get_option('home') . '/');
        if (is_ssl()) $preview_link = str_replace('http://', 'https://', $preview_link);
        $preview_link = add_query_arg(array('preview' => 0, 'template' => $ct -> template,
                'stylesheet' => $ct -> stylesheet, 'TB_iframe' => 'true', 'cs' => $_POST['cs'], 'ce' => $_POST['ce'],
                'cg_d' => $_POST['cg_d'], 'cg_p' => $_POST['cg_p'], 'cg_h' => $_POST['cg_h'], 'cd' => $_POST['cd'],
                'cn' => $_POST['cn'], 'cf' => $_POST['cf'], 'ct' => $_POST['ct'], 'cl_i' => $_POST['cl_i'],
                'cl_e' => $_POST['cl_e'], 'cl_p' => $_POST['cl_p'], 'cl_a' => $_POST['cl_a'],
                'cl_c' => $_POST['cl_c'], 'cl_t' => $_POST['cl_t'], 'cl_n' => $_POST['cl_n'],
                'cl_s' => $_POST['cl_s'], 'cc_e' => $_POST['cc_e'], 'cc_t' => $_POST['cc_t']), $preview_link);
        die($preview_link);
    }

    /**
     * wp.Vicuna.ext take over the settings
     *
     * @return void
     */
    function action_transition()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'vicuna_config-options')) die(__('Page has expired. Please refresh the page.', 'vicuna'));
        $config = &Vicuna :: config();
        $transitions = array();
        $vicuna_config = get_option('vicuna_config');
        foreach($config -> transitions['vicuna_config'] as $old => $new) {
            update_option($new, $vicuna_config[$old]);
        }
        $vicuna_layout = get_option('vicuna_layout');
        foreach($config -> transitions['vicuna_layout'] as $old => $new) {
            update_option($new, $vicuna_config[$old]);
        }
        if ($vicuna_config['title'] === '1') {
            update_option('vicuna-title-front', 'entry_title');
            update_option('vicuna-title-rear', 'blog_name');
            update_option('vicuna-title-separator', '-');
        } else {
            update_option('vicuna-title-front', 'blog_name');
            update_option('vicuna-title-rear', 'entry_title');
            update_option('vicuna-title-separator', '-');
        }
        die('ok');
    }

    function action_color()
    {
        $config = &Vicuna :: config();
        $api_url = parse_url($config -> color_api);
        $param = 'd=' . urlencode(getenv('HTTP_HOST'));
        if (isset($_POST['a']) && in_array($_POST['a'], $config -> enable_color_api_actions))
            $param .= '&a=' . urlencode($_POST['a']);
        if (isset($_POST['p']) && ctype_digit($_POST['p']))
            $param .= '&p=' . urlencode($_POST['p']);
        if (isset($_POST['q']))
            $param .= '&q=' . urlencode($_POST['q']);
        $referer = sprintf('%s://%s%s', (getenv('HTTPS')? 'https': 'http'), getenv('HTTP_HOST'), getenv('REQUEST_URI'));

        $request = "POST {$api_url['path']} HTTP/1.1\r\n"
         . "Host: {$api_url['host']}\r\n"
         . "Content-Type: application/x-www-form-urlencoded\r\n"
         . "Content-Length: " . strlen($param) . "\r\n"
         . "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n"
         . "Referer: {$referer}\r\n"
         . "Connection: Close\r\n"
         . "\r\n"
         . $param . "\r\n";

        $sock = fsockopen($api_url['host'], 80);
        if (!$sock)
            die(json_encode(array('success' => false)));

        $res = $header = null;
        fputs($sock, $request);
        while (!feof($sock)) {
            $res .= fgets($sock);
        }
        fclose($sock);
        $res = explode("\r\n\r\n", $res);
        die(preg_replace('/(^[0-9a-z\r\n]+|[0-9a-z\r\n]+$)/m', null, $res[1]));
    }

    function action_delete_color()
    {
        if (!isset($_POST['themeid']) || !preg_match('/^[mt]?[0-9]+$/i', $_POST['themeid']))
            return null;
        $config = &Vicuna :: config();
        $themes = $config -> get_option('vicuna-color-themes');
        if (isset($themes[$_POST['themeid']])) unset($themes[$_POST['themeid']]);
        update_option('vicuna-color-themes', $themes);
        die('1');
    }

    /**
     * Constructor
     *
     * @return void
     */
    function VicunaAjax()
    {
        add_action('wp_ajax_vicuna_transition', array($this, 'action_transition'));
        add_action('wp_ajax_vicuna_sample_url', array($this, 'action_sample_url'));
        add_action('wp_ajax_vicuna_color', array($this, 'action_color'));
        add_action('wp_ajax_vicuna_delete_color', array($this, 'action_delete_color'));
    }
}

/**
 * Vicuna Controller
 *
 * @package wp.Vicuna
 * @version 0.1.0
 * @author HAYASHI Ryo <ryo@spais.co.jp>
 */
class Vicuna {
    /**
     * Return to wp.Vicuna theme Version
     *
     * @param  $echo boolean OPTIONAL If true is echo
     * @return VICUNA_VERSION string
     */
    function get_version($echo = true)
    {
        if ($echo === true) echo VICUNA_VERSION;
        return VICUNA_VERSION;
    }

    /**
     * Return to Vicuna web site URI
     *
     * @return unknown_type
     */
    function uri()
    {
        $locale = get_locale();
        if ($locale === 'ja') {
            echo 'http://wp.vicuna.jp/';
        } else if ($locale === 'zh_TW' || $locale === 'zh_CN') {
            echo 'http://cn.wp.vicugna.org/';
        } else {
            echo 'http://en.wp.vicugna.org/';
        }
    }

    /**
     * Return random category
     *
     * @return integer Category id
     */
    function rand_category()
    {
        global $wpdb;
        $rows = $wpdb -> get_results("SELECT `term_id` FROM `{$wpdb->term_taxonomy}`
        	WHERE `taxonomy` = 'category' AND `count` > 0 ORDER BY RAND() LIMIT 1");
        return $rows[0] -> term_id;
    }

    /**
     * Return random tag
     *
     * @return string Tag slug
     */
    function rand_tag()
    {
        global $wpdb;
        $rows = $wpdb -> get_results("SELECT `slug` FROM `{$wpdb->term_taxonomy}` LEFT JOIN
        	`{$wpdb->terms}` ON `{$wpdb->term_taxonomy}`.`term_id` = `{$wpdb->terms}`.
        	`term_id` WHERE `taxonomy` = 'post_tag' AND `count` > 0 ORDER BY RAND() LIMIT 1");
        return $rows[0] -> slug;
    }

    /**
     * Return random archive
     *
     * @return string Archive date
     */
    function rand_archive()
    {
        global $wpdb;
        $rows = $wpdb -> get_results("SELECT DATE_FORMAT(`post_date`, '%Y-%m') `date` FROM
	        `{$wpdb->posts}` WHERE `post_status` = 'publish' AND `post_type` = 'post'
	        ORDER BY RAND() LIMIT 1");
        return $rows[0] -> date;
    }

    /**
     * Return random post
     *
     * @return integer Post id
     */
    function rand_post()
    {
        global $wpdb;
        $rows = $wpdb -> get_results("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_status`
        	= 'publish' AND `post_type` = 'post' ORDER BY RAND() LIMIT 1");
        return $rows[0] -> ID;
    }

    /**
     * return random page
     *
     * @return integer Page id
     */
    function rand_page()
    {
        global $wpdb;
        $rows = $wpdb -> get_results("SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_status`
        	= 'publish' AND `post_type` = 'page' ORDER BY RAND() LIMIT 1");
        return $rows[0] -> ID;
    }
    // Actions and Filters {{{
    /**
     * Action hooked for delete_post
     *
     * @param  $postid integer Post ID
     * @return void
     */
    function action_delete_post($postid)
    {
        global $wpdb;
        $date = $wpdb -> get_var($wpdb -> prepare("SELECT DATE_FORMAT(`post_date`, '%Y-%m')
        	AS `date` FROM `{$wpdb->posts}` WHERE `ID` = %d", $postid));
        if (!empty($date)) {
            Vicuna :: cache_del($date, 'calendar');
        }
        Vicuna :: cache_del(null, 'archives_link');
        Vicuna :: cache_del(null, 'tag_cloud');
    }

    /**
     * Action hooked save_post
     *
     * @param  $postid integer Post ID
     * @return void
     */
    function action_save_post($postid)
    {
        global $wpdb;
        $date = $wpdb -> get_var($wpdb -> prepare("SELECT DATE_FORMAT(`post_date`, '%%Y-%%m')
        	AS `date` FROM `{$wpdb->posts}` WHERE `ID` = %d", $postid));
        if (!empty($date)) {
            Vicuna :: cache_del($date, 'calendar');
        }
        Vicuna :: cache_del(null, 'archives_link');
        Vicuna :: cache_del(null, 'tag_cloud');
    }

    /**
     * Action hooked wp_set_comment_status
     *
     * @return void
     */
    function action_wp_set_comment_status()
    {
        Vicuna :: cache_del(null, 'recent_comments_link');
    }

    /**
     * Filter hooked for locale
     *
     * @param  $locale mixed Locale
     * @return $locale string
     */
    function filter_locale($locale)
    {
        $Config = &Vicuna :: Config();
        $vicuna_language = $Config -> get_option('vicuna-language');
        if (!empty($vicuna_language) && $vicuna_language !== 'default') $locale = $vicuna_language;
        return $locale;
    }

    /**
     * Filter hooked for the_posts
     *
     * If there is no article to display 404 error template.
     *
     * @param  $posts array Post data
     * @return $posts array
     */
    function filter_the_posts($posts)
    {
        global $wp_query;
        if (empty($posts)) $wp_query -> is_404 = true;
        return $posts;
    }

    /**
     * Filter hooked for query_string
     *
     * @param  $query string Query string
     * @return string Query string
     */
    function filter_query_string($query)
    {
        $config = &Vicuna :: config();
        if ($template = $config -> preview_arg('template')) {
            switch ($template) {
            case 'category':
                $query = 'cat=' . Vicuna :: rand_category();
                break;
            case 'archive':
                $query = 'm=' . Vicuna :: rand_archive();
                break;
            case 'tag':
                $query = 'tag=' . Vicuna :: rand_tag();
                break;
            case 'page':
                $query = 'page_id=' . Vicuna :: rand_page();
                break;
            case 'single':
                $query = 'p=' . Vicuna :: rand_post();
                break;
            case 'search':
                $query = 's=a';
                break;
            case '404':
                $query = 'page_id=-1';
                break;
            }
        }
        return $query;
    }
    // }}} Actions and Filters
    /**
     * Get cache data
     *
     * @param  $key string Cache key
     * @param  $group string OPTIONAL Cache group
     * @return mixed Cache data
     */
    function cache_get($key, $group = 'default')
    {
        if (VICUNA_CACHE === false) return false;
        if (!$cache = get_option("vicuna-cache-{$group}")) return false;
        return isset($cache[$key])? $cache[$key]: false;
    }

    /**
     * Add cache data
     *
     * @param  $key string Cache key
     * @param  $data mixed Cache data
     * @param  $group string OPTIONAL Cache group
     * @return void
     */
    function cache_add($key, $data, $group = 'default')
    {
        if (VICUNA_CACHE === false) return null;
        $cache = get_option("vicuna-cache-{$group}", array());
        $cache[$key] = $data;
        update_option("vicuna-cache-{$group}", $cache);
    }

    /**
     * Delete cache data
     *
     * @param  $key string OPTIONAL Cache key
     * @param  $group string OPTIONAL Cache group
     * @return void
     */
    function cache_del($key = null, $group = 'default')
    {
        if (VICUNA_CACHE === false) return null;
        if (empty($key)) {
            update_option("vicuna-cache-{$group}", null);
        }
        $cache = get_option("vicuna-cache-{$group}", array());
        $cache[$key] = false;
        update_option("vicuna-cache-{$group}", $cache);
    }

    /**
     * Get instance of VicunaConfig class
     *
     * @return VicunaConfig object
     */
    function &config()
    {
        return Vicuna :: i('Config');
    }

    /**
     * Get Instance of VicunaTheme class
     *
     * @return VicunaTheme object
     */
    function &theme()
    {
        return Vicuna :: i('Theme');
    }

    /**
     * Get instance of VicunaWidget class
     *
     * @return VicunaWidget object
     */
    function &widget()
    {
        return Vicuna :: i('Widget');
    }

    /**
     * Get instance of VicunaAjax class
     *
     * @return VicunaAjax object
     */
    function &ajax()
    {
        if (is_admin()) return Vicuna :: i('Ajax');
        else return false;
    }

    /**
     * [SINGLETON] Create and Get Instance method
     *
     * @param  $instance_name mixed OPTIONAL Instance name to be acquired or, IF then NULL is create instances.
     * @return $instance mixed
     */
    function &i($instance_name = null)
    {
        static $instances = array();
        if (empty($instances)) {
            $instances = array('Config' => new VicunaConfig,
                'Theme' => new VicunaTheme,
                'Widget' => new VicunaWidget
                );
            if (is_admin()) $instance['Ajax'] = new VicunaAjax;
        }
        if (empty($instance_name) || !isset($instances[$instance_name])) {
            $instance = null;
            return $instance;
        }
        return $instances[$instance_name];
    }

    /**
     * Initial method
     *
     * @return void
     */
    function init()
    {
        if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
        if (array_key_exists('preview', $_GET) && array_key_exists('template', $_GET) && strtolower($_GET['template']) === 'wp.vicuna')
            define('VICUNA_IS_PREVIEW', true);
        else
            define('VICUNA_IS_PREVIEW', false);
        define('VICUNA_VERSION', '2.0.3');
        define('VICUNA_CACHE', true);
        $lang = get_option('vicuna-language');
        if (!empty($lang) && $lang === 'default') $lang = get_locale();
        load_textdomain('vicuna', dirname(dirname(__FILE__)) . DS . 'languages' . DS . $lang . '.mo');
        Vicuna :: i();
        add_action('delete_post', array('Vicuna', 'action_delete_post'));
        add_action('save_post', array('Vicuna', 'action_save_post'));
        add_action('wp_set_comment_status', array('Vicuna', 'action_wp_set_comment_status'));
        add_filter('locale', array('Vicuna', 'filter_locale'));
        add_filter('the_posts', array('Vicuna', 'filter_the_posts'));
        add_filter('query_string', array('Vicuna', 'filter_query_string'));
    }
}

Vicuna :: init();

if (!function_exists('h')) {
    /**
     * htmlspecialchars wrapper
     *
     * @param  $value string Escape string
     * @param  $echo boolean OPTIONAL TRUE is echo
     * @return Escaped string
     */
    function h($value, $echo = false)
    {
        $value = htmlspecialchars($value, ENT_QUOTES, get_bloginfo('charset'));
        if ($echo === true) echo $value;
        return $value;
    }
}
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
?>
