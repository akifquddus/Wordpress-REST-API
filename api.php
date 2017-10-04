<?php
/*
Plugin Name: WP REST API
Plugin URI:  http://akifquddus.com
Description: WP REST API Plugin
Version:     1.1
Author:      Akif Quddus
Author URI:  http://akifquddus.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wprestapi
Domain Path: /languages
*/


if (($_POST['wprequest'])) {
    include '../../../wp-includes/wp-db.php';
    include '../../../wp-config.php';
    include '../../../wp-load.php';

    if ($_POST['type'] == 'verify') {
        $user = htmlspecialchars($_POST['user'],ENT_QUOTES);
        $pass = $_POST['pass'];
        if (verify($user, $pass)) {
            echo json_encode(array(
                'status' => true,
                'message' => "Account Successfully Verified",
            ));
        } else {
            echo json_encode(array(
                'status' => false,
                'message' => "Authentication Error Occurred",
            ));
        }
    } else if ($_POST['type'] == 'createpost') {
        $user = htmlspecialchars($_POST['user'],ENT_QUOTES);
        $pass = $_POST['pass'];
        $userinfo = (wp_authenticate($user, $pass));

        if (isset($userinfo->id)) {
            $post = array(
                'post_title' => $_POST['post_title'],
                'post_content' => $_POST['post_content'],
                'post_status' => $_POST['post_status'],
                'post_category' => unserialize($_POST['post_category']),
            );


            $post = (wp_insert_post($post, true));
            if ($_POST['image'] != '#') {
                Generate_Featured_Image($_POST['image'], $post);
            }

            echo json_encode(array('status' => true, 'post' => get_post($post)));

        } else {
            echo json_encode(array('status' => false, 'message' => 'Authentication Error Occurred'));
        }
    }

}

function verify($User, $Pass) {
    $user = $User;
    $pass = $Pass;
    $userinfo = (wp_authenticate($user, $pass));
    if (is_wp_error($userinfo)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Takes Image URL and Post ID
 * Upload Image to WP Directory and Database
 * Add Image as a Featured to the Given Post ID
 */
function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file($image_url);
    $filename = basename($image_url);

    if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}
