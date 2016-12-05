<style type="text/css">

.row div {
	display: inline-block;
	border: solid 1px black;
	text-align: center;
	font-size: 16px;
	padding: .5em;
}

.post-id {
	width: 100px;
}

.topic-id {
	width: 100px;
}

.score {
	width: 50px;
}

</style>
<?php

include('../common.php');

$query = "SELECT * FROM posts ORDER BY score DESC";
$result = mysql_query($query, $conn);
while ($row = mysql_fetch_assoc($result)) {
	$score = $row['score'];
	$topic_id = $row['topic_id'];
	$post_id = $row['post_id'];
	
	echo "
<div class='row'>
	<div class='post-id'>$post_id</div>
	<div class='topic-id'>$topic_id</div>
	<div class='score'>$score</div>
</div>";
	
}

?>