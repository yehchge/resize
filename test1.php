<?php

/**
 *  @desc 將指定圖片等比例縮放，只顯示，不產生圖檔。
 *  @created 2015/12/08
 */


/*
 * PHP GD
 * resize an image using GD library
 */

// File and new size
//the original image has 1120x539
$filename = 'image.jpg';
//the resize will be a percent of the original size
$percent = 0.5;

// Content type
header('Content-Type: image/jpeg');

// Get new sizes
list($width, $height) = getimagesize($filename);
$newwidth = $width * $percent;
$newheight = $height * $percent;

// Load
$thumb = imagecreatetruecolor($newwidth, $newheight);
$source = imagecreatefromjpeg($filename);

// Resize
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

// Output and free memory
//the resized image will be 400x300
imagejpeg($thumb);
imagedestroy($thumb);

?>