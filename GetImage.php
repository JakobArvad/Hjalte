<?php
if(isset($_GET["img"]) & isset($_GET["path"])){
	if(!isset($_GET["size"]))
		$_GET["size"]="view";
	header('Content-Type: image/jpg');
	readfile(base64_decode($_GET["path"]).'/'.$_GET["size"].'/'.base64_decode($_GET["img"]));
	//echo (base64_decode($_GET["path"]).'/'.$_GET["size"].'/'.base64_decode($_GET["img"]));

}
if(isset($_GET["mov"]) & isset($_GET["path"])){
	if(!isset($_GET["size"]))
			$_GET["size"]="SD";
		header('Content-Type: video/mp4');
		readfile(base64_decode($_GET["path"]).'/'.$_GET["size"].'/'.base64_decode($_GET["mov"]));
		//echo (base64_decode($_GET["path"]).'/'.$_GET["size"].'/'.base64_decode($_GET["img"]));

}
?>
