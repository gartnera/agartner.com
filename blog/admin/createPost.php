<?php

$list = scandir("../posts/json/", SCANDIR_SORT_DESCENDING);

preg_match('/^(.*)\.json$/', $list[0], $matches);

if ($matches) {
	$index = $matches[1];
	$nextIndex = $index + 1;
}
else {
	$nextIndex = 0;
}

$input = json_decode($_POST['content']);
$input->title = trim($_POST['title']);
$input->date = date("m/d/Y");

$output = json_encode($input);

file_put_contents("../posts/json/$nextIndex.json", $output);

require('renderPosts.php');

echo 'Post submitted. HTML has been generated.';