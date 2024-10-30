<?php
/**
 * Leafio Letter Avatar
 *
 * Plugin Name: Leafio Letter Avatar
 * Plugin URI:  
 * Description: Create user avatars using their initial letters.
 * Version:     1.0.1
 * Author:      blogvii
 * Author URI:  https://leafio.net/
 * Text Domain: leafio_letter_avatar
 * Domain Path: 
 * License: GPLv2 or later
 * Requires at least: 5.9
 * Requires PHP: 7.2
 * Tested up to: 6.6.2
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


add_filter('get_avatar', 'leafio_letters_avatar_filter', 10, 6);

function leafio_letters_avatar_filter($avatar, $id_or_email, $size, $default, $alt, $args) {
    // Determine the name for initials
    $name = '';

    // Check if it's a numeric user ID
    if (is_numeric($id_or_email)) {
        $user = get_userdata($id_or_email);
        $name = $user ? $user->display_name : ''; // Get display name for registered users
    } elseif (is_object($id_or_email)) {
        $name = $id_or_email->comment_author; // Use comment author's name
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        $name = $user ? $user->display_name : $alt; // Use display name or alt
    }

    // If no display name, use the first character of the email
    if (empty($name)) {
        $email = is_object($id_or_email) ? $id_or_email->comment_author_email : $id_or_email;
        $name = !empty($email) ? substr($email, 0, 1) : 'G'; // Use first character of email or '?'
    }

    // Extract initials
    $words = explode(' ', $name);
    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

    // Generate random hex color
    $random_color = '#' . str_pad(dechex(wp_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

    // Determine if the input is a comment object
    $is_comment_object = is_object($id_or_email) && isset($id_or_email->comment_author);

    // Set padding style based on whether it's a comment
    $padding_style = $is_comment_object ? 'padding:5px;' : '';

    // Create fallback avatar
    $fallback_avatar = sprintf(
        '<div style="width:%1$spx;height:%1$spx;border-radius:50%%;background-color:%2$s;position:relative;display:inline-block;margin:10px;%4$s">
        <span style="position:absolute;top:50%%;left:50%%;transform:translate(-50%%,-50%%);font-size:%3$spx;font-weight:bold;color:white;height:unset;">%5$s</span>
    </div>',
        $size,
        $random_color,
        floor($size / 2.3), // Adjust font size based on avatar size
        $padding_style,
        $initials
    );

    return $fallback_avatar; // Always return the custom avatar
}




