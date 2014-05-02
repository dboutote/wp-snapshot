<?php
/*
Plugin Name: WP Snapshot
Plugin URI: http://darrinb.com/notes/2008/wp-snapshot-a-multiuse-wordpress-plugin-for-getting-a-snippet-of-text/
Description: Display a snapshot of text.  Useful for meta descriptions, post snippets, or your own custom text.
Version: 1.0
Author: Darrin Boutote
Author URI: http://darrinb.com
*/
/*  
	Copyright 2008  Darrin Boutote  Contact: http://darrinb.com/hello	
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.
	
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/.
*/

function mish_wp_get_snapshot( $custom_text = '', $word_limit = '', $trailing_text = '' ) {

	// Init variables
	global $post;
	$snap_text = '';
	
	// Set the default word limit to 27 if no limit is set by user
	$word_limit = ( '' === $word_limit ) ? 27 : $word_limit;
	
	// Set the trailing text to an ellipsis (...) if none is set by user
	$trailing_text = ( '' === $trailing_text ) ? '&#8230;' : $trailing_text;
	
	/* 
	 * Set the trailing text  to nothing ('') if the user is using Snapshot 
	 * for a Meta Description.
	 */
	$trailing_text = ( 'meta-desc' === $custom_text ) ? '' : $trailing_text;
				
	// #1. Let's get the content to snapshot:
	
	// If the user enters custom text AND it's NOT "meta-desc"
	if ( '' !== $custom_text && 'meta-desc' !== $custom_text ) {		
		$snap_text = $custom_text; // Use the text.
	}
	
	// If the user didn't enter custom text
	if( '' === $custom_text || 'meta-desc' === $custom_text){
	
		// if it's a post or page
		if( is_single() || is_page() ) {
	
			// If the post excerpt is empty we'll use the post content.
			$snap_text = ( '' !== $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;	

			$snap_text = strip_shortcodes( $snap_text );
			
		
			// If it's a Page and the content AND excerpt are empty, use the post (Page) title.
			if( is_page() && '' === $snap_text ){
				$snap_text = $post->post_title;
			}
		}

		// if it's an Archive (Category, Tag, Author, or Date-based)
		if( is_archive() ) {
;
			if( is_year() ) { 
				$snap_text = 'An archive of all posts for the year of ' . get_the_time('Y'); 
			}
			
			if( is_month() ) { 
				$snap_text = 'An archive of all posts for the month of ' . get_the_time('F, Y'); 
			}

			if( is_day() ) { 
				$snap_text = 'An archive of all posts for the day of' . get_the_time('F, jS, Y'); 
			}
			
			if( is_tag() ) { 
				$snap_tag = single_tag_title('', false);
				$snap_text = 'An archive of all posts tagged with &#8220;' . $snap_tag . '&#8221;.'; 
			}
			
			/**
			 * If it's a category, check for a description
			 * 
			 * Strip the [p] tags and [br] tag from the Category Desc (WP adds 
			 * these).  Even if it's blank, WP adds a [br /] tag which will throw a "false positive".
			 */
			if( is_category() ) {
				$cat_desc = category_description();
				$cat_desc = str_replace( array('<p>','</p>', '<br />'), '', $cat_desc );
				$cat_desc = trim( $cat_desc );
				if( '' !== $cat_desc ) {
					$snap_text = $cat_desc;
				} else {
					$snap_text = 'An archive of posts published in the ' . single_cat_title('', false) . ' Category.' ;
				}				
			}
			
			// default (author, custom taxonomy, custom post-type archive)
			if( '' === $snap_text ) {
				$snap_text = 'An archive of posts.';
			}
						
		}
		
		/**
		 * If it's the blog index or the front page
		 *
		 * Check if the blog description is blank.
		 */
		if( is_home() || is_front_page() ){
			$blog_desc = strip_tags( get_bloginfo('description') );
			$blog_desc = trim( $blog_desc );
			if( '' !== $blog_desc ) {
				$snap_text = $blog_desc;
			}			
		}
				
	}
	
	// default
	if( '' === trim($snap_text)) {
		$snap_text = 'Just another WordPress website.' ;
	}	
	

	// clean up the snap text 
	$snap_text = wp_kses($snap_text, $allowed_html=array());

	// Convert HTML entities (for "true" XHTML compliance (XHTML 1.1 throws an error on '&')).
	$encoded_text = htmlspecialchars( $snap_text, ENT_QUOTES, "utf-8" );
	$encoded_text = preg_replace( array('/&amp;/', '/ & /'), array('&', ' &amp; '), $encoded_text );
	
	// Split the text by any number of commas or space characters and put into an array.
	$split_text = preg_split( "/[\s,]+/", $encoded_text );
	
	// Count the keys (words) in the array
	$word_count = count( $split_text );
	
	// If the # of words is greater than the limit set by the user/function..
	if ( $word_count > $word_limit ) 
	{
		// ..Take a slice of the array; default is the first 27 words,
		$text_slice = array_slice( $split_text, 0, $word_limit );
				
		// ..Join the pieces of the array slice with a space between the words,
		$snapshot = implode( " ", $text_slice );

		// ..Get the length of the string,
		$snap_len = strlen( $snapshot );
		
		// ..Init an array for punctuation marks,
		$punc_array = array( '.', '?', '!', ':', ';' );
		
		// ..Determine if the snapshot text ends in a punctuation mark,
		$punc_end = ( in_array( $snapshot[$snap_len-1], $punc_array ) ) ? true : false;
		
		// ..If it does, set the trailing text to blank (''),
		$trailing_text = ( $punc_end ) ? '' : $trailing_text;
		
		// ..Finally init a new var for Snapshot
		$snapshot .= $trailing_text;
	}
	
	// If the # of words is NOT greater than the limit the user/function set..
	else 
	{
		// We'll use all of it
		$snapshot = $encoded_text;
	}
	
	return $snapshot;
}

function mish_wp_snapshot( $custom_text = '', $word_limit = '', $trailing_text = '' )
{
	echo mish_wp_get_snapshot($custom_text, $word_limit, $trailing_text);
}

?>