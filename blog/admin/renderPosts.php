<?php
require 'Parsedown.php';

define("POSTS_PER_PAGE", 5);

function processElement($element)
{
	$pd = Parsedown::instance();
	switch ($element->type) {
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

function generatePostHtml($json)
{
	$output = '<div class="content-section">';
	$output .= "<h1>$json->title</h1>";
	$output .= "<div class=\"date\">$json->date</div>";

	foreach ($json->data as $element) {
		$output .= processElement($element);
	}

	$output .= '</div>';

	return $output;
}

function truncatePostHtml($postHtml, $number)
{
	$summary = substr($postHtml, 0, strpos($postHtml, '</p>') + 4);
	$summary .= "<a href=\"posts/$number.html\">More...</a>";
	$summary .= '</div>';
	return $summary;
}

function applyBlogTheme($content = '', $title = '', $description = '')
{
	$theme = file_get_contents('template.html');
	$theme = str_replace('***TITLE***', $title, $theme);
	$theme = str_replace('***DESCRIPTION***', $description, $theme);
	$theme = str_replace('***CONTENT***', $content, $theme);

	return $theme;
}

function generateIndexes($posts)
{
	$postCount = count($posts);
	$indexCount = ceil($postCount / POSTS_PER_PAGE);
	$currentPost = 0;

	for ($i = 1; $i <= $indexCount; ++$i) {
		$indexPosts = '';
		for ($j = $currentPost + POSTS_PER_PAGE; $currentPost < $j && $currentPost < $postCount; ++$currentPost) {
			$indexPosts .= $posts[$currentPost];
		}
		$indexPosts .= generatePagation($i, $indexCount);

		$themedIndex = applyBlogTheme($indexPosts, 'Alex Gartner\' Blog');

		if ($i == 1){
			file_put_contents('../index.html', $themedIndex);
		}
		else{
			file_put_contents("../index$i.html", $themedIndex);
		}
	}

}

function generatePagation($currentIndex, $indexCount)
{
	//TODO: add ellipsis for more pages
	$str = '<div class="content-section pagation">';
	for ($i = 1; $i <= $indexCount; ++$i) {
		if ($i == 1){
			if ($i == $currentIndex){
				$str .= "<a class=\"active\" href=\"index.html\">1</a>";
			}
			else{
				$str .= "<a href=\"index.html\">1</a>";
			}
		}
		else if ($i == $currentIndex) {
			$str .= "<a class=\"active\" href=\"index$i.html\">$currentIndex</a>";
		}
		else {
			$str .= "<a href=\"index$i.html\">$i</a>";
		}
	}
	$str .= '</div>';
	return $str;
}

$postList = array_diff(scandir("../posts/json", SCANDIR_SORT_DESCENDING), array('.', '..'));

$indexHtml = array();

$postEditOptions = '<div class="content-section"><form method="get" action="postEditor.html"><select name="id">';

foreach ($postList as $post) {
	preg_match('/^(.*)\.json$/', $post, $matches);
	$number = $matches[1];
	$json = json_decode(file_get_contents("../posts/json/$post"));

	$postEditOptions .= "<option value=\"$number\">$json->title</option>";

	$postHtml = generatePostHtml($json);

	$postHtmlIndex = truncatePostHtml($postHtml, $number);
	array_push($indexHtml, $postHtmlIndex);

	preg_match('/(?:<h1>)(.*)(?:<\/h1>)/', $postHtml, $matches);
	$title = $matches[1];
	preg_match('/(?:<p>)(.*)(?:<\/p>)/', $postHtml, $matches);
	$description = strip_tags($matches[1]);

	$themedPost = applyBlogTheme($postHtml, $title, $description);
	file_put_contents("../posts/$number.html", $themedPost);
}

$postEditOptions .= '</select><input type="submit" value="Submit" /></form></div>';

$themedPostEditPage = applyBlogTheme($postEditOptions);
file_put_contents('editPosts.html', $themedPostEditPage);

generateIndexes($indexHtml);