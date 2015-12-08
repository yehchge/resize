<?php

echo "bug, 會將原圖替換掉"; exit;
/*
 * PHP GD 等比例縮小圖片大小
 * resize an image using GD library
 */

// File and new size
//the original image has 800x600
$filename = 'image.jpg';
//the resize will be a percent of the original size
//$percent = 0.5;
$maxDim = 1024;

// Content type
header('Content-Type: image/jpeg');

// Get new sizes
//list($width, $height) = getimagesize($filename);
list($width, $height, $type, $attr) = getimagesize($filename);

if ($width > $maxDim OR $height > $maxDim) {
	$target_filename = $filename;
	$fn = $filename;
	$size = getimagesize($fn);
	$ratio = $size[0]/$size[1]; // width/height
	if ($ratio > 1) {
		$width = $maxDim;
		$height = $maxDim/$ratio;
	} else {
		$width = $maxDim*$ratio;
		$height = $maxDim;
	}
	$src = imagecreatefromstring(file_get_contents($fn));
	$dst = imagecreatetruecolor($width, $height);
	imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
	imagejpeg($dst, null, 100);
	imagedestroy($src);
	imagepng($dst, $target_filename); // adjust format as needed
	imagedestroy($dst);
}

?>