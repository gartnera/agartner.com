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
		case 'heading':
			$text = $element->data->text;
			return "<h2>$text</h2>";
		case 'list':
			return $pd->text($element->data->text);
		case 'code':
			$text = $element->data->text;
			return "<pre><code>$text</code></pre>";
		case 'video':
			$id = $element->data->remote_id;
			return "<iframe class=\"vid\" src=\"https://youtube.com/embed/$id\" width=\"580\" height=\"320\" frameborder=\"0\" allowfullscreen />";
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

$postEditPage = '<div class="content-section"><form method="get" action="postEditor.html"><select name="id">';

foreach ($postList as $post){
	preg_match('/^(.*)\.json$/', $post, $matches);
	$number = $matches[1];
	$json = json_decode(file_get_contents("../posts/json/$post"));

	$postEditPage .= "<option value=\"$number\">$json->title</option>";

	$postHtml = generatePostHtml($json);

	$postHtmlIndex = truncatePostHtml($postHtml, $number);
	$indexHtml .= $postHtmlIndex;

	$themedPost = applyBlogTheme($postHtml);
	file_put_contents("../posts/$number.html", $themedPost);
}

$postEditPage .= '</select><input type="submit" value="Submit" /></form></div>';

$themedPostEditPage = applyBlogTheme($postEditPage);
file_put_contents('editPosts.html', $themedPostEditPage);

$themedIndex = applyBlogTheme($indexHtml);
file_put_contents('../index.html', $themedIndex);

