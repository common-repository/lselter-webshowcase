<?php
/**
 * Created by PhpStorm.
 * User: Lönja
 * Date: 09/10/2014
 * Time: 21:01
 */
//!!this file is not meant to be edited, please leave settings as they are
defined('ABSPATH') or die("No script kiddies please!");

$basepath = plugins_url() + "lselter-webshowcase";
$plugin_base_url = $basepath. '/lselter-webshowcase.php' ;

$plugin_prefix = "lswc_";
add_filter( 'default_content', 'my_editor_content', 10, 2 );

function my_editor_content( $content, $post ) {

    switch( $post->post_type ) {

        case 'sites':
            $content = '[gallery]';
            break;
        default:
            $content = $content;
            break;
    }

    return $content;
}

//display  sites with normal posts
add_action( 'pre_get_posts', 'add_my_post_types_to_query' );

function add_my_post_types_to_query( $query ) {
    if ( is_home() && $query->is_main_query() )
        $query->set( 'post_type', array( 'post', 'sites' ) );
    return $query;
}
