<?php 
$gener_id = 0;
if(isset($_GET['genre'])){
    $gener_id = (int)$_GET['genre'];
}
$attr = 'accept="audio/*"';
if (!empty($_GET['type']) && $_GET['type'] == 'folder') {
	$attr = 'webkitdirectory mozdirectory msdirectory odirectory directory accept="audio/*" ';
}
$form_id = time();


$data = [
	'status' => 200,
	'html' => loadPage('upload-song/upload-album-bulk-form', ['form_id' => $form_id,'genre_id' => $gener_id,'ATTR' => $attr]),
	'form_id' => $form_id
];
?>