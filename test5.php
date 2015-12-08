<?php

/**
 *  @desc 指定圖片大小，配合 test3.php 使用。裁切檔案
 *  @created 2015/11/16
 */
ini_set('memory_limit', '64M');

//works with both POST and GET
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
	imageResizer($_GET['url'], $_GET['w'], $_GET['h']);
} elseif ($method == 'POST') {
	imageResizer($_POST['url'], $_POST['w'], $_POST['h']);
}
 
function imageResizer($url, $width, $height) {
	header('Content-type: image/jpeg');

	//list($width_orig, $height_orig) = getimagesize($url);
	//$ratio_orig = $width_orig/$height_orig;
	
	$imgsize = getimagesize($url);
    $width_orig = $imgsize[0];
    $height_orig = $imgsize[1];
    $mime = $imgsize['mime'];
	$ratio_orig = $width_orig/$height_orig;
	
	switch($mime){
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image = "imagegif";
            break;
 
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image = "imagepng";
            $quality = 7;
            break;
 
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image = "imagejpeg";
            $quality = 80;
            break;
 
        default:
            return false;
            break;
    }
	
	$dst_img = imagecreatetruecolor($width, $height);
	$src_img = $image_create($url);
	
    $width_new = $height_orig * $width / $height;
    $height_new = $width_orig * $height / $width;
	
	//if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if($width_new > $width_orig){
        //cut point by height
        $h_point = (($height_orig - $height_new) / 2);
        //copy image
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $width, $height, $width_orig, $height_new);
    }else{
        //cut point by width
        $w_point = (($width_orig - $width_new) / 2);
        imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $width, $height, $width_new, $height_orig);
    }
	
	//$image($dst_img, $dst_dir, $quality);
	$image($dst_img, null, $quality);
 
   // if($dst_img)imagedestroy($dst_img);
   // if($src_img)imagedestroy($src_img);

	//if ($width/$height > $ratio_orig) {
	//  $width = $height*$ratio_orig;
	//} else {
	//  $height = $width/$ratio_orig;
	//}

	// This resamples the image
	//$image_p = imagecreatetruecolor($width, $height);
	//$image = imagecreatefromjpeg($url);
	//imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	// Output the image
	//imagejpeg($image_p, null, 100);
	
}

/* End of File */