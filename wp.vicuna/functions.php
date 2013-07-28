<?php
/**
 * wp.Vicuna theme functions
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

require_once('src/classes.php');

/**
 *
 * @see VicunaTheme::xml_declaration()
 */
function vicuna_xml_declaration()
{
    $theme = &Vicuna :: theme();
    $theme -> xml_declaration();
}

/**
 *
 * @see VicunaTheme::page_head_title()
 */
function vicuna_page_head_title()
{
    $theme = &Vicuna :: Theme();
    $theme -> page_head_title();
}

/**
 *
 * @see VicunaTheme::page_layout()
 */
function vicuna_page_layout()
{
    $theme = &Vicuna :: theme();
    $theme -> page_layout();
}

/**
 *
 * @see VicunaTheme::page_description()
 */
function vicuna_page_description()
{
    $theme = &Vicuna :: Theme();
    $theme -> page_description();
}

/**
 *
 * @see VicunaTheme::global_navigation()
 */
function vicuna_global_navigation()
{
    $theme = &Vicuna :: Theme();
    $theme -> global_navigation();
}

/**
 *
 * @see VicunaTheme::search_result()
 */
function vicuna_search_result($arg)
{
    $theme = &Vicuna :: Theme();
    $theme -> search_result($arg);
}

/**
 *
 * @see VicunaTheme::paging()
 */
function vicuna_paging($args = null)
{
    $theme = &Vicuna :: Theme();
    $theme -> paging($args);
}

/**
 *
 * @see VicunaTheme::category_relay()
 */
function vicuna_category_relay($args = null)
{
    $theme = &Vicuna :: Theme();
    $theme -> category_relay($args);
}

/**
 *
 * @see VicunaTheme::page_relay()
 */
function vicuna_page_relay($args = null)
{
    $theme = &Vicuna :: Theme();
    $theme -> page_relay($args);
}

/**
 *
 * @see VicunaTheme::archive_link()
 */
function vicuna_archives_link()
{
    $theme = &Vicuna :: Theme();
    $theme -> archives_link();
}

/**
 *
 * @see VicunaTheme::tag_cloud()
 */
function vicuna_tag_cloud($args = null)
{
    $theme = &Vicuna :: Theme();
    return $theme -> tag_cloud($args);
}

/**
 *
 * @return string HTML Element
 */
function vicuna_require_password()
{
    global $post;
    if (empty($post -> post_password) || $_COOKIE['wp-postpass_' . COOKIEHASH] === $post -> post_password) return true;
    printf('<p class="nocomments">%s</p>', __('This post is password protected. Enter the password to view comments.', 'vicuna'));
    return false;
}

/**
 *
 * @see VicunaTheme::comments_and_trackbacks()
 */
function vicuna_comments_and_trackpings($comments)
{
    $theme = &Vicuna :: Theme();
    return $theme -> comments_and_trackpings($comments);
}

/**
 *
 * @see VicunaTheme::older_post_exists()
 */
function vicuna_older_post_exists()
{
    $theme = &Vicuna :: Theme();
    return $theme -> older_post_exists();
}

/**
 *
 * @see VicunaTheme::newer_post_exists()
 */
function vicuna_newer_post_exists()
{
    $theme = &Vicuna :: Theme();
    return $theme -> newer_post_exists();
}

/**
 * Return edit comment link
 *
 * @param  $text string OPTIONAL Link text
 * @param  $before string OPTIONAL Before link element
 * @param  $after string OPTIONAL After link element
 * @return string HTML Element
 */
function vicuna_edit_comments_link($text = null, $before = '<p class="admin">', $after = '</p>')
{
    global $post;
    if (is_attachment() || $post -> post_type === 'page' || !current_user_can('edit_post', $post -> ID)) return null;
    if (empty($text)) $text = __('Edit This Comments.', 'vicuna');
    printf('%s<a href="%s/wp-admin/edit.php?p=%d&amp;c=1">%s</a>%s', $before, get_option('siteurl'), $post -> ID, $text, $after);
}

/**
 *
 * @see VicunaTheme::archive_title()
 */
function vicuna_archive_title()
{
    $theme = &Vicuna :: Theme();
    $theme -> archive_title();
}

/**
 * Returns TRUE if login is required to comment
 *
 * @return boolean
 */
function vicuna_comment_is_needed_login()
{
    return get_option('comment_registration') && !is_user_logged_in()? true: false;
}

/**
 *
 * @see VicunaTheme::from_the_posted()
 */
function vicuna_author($args = null)
{
    $theme = &Vicuna :: Theme();
    $theme -> from_the_posted($args);
}

/**
 *
 * @see VicunaTheme::the_content_onliner()
 */
function vicuna_the_content_oneliner()
{
    $theme = &Vicuna :: Theme();
    $theme -> the_content_oneliner();
}

/**
 *
 * @see VicunaTheme::color_information()
 */
function vicuna_color_information()
{
    $theme = &Vicuna :: Theme();
    $theme -> color_information();
}

/**
 *
 * @see VicunaConfig::get_option()
 */
function vicuna_skinname()
{
    $config = &Vicuna :: Config();
    echo $config -> get_option('vicuna-skin');
}
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
?>