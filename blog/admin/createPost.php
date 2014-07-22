<?php

$input = json_decode($_POST['content']);
$input->title = trim($_POST['title']);

if (!isset($_POST['index'])) {
	$input->date = date("m/d/Y");

	$list = scandir("../posts/json/", SCANDIR_SORT_DESCENDING);

	preg_match('/^(.*)\.json$/', $list[0], $matches);

	if ($matches) {
		$index = $matches[1];
		$nextIndex = $index + 1;
	}
	else {
		$nextIndex = 0;
	}
}
else{
	$nextIndex = (int) $_POST['index'];
	$input->date = $_POST['date'];
}

$output = json_encode($input);

file_put_contents("../posts/json/$nextIndex.json", $output);

require('renderPosts.php');

echo 'Post submitted. HTML has been generated.';