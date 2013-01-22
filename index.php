<?php

/*
Plugin Name: Recent Posts Widget Extended
Plugin URI: http://github.com/kasparsj/recent-posts-widget-extended
Description: Extends the default Recent posts widget with more otions: show excerpt when available, show thumbnail, choose post type and show archive link.
Version: 1.0
Author: Kaspars Jaudzems
Author URI: http://kasparsj.wordpress.com
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function register_recent_posts_widget() {
    require_once("recent_posts_widget.class.php");
    
    unregister_widget("WP_Widget_Recent_Posts");
    return register_widget("Recent_Posts_Widget");
}

add_action('widgets_init', 'register_recent_posts_widget');

?>
