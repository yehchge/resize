<?php

/**
 *  @desc 呼叫 test5.php 檔案，將圖片裁切後顯示，不產生檔案
 */

// makes the process simpler
function loadImage($url, $width, $height){
    echo 'test5.php?url=', urlencode($url) ,
    '&w=',$width,
    '&h=',$height;
}
	
?>

<img src="<?php loadImage('image.jpg', 300, 300) ?>" />