<?php

/**
 *  @desc 由網址帶入參數，縮放圖片後顯示，不產生圖檔
 *  @usage http://localhost/resize/test2.php?url=http://texipaleo.files.wordpress.com/2014/01/cropped-cropped-cropped-cropped-blogheader4.jpg&w=800&h=600
 *  @created 2015/12/08
 */

function imageResizer($url, $width, $height) {

    header('Content-type: image/jpeg');

    list($width_orig, $height_orig) = getimagesize($url);

    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig) {
      $width = $height*$ratio_orig;
    } else {
      $height = $width/$ratio_orig;
    }

    // This resamples the image
    $image_p = imagecreatetruecolor($width, $height);
    $image = imagecreatefromjpeg($url);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    // Output the image
    imagejpeg($image_p, null, 100);
    
}

//works with both POST and GET
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'GET') {
    imageResizer($_GET['url'], $_GET['w'], $_GET['h']);
} elseif ($method == 'POST') {
    imageResizer($_POST['url'], $_POST['w'], $_POST['h']);
}
    
?>