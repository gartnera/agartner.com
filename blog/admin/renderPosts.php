<?php
require 'Parsedown.php';

function processElement($element){
	$pd = Parsedown::instance();
	switch ($element->type){
		case 'text':
			return $pd->text($element->data->text);
		case 'image':
			$src = $element->data->file->url;
			return "<img src=\"$src\" />";
	}
	return null;
}

function generatePostHtml($json){
	$output = '<div class="content-section">';
	$output .= "<h1>$json->title</h1>";
	$output .= "<div class=\"date\">$json->date</div>";

	foreach ($json->data as $element){
		$output .= processElement($element);
	}

	$output .= '</div>';

	return $output;
}

function truncatePostHtml($postHtml, $number){
	$summary = substr($postHtml, 0, strpos($postHtml, '</p>') + 4);
	$summary .= "<a href=\"posts/$number.html\">More...</a>";
	$summary .= '</div>';
	return $summary;
}

function applyBlogTheme($output){
	$theme = file_get_contents('template.html');
	return str_replace('***CONTENT***', $output, $theme);
}


$postList = array_diff(scandir("../posts/json", SCANDIR_SORT_DESCENDING), array('.', '..'));

$indexHtml = '';

foreach ($postList as $post){
	preg_match('/^(.*)\.json$/', $post, $matches);
	$number = $matches[1];
	$json = json_decode(file_get_contents("../posts/json/$post"));

	$postHtml = generatePostHtml($json);

	$postHtmlIndex = truncatePostHtml($postHtml, $number);
	$indexHtml .= $postHtmlIndex;

	$themedPost = applyBlogTheme($postHtml);
	file_put_contents("../posts/$number.html", $themedPost);
}

$themedIndex = applyBlogTheme($indexHtml);
file_put_contents('../index.html', $themedIndex);

