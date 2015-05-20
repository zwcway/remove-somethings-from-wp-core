<?php
/*
Plugin Name: 去掉无用的东西
Description: 从 WP 内核中去除无用的东西，以提高运行速度。
Author: zwcway
Version: 1.0
*/
defined('ABSPATH') or exit;


/******************************************************************************
 * 去掉 logo
 * @param WP_Admin_Bar $wp_admin_bar
 */
function zwcway_remove_wp_logo_from_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', 'zwcway_remove_wp_logo_from_admin_bar', 25 );

/******************************************************************************
 * 去掉加载谷歌字体
 */
function zwcway_remove_open_sans_from_wp_core() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');
}
add_action( 'init', 'zwcway_remove_open_sans_from_wp_core' );

/******************************************************************************
 * 更改 jQuery CDN 为新浪的
 */
function zwcway_modify_jquery() {
    if (!is_admin()) {
//        wp_deregister_script('jquery-core');
//        wp_register_script('jquery-core', 'http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js', false, '1.9.1');
//        wp_enqueue_script('jquery-core');
    }
}
add_action('init', 'zwcway_modify_jquery');


/******************************************************************************
 * 去掉加载谷歌字体
 * @param String $translations
 * @param String $text
 * @param String $context
 * @param String $domain
 * @return String
 */
function zwcway_disable_open_sans( $translations, $text, $context, $domain ) {
    if (
        ( 'Open Sans font: on or off' == $context && 'on' == $text)
        /*for twentyfourteen*/
        ||( 'Lato font: on or off' == $context && 'on' == $text)
        /*for twentyfifteen*/
        ||( 'Noto Sans font: on or off' == $context && 'on' == $text)
        ||( 'Noto Serif font: on or off' == $context && 'on' == $text)
        ||( 'Inconsolata font: on or off' == $context && 'on' == $text)
    ) {
        $translations = 'off';
    }
    return $translations;
}
add_filter( 'gettext_with_context', 'zwcway_disable_open_sans', 888, 4 );


/******************************************************************************
 * 去掉资源路径的版本信息
 */
function zwcway_remove_assest_version( $src ){
    return remove_query_arg( 'ver', $src );
}
add_filter( 'script_loader_src', 'zwcway_remove_assest_version' );
add_filter( 'style_loader_src', 'zwcway_remove_assest_version' );

/******************************************************************************
 * 前台顶部清理
 */
function zwcway_header_clean_up(){
    if (!is_admin()) {
        foreach(array('wp_generator','rsd_link','index_rel_link','start_post_rel_link','wlwmanifest_link') as $clean){remove_action('wp_head',$clean);}
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
        remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
        foreach(array('single_post_title','bloginfo','wp_title','category_description','list_cats','comment_author','comment_text','the_title','the_content','the_excerpt') as $where){
            remove_filter ($where, 'wptexturize');
        }
        /*remove_filter( 'the_content', 'wpautop' );
        remove_filter( 'the_excerpt', 'wpautop' );*/
        wp_deregister_script( 'l10n' );
    }
}
add_action('get_header', 'zwcway_header_clean_up', 11);

/******************************************************************************
 * 移除某些WP自带的小工具
 */
function zwcway_remove_meta_widget() {
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    //unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Links');
    unregister_widget('WP_Widget_Meta');
    // unregister_widget('WP_Widget_Search');
    // unregister_widget('WP_Widget_Text');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Nav_Menu_Widget');
    /* 注册自定义工具 */
//    register_widget('WP_Widget_Meta_Mod');
}
add_action( 'widgets_init', 'zwcway_remove_meta_widget', 11);

/******************************************************************************
 * 移除WP为仪表盘(dashboard)页面加载的小工具
 */
function zwcway_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}
add_action('wp_dashboard_setup', 'zwcway_remove_dashboard_widgets', 11);
?>