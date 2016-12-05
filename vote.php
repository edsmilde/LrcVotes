<?php

header('Content-type: application/json');

// Connect to DB

$db_username = 'bcf8153a0bef37';
$db_password = 'bac59ded';
$db_host = 'us-cdbr-azure-west-b.cleardb.com';
$db_port = '3306';
$db_name = 'lrc_votes_php';

$conn = mysql_connect("$db_host:$db_port", $db_username, $db_password);
echo mysql_error();

mysql_select_db($db_name);
echo mysql_error();

// Info about this vote

$post_id = intval($_REQUEST['postId']);
$topic_id = intval($_REQUEST['topicId']);
$is_cancel = $_REQUEST['isCancel'] ? 1 : 0;
$is_upvote = $_REQUEST['isUpvote'] ? 1 : 0;

$is_upvote = intval($_REQUEST['isUpvote']);

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip_address = $_SERVER['REMOTE_ADDR'];
}

if ($is_cancel) {
	$change_upvote = 0;
	$change_downvote = 0;
} else {
	if ($is_upvote) {
		$change_upvote = 1;
		$change_downvote = 0;
	} else {
		$change_upvote = 0;
		$change_downvote = 1;
	}
}

// Check if user has voted on this post already

$query = "SELECT * FROM votes WHERE ip_address='$ip_address' AND post_id=$post_id";
$result = mysql_query($query, $conn);
if ($row = mysql_fetch_assoc($result)) {
	// user has already voted
	
	if ($is_cancel) {
		if ($row['is_upvote']) {
			// cancel past upvote
			$change_upvote = -1;
		} else {
			// cancel past downvote
			$change_downvote = -1;
		}
		// delete
		$query = "DELETE FROM votes WHERE ip_address='$ip_address' AND post_id=$post_id";
		mysql_query($query, $conn);
		echo mysql_error();
	} else {
		if ($row['is_upvote'] == $is_upvote) {
			// user already voted with this exact preference
			$change_upvote = 0;
			$change_downvote = 0;
		} else {
			if ($is_upvote) {
				// user is changing downvote to upvote
				$change_downvote = -1;
			} else {
				// user is changing upvote to downvote
				$change_upvote = -1;
			}
			// user changed vote, update it
			$query = "UPDATE votes SET is_upvote=$is_upvote WHERE ip_address='$ip_address' AND post_id=$post_id";
			mysql_query($query, $conn);
			echo mysql_error();
			}
		}
} else if (!$is_cancel) {
	// doesn't exist yet, insert into votes
	$query = "INSERT INTO votes (post_id, topic_id, is_upvote, ip_address, date_submitted) VALUES ($post_id, $topic_id, $is_upvote, '$ip_address', NOW())";
	$result = mysql_query($query, $conn);
	echo mysql_error();


}




if ($change_upvote or $change_downvote) {
	// check if exists in posts
	$query = "SELECT * FROM posts WHERE post_id=$post_id";
	$result = mysql_query($query, $conn);
	echo mysql_error();

	if ($row = mysql_fetch_assoc($result)) {
		// already exists, update
		$query = "UPDATE posts SET upvotes=upvotes+$change_upvote, downvotes=downvotes+$change_downvote WHERE post_id=$post_id";
		$result = mysql_query($query, $conn);
		echo mysql_error();
	} else {
		// doesn't exist, insert
		$query = "INSERT INTO posts (post_id, topic_id, upvotes, downvotes, date_first_voted) VALUES ($post_id, $topic_id, $change_upvote, $change_downvote, NOW())";
		$result = mysql_query($query, $conn);
		echo mysql_error();
	}
}



?>
response: {
	upvoteRecorded: <?php echo $change_upvote; ?>,
	downvoteRecorded: <?php echo $change_downvote; ?>,
}