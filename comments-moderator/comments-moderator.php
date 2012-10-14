<?php
/*
Plugin Name: ITC Comments Moderator Plugin
Plugin URI: http://www.itcuties.com/
Description: Cuts bad words from comments text
Version: 1
Author: ITCUTIES
Author URI: http://www.itcuties.com
*/

function moderateComments () {
	// Set the number of lates comments to get
	$args_comments = array(
		'number' => '100'
	);

	// Get latest comments comments
	$comments = get_comments($args_comments);

	// Iterate results
	foreach($comments as $c) {
		// Get comments as na array
		$comment_array = get_comment( $c->comment_ID, ARRAY_A );
	
		// Iterate bad words file
		$lines = file('[BAD_WORDS_URL_HERE]', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line_num => $line) {
			// Replace bad words in a comment text with some stars
			$comment_array['comment_content'] = str_replace($line, "****", $comment_array['comment_content']);
		}

		// Update comment
		wp_update_comment( $comment_array );

	}
}

// Register schedule event on plugin activation
register_activation_hook(__FILE__, 'moderate_comments_activation');
add_action('my_comment_moderation_event', 'moderateComments');

function moderate_comments_activation() {
	// Run moderation for the first time
	moderateComments();
	// Schedule moderation to run every day
	wp_schedule_event( current_time( 'timestamp' ), 'daily', 'my_comment_moderation_event');
}

// Register schedule event on plugin deactivation
register_deactivation_hook(__FILE__, 'moderate_comments_deactivation');

function moderate_comments_deactivation() {
	wp_clear_scheduled_hook('my_comment_moderation_event');
}

?>
