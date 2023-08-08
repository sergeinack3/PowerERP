<?php
	header("Content-type: image/png");

	if (isset($_GET['src']) && !empty($_GET['src'])) {
		//donnez votre path complet depuis la racine du site, ne vous souciez donc pas des problèmes de path
		$path = '../../'.trim($_GET['src']);
		$new_width = 32;
		$new_height = 32;
		if(file_exists($path)){
			$im = imagecreatefrompng($path);

			if (isset($_GET['w']) && !empty($_GET['w']) && (int)trim($_GET['w']) > 0){
				$new_width = (int)trim($_GET['w']);
			}
			if (isset($_GET['h']) && !empty($_GET['h']) && (int)trim($_GET['h']) > 0){
				$new_height = (int)trim($_GET['h']);
			}
			list($width, $height) = getimagesize($path);
			
			$new_img = imagecreatetruecolor($new_width, $new_height);
			//$new_img = imagecreatetruecolor($new_width, $new_height); //image en fond noir
			//imagecopyresampled($new_img,$im,0,0,0,0,$new_width,$new_height,$width,$height);
			imagecopyresized($new_img, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagepng($new_img);

		}
		

		//imagepng($im);
		//imagedestroy($im);
		//imagecopyresampled();
	}
?>