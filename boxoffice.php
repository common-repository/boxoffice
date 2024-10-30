<?php
/*
Plugin Name: BoxOffice
Plugin URI: http://www.woodymood-dev.net/cms/wordpress/en/2009/04/03/plugin-boxoffice
Description: Display a boxoffice of all your articles ordered by their count views. Requires <a href="http://wordpress.org/extend/plugins/popular-posts-plugin/" target="_blank">Popular Posts</a>
Version: 0.1
Author: Anthony Dubois
Author URI: http://www.woodymood-dev.net/cms/wordpress/
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/




load_plugin_textdomain('boxoffice', PLUGINDIR . '/' . dirname(plugin_basename (__FILE__)) . '/lang');
	

add_filter( 'the_content', 'boxoffice' );
		

function boxoffice($content) {

	if ( function_exists('popular_posts_views') && is_page() && strpos($content, '<!-- box office replacement point -->') !== false ) { 

		$args = array(
		'post_type' => 'post',
		'numberposts' => -1,
		'post_parent' => null
		); 
		
		$all_posts_com = get_posts($args);
		
		if ($all_posts_com) {
		
			$replace = 
			'
			<div class="boxoffice">
			<table>';
		
			$all = array();
			
			foreach ($all_posts_com as $post_given) {
			
				$date_stamp = strtotime( $post_given->post_date );
				$date_s = date_i18n( get_option( 'date_format' ), $date_stamp );
				
				$vv = popular_posts_views($post_given->ID);
				$vv = ($vv > 1 ? $vv . ' ' . __('visits', 'boxoffice') : ($vv == 1 ? ' 1 ' . __('visit', 'boxoffice') : ''));
				
				$title = '<a href="' . get_permalink($post_given->ID) . '">' . $post_given->post_title . '</a>';
				
				$new_post = array($post_given->ID, $title, $date_s, $vv);
				
				if ( !array_key_exists($vv, $all) ) {
					// new count, create
					$all[$vv] = array();
					$all[$vv][] = $new_post;
				}
				else {
					// old count
					$all[$vv][] = $new_post;
				}
				
			}
			
			krsort($all, SORT_NUMERIC);
			
			foreach ( $all as $tp ) {
				foreach ( $tp as $p ) {
					$replace .= 
						'
						<tr valign="top">
						<td align="right" width="80"><p>' . $p[3] . '</p></td>
						<td><p>' . $p[1] . '</p></td>
						<td align="right" width="120"><p>' . $p[2] .'</p></td>
						</tr>
						';
				}
				
			}
			
			$replace .= '</table></div>';
			
			$content = str_replace('<p><!-- box office replacement point --></p>', $replace, $content);
		}
		else {
			echo __('Something wrong happend while calculating this page!', 'boxoffice');
		}


		
	}
	
	return $content;

}




?>