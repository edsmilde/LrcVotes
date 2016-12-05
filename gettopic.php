<?php

header('Content-type: application/json');

include('common.php');


if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip_address = $_SERVER['REMOTE_ADDR'];
}

$topic_id = intval($_REQUEST['topicId']);

// Get scores of posts in topic
$query = "SELECT post_id, score FROM posts WHERE topic_id=$topic_id";
$result = mysql_query($query, $conn);

?>
posts: {<?php
while ($row = mysql_fetch_assoc($result)) {
	$post_id = $row['post_id'];
	$score = $row['score'];
	echo "
	'post_$post_id': $score,";
	
}
?>

},<?php

// Get posts in which user has voted
$query = "SELECT DISTINCT post_id, is_upvote FROM votes WHERE ip_address='$ip_address' AND topic_id=$topic_id";
$result = mysql_query($query, $conn);

?>

votes: {<?php
while ($row = mysql_fetch_assoc($result)) {
	$post_id = $row['post_id'];
	$is_upvote = $row['is_upvote'];
	echo "
	'post_$post_id': $is_upvote,";
	
}
?>

}<?php


?>