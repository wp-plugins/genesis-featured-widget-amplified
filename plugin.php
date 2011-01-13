<?php
/*
Plugin Name: Genesis Featured Widget Amplified
Plugin URI: http://DesignsByNicktheGeek.com
Version: 0.2b
Author: Nick Croft
Author URI: http://DesignsByNicktheGeek.com
Description: Adds additional Featured Post widget to the Genesis Theme Framework which allows support for custom post types, taxonomies, and extends the flexibility of the widget via action hooks to allow the elements to be repositioned or other elements to be added. This requires WordPress 3.0+ and Genesis 1.4+.
*/

/* Prevent direct access to the plugin */
if (!defined('ABSPATH')) {
	exit(__( "Sorry, you are not allowed to access this page directly.", 'GFPWU' ));
}

register_activation_hook(__FILE__, 'simplehooks_activation_check');
function gfwa_activation_check() {

		$latest = '1.4';

		$theme_info = get_theme_data(TEMPLATEPATH.'/style.css');

        if( basename(TEMPLATEPATH) != 'genesis' ) {
	        deactivate_plugins(plugin_basename(__FILE__)); // Deactivate ourself
            wp_die('Sorry, you can\'t activate unless you have installed <a href="http://www.studiopress.com/themes/genesis">Genesis</a>');
		}
            $version = gfwa_truncate( $theme_info['Version'], 3 );
		if( version_compare( $version, $latest, '<' ) ) {
                deactivate_plugins(plugin_basename(__FILE__)); // Deactivate ourself
                wp_die('Sorry, you can\'t activate without <a href="http://www.studiopress.com/support/showthread.php?t=19576">Genesis '.$latest.'</a> or greater');
        }

}

function gfwa_truncate ($str, $length=10)
{

      if (strlen($str) > $length)
      {
         // string exceeded length, truncate
         return substr($str,0,$length);
      }
      else
      {
         // string was already short enough, return the string
         $res = $str;
      }

      return $res;
}

define('GFWA_PLUGIN_DIR', dirname(__FILE__));

// Include files
require_once(GFWA_PLUGIN_DIR . '/widget.php');

function gfwa_before_loop($instance) { do_action('gfwa_before_loop', $instance); }
function gfwa_before_post_content($instance) { do_action('gfwa_before_post_content', $instance); }
function gfwa_post_content($instance) { do_action('gfwa_post_content', $instance); }
function gfwa_after_post_content($instance) { do_action('gfwa_after_post_content', $instance); }
function gfwa_endwhile($instance) { do_action('gfwa_endwhile', $instance); }
function gfwa_after_loop($instance) { do_action('gfwa_after_loop', $instance); }
function gfwa_list_items($instance) { do_action('gfwa_list_items', $instance); }
function gfwa_print_list_items($instance) { do_action('gfwa_print_list_items', $instance); }
function gfwa_category_more($instance) { do_action('gfwa_category_more', $instance); }
function gfwa_after_category_more($instance) { do_action('gfwa_after_category_more', $instance); }

function gfwa_form_first_column($instance) { do_action('gfwa_form_first_colum', $instance); }
function gfwa_form_second_column($instance) { do_action('gfwa_form_second_colum', $instance); }



function gfwa_exclude_taxonomies($taxonomy){
    $filters = array('', 'nav_menu');
    $filters = apply_filters('gfwa_exclude_taxonomies', $filters);
    return(!in_array($taxonomy, $filters));
}

function gfwa_exclude_post_types($type){
    $filters = array('', '');
    $filters = apply_filters('gfwa_exclude_post_types', $filters);
    return(!in_array($type, $filters));
}
