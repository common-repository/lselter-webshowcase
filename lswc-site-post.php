<?php

// Create Custom Post Type
//done: test nonce check on all post types.
function register_sites_posttype()
{
    $labels = array(
        'name' => _x('Sites', 'post type general name'),
        'singular_name' => _x('Site', 'post type singular name'),
        'add_new' => __('Add New Site'),
        'add_new_item' => __('Add New Site'),
        'edit_item' => __('Edit Site'),
        'new_item' => __('New Site'),
        'view_item' => __('View Site'),
        'search_items' => __('Search Sites'),
        'not_found' => __('Site'),
        'not_found_in_trash' => __('Site'),
        'parent_item_colon' => __('Site'),
        'menu_name' => __('Sites')
    );

    $taxonomies = array('slug' =>'site');

    $supports = array('title', 'thumbnail', 'editor');
    $basepath = plugins_url() . "/lselter-webshowcase/";
    $post_type_args = array(
        'labels' => $labels,
        'singular_label' => __('Site'),
        'public' => true,
        'show_ui' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => 'sites', 'with_front' => false),
        'supports' => $supports,
        'menu_position' => 27, // Where it is in the menu. Change to 6 and it's below posts. 11 and it's below media, etc.
        'menu_icon' => ($basepath . 'images/site.png'),
        'taxonomies' => $taxonomies
    );
    register_post_type('sites', $post_type_args);
}

add_action('init', 'register_sites_posttype');
// Meta Box for Site URL

$sitelink_2_metabox = array(
    'id' => 'siteinfo',
    'title' => 'Site Info',
    'page' => array('sites'),
    'context' => 'normal',
    'priority' => 'default',
    'fields' => array(


        array(
            'name' => 'Site URL',
            'desc' => 'The address of the site (without "http://")',
            'id' => 'lswc_siteurl',
            'class' => 'lswc_siteurl',
            'type' => 'text',
            'rich_editor' => 0,
            'max' => 0
        ),
//        array(
//            'name' => 'Site Description',
//            'desc' => 'A short description of this site',
//            'id' => 'lswc_sitedesc',
//            'class' => 'lswc_sitedesc',
//            'type' => 'textarea',
//            'rich_editor' => 1,
//            'max' => 0
//        ),
        array(
            'name' => 'Override Site Image',
            'desc' => 'To override the automatically generated image with your own use "Set Featured image" function.',
            'id' => 'lswc_siteoverride',
            'class' => 'lswc_siteoverride',
            'type' => 'checkbox',

        ),
        array(
            'name' => 'Refresh Thumbnails',
            'desc' => 'Check to force update of this sites thumbnails',
            'id' => 'lswc_update',
            'class' => 'lswc_update',
            'type' => 'checkbox',

        ),
    )
);

add_action('admin_menu', 'lswc_add_sitelink_2_meta_box');
function lswc_add_sitelink_2_meta_box()
{

    global $sitelink_2_metabox;

    foreach ($sitelink_2_metabox['page'] as $page) {
        add_meta_box($sitelink_2_metabox['id'], $sitelink_2_metabox['title'], 'lswc_show_sitelink_2_box', $page, 'normal', 'default', $sitelink_2_metabox);
    }
}

// function to show meta boxes
function lswc_show_sitelink_2_box()
{
    global $post;
    global $sitelink_2_metabox;
    global $lswc_prefix;
    global $wp_version;

    // Use nonce for verification
    echo '<input type="hidden" name="lswc_sitelink_2_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    //var_dump(wp_create_nonce(basename(__FILE__)));

    echo '<table class="form-table">';

    foreach ($sitelink_2_metabox['fields'] as $field) {
        // get current post meta data

        $meta = get_post_meta($post->ID, $field['id'], true);


        echo '<tr>',
        '<th style="width:20%"><label for="', $field['id'], '">', stripslashes($field['name']), '</label></th>',
            '<td class="lswc_field_type_' . str_replace(' ', '_', $field['type']) . '">';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" placeholder="Site URL" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" size="30" style="width:97%" /><br/>', '', stripslashes($field['desc']);
                break;
            // checkbox
            case 'checkbox':

                echo("<input type='hidden' value='false' name='" . $field['id'] . "'>");
                echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ';
                if ($meta == 'true') {
                    echo('checked="checked"');
                };

                echo('/>
        <label for="' . $field['id'] . '">' . $field['desc'] . '</label>');
                break;
            // textarea
            case 'textarea':
                // echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4">' . $meta . '</textarea>
                //<br /><span cla                ss="description">' . $field['desc'] . '</span>';

                wp_editor($meta, $field['id']);
                break;
            // checkbox
        }
        echo '<td>',
        '</tr>';
    }

    echo '</table>';
}


// Save data from meta box
add_action('save_post', 'lswc_sitelink_2_save');

function lswc_sitelink_2_save($post_id)
{
    if (isset($_POST['lswc_siteurl'])) {
        global $post;
        global $sitelink_2_metabox;

        // verify nonce

        if (!wp_verify_nonce($_POST['lswc_sitelink_2_meta_box_nonce'], basename(__FILE__))) {
            return $post_id;
        }


        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        foreach ($sitelink_2_metabox['fields'] as $field) {

            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];

            if ($new && $new != $old) {
                if ($field['type'] == 'date') {
                    $new = format_date($new);
                    update_post_meta($post_id, $field['id'], $new);
                }
                else {
                    if (is_string($new)) {
                        $new = $new;
                    }
                if ($field['id']== 'lswc_update')
                    //lselter_webshowcase_cache_thumbnails($post_id);
                    $new= '';

                    update_post_meta($post_id, $field['id'], $new);
                    // Checks for input and saves
                    if (($_POST['lswc_siteoverride'] == 'on') || $_POST['lswc_siteoverride'] == 'true') {
                        update_post_meta($post_id, 'lswc_siteoverride', 'true');
                    } else {
                        update_post_meta($post_id, 'lswc_siteoverride', 'false');
                    }

                }
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }

        }
    };



}

// add icon to custom post type in admin menu.
function add_menu_icons_styles()
{
    ?>

    <style>
        #adminmenu .menu-icon-sites div.wp-menu-image:before {
            content: '\f116';
        }
    </style>

<?php
}

add_action('admin_head', 'add_menu_icons_styles');
add_action( 'save_post', 'lswc_on_update_site' );
/**
 * Save post metadata when a post is saved.
 *
 * @param int $post_id The ID of the post.
 */
function lswc_on_update_site( $post_id ) {

    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */
    $slug = 'sites';

    // If this isn't a 'book' post, don't update it.
    if ( $slug != $_POST['post_type'] ) {
        return;
    }

    // - Update the post's metadata.
    //var_dump( $_REQUEST['lswc_update']);
    //var_dump($_POST);
    if ( isset( $_REQUEST['lswc_update']) &&  $_REQUEST['lswc_update'] == 'on'  ) {
        //var_dump( $_REQUEST['lswc_update']);
        lselter_webshowcase_cache_thumbnails($post_id);
        update_post_meta( $post_id, 'lswc_update', '' );
    }



    // Checkboxes are present if checked, absent if not.
}
//todo: add metadata url to the body with basic texton publish but not on update
