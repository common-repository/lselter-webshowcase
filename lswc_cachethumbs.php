<?php
/**
 * Created by PhpStorm.
 * User: Lönja
 * Date: 09/10/2014
 * Time: 21:03
 */
//DONE?: add size options
//done: add info to title no tag available
//done:only set thumbnail if override not set

defined('ABSPATH') or die("No script kiddies please!");
add_filter('cron_schedules', 'cron_add_weekly');
//add a weekly scheduler
function lswc_the_cache_loop($post_id){
    $options = get_option('lswc_options');
    $p2i_key = $options['p2i_api_key'];
    //var_dump( $options['p2i_api_key']);

    if (isset($p2i_key)) {
        $apikey = $p2i_key;
    } else {
        //done: important: development only
        $apikey = "null";
    };
    $sizes = array();
    if(isset($options['size_i4'])){
        if($options['size_i4'] == 1){
            array_push($sizes, array(0,'on an Iphone 4'));
        };
    };
    if(isset($options['size_i5'])){
        if($options['size_i5'] == 1){
            array_push($sizes, array(1, 'on an Iphone 5'));
        };
    };
    if(isset($options['size_a'])){
        if($options['size_a'] == 1){
            array_push($sizes, array(2 ,'on an Android Phone'));
        };
    };
    if(isset($options['size_w'])){
        if($options['size_w'] == 1){
            array_push($sizes, array(3 , 'on a Windows Phone'));
        };
    };
    if(isset($options['size_ip'])){
        if($options['size_ip'] == 1){
            array_push($sizes, array(4, 'on an Ipad'));
        };
    };
    if(isset($options['size_at'])){
        if($options['size_at'] == 1){
            array_push($sizes, array(5 , 'on an Android Tablet'));
        };
    };
    if(isset($options['size_d'])){
        if($options['size_d'] == 1){
            array_push($sizes, array(6 , 'on a Desktop'));
        };
    };
    //var_dump($sizes);


    $site_url = get_post_meta($post_id, 'lswc_siteurl', true);
    foreach($sizes as $size => $size_name) {

        //var_dump($size_name);
        //echo '<br>';
        //var_dump($site_url);
        $img_url = call_p2i($apikey, $site_url, $size_name[0]);
        if($img_url ==false){
            //echo 'breaking';
            break;
        }
        //var_dump($img_url);
        //$img_url = 'http://api.page2images.com/directlink?p2i_url='.$site_url.'&p2i_key='.$p2i_key;
        $url = $img_url;
        //var_dump($img_url);


        $site_override = get_post_meta($post_id, 'lswc_siteoverride', true);
        //var_dump($site_override);
        $tmp = download_url($img_url);

        //var_dump($tmp);
        $desc = the_title('','',false).' at '.date('Y-m-d H:i (e)').' '. $size_name[1] ;
        //var_dump($img_url);
        $file_array = array();
        $file_array['post_title'] = 'test';
        // Set variables for storage
        // fix file filename for query strings
        preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
        $file_array['name'] = basename($matches[0]);
        $file_array['tmp_name'] = $tmp;
        // If error storing temporarily, unlink
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);
            $file_array['tmp_name'] = '';
        }

        // do the validation and storage stuff
        $id = media_handle_sideload($file_array, $post_id, $desc);

        // If error storing permanently, unlink
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            return $id;
        }
        //echo('<br>');
        //var_dump($post_id);
        //echo('<br>');

        //echo('<br>');
        //var_dump($id);
        //echo('<br>');
        //var_dump($site_override);
        //var_dump($size);
        if($site_override !=='true' && $size == $options['default_thumb_size']) {

            $result = set_post_thumbnail($post_id, $id);
            //echo('<br>');
            //var_dump($result);
        };
    };



}

/**
 *lselter_webshowcase_cache_thumbnails:
 * Downloads screenshots to media library for all active sizes and published sites
 * Image title format: "<post title> on <time> on a/an <device>"
 * Currently only uses p2i API
 * in future may add other APIs selected through main options screen
 * only one API can be active at a time (reduces server load and disk
 * space requirements, as well as reducing clutter in media library.
 * currently run once per week regardless of options
 */
function delete_post_media( $post_id ) {

    if(!isset($post_id)) return; // Will die in case you run a function like this: delete_post_media($post_id); if you will remove this line - ALL ATTACHMENTS WHO HAS A PARENT WILL BE DELETED PERMANENTLY!
    elseif($post_id == 0) return; // Will die in case you have 0 set. there's no page id called 0 :)
    elseif(is_array($post_id)) return; // Will die in case you place there an array of pages.

    else {

        $attachments = get_posts( array(
            'post_type'      => 'attachment',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'post_parent'    => $post_id
        ) );

        foreach ( $attachments as $attachment ) {
            if ( false === wp_delete_attachment( $attachment->ID ) ) {
                // Log failure to delete attachment.
            }
        }
    }
}

/******* DANGER - WILL REMOVE ALL ATTACHMENTS FROM THIS PARENT ID (666) *******/
// delete_post_media( 666 );
function lselter_webshowcase_cache_thumbnails($post_id = 0)
{
    require('APIs/p2i.php');
    if ($post_id == 0){

    $query = new WP_Query(array(
        'post_type' => 'sites',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));


    while ($query->have_posts()) {
        $query->the_post();
        //var_dump($query -> the_post());
        $post_id = get_the_ID();
        //echo('post ID: ' . $post_id);
    delete_post_media($post_id);

    lswc_the_cache_loop($post_id);

    wp_reset_query();
}
    }
    else{
        delete_post_media($post_id);
        lswc_the_cache_loop($post_id);

    }
//done: finish core loop
//done: change default image size to desktop

}
