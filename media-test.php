<?php
/**
 * Created by PhpStorm.
 * User: Lönja
 * Date: 09/10/2014
 * Time: 21:19
 */
/**
 * Copies a file from the a subdirectory of the root of the WordPress installation
 * into the uploads directory, attaches it to the given post ID, and adds it to
 * the Media Library.
 *
 * @param int $post_id The ID of the post to which the image is attached.
 * @param string $filename The name of the file to copy and to add to the Media Library
 */
function acme_add_file_to_media_uploader($post_id, $filename)
{

// Locate the file in a subdirectory of the root of the installation
    $file = trailingslashit(ABSPATH) . 'my-files/' . $filename;

// If the file doesn't exist, then write to the error log and duck out
    if (!file_exists($file) || 0 === strlen(trim($filename))) {

        error_log('The file you are attempting to upload, ' . $file . ', does not exist.');
        return;

    }

    /* Read the contents of the upload directory. We need the
    * path to copy the file and the URL for uploading the file.
    */
    $uploads = wp_upload_dir();
    $uploads_dir = $uploads['path'];
    $uploads_url = $uploads['url'];

// Copy the file from the root directory to the uploads directory
    copy($file, trailingslashit($uploads_dir) . $filename);

    /* Get the URL to the file and grab the file and load
    * it into WordPress (and the Media Library)
    */
    $url = trailingslashit($uploads_url) . $filename;
    $result = media_sideload_image($url, $post_id, $filename);

// If there's an error, then we'll write it to the error log.
    if (is_wp_error($$result)) {
        error_log(print_r($result, true));
    }

}