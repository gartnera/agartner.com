<?php
$serverbase = "http://localhost/blog/images/";
$basename = basename($_FILES['attachment']['name']['file']);
$uploadedfile = '../images/' . basename($_FILES['attachment']['name']['file']);

while (file_exists($uploadedfile)){
	$uploadedfile = str_replace('.', '1.', $uploadedfile);
}

if (!move_uploaded_file($_FILES['attachment']['tmp_name']['file'], $uploadedfile)){
	header("HTTP/1.0 500 Internal Server Error");
	die();
}

$result = array();
$result['file'] = array();
$result['file']['url'] = $serverbase . $basename;
$result['file']['basename'] = $basename;
echo json_encode($result);
