<?php
// check gclc files
$gclcfolder = '../gclcimages/';
$imagefolder = 'images/gclcimages_desktop/';
$imagefoldermobile = 'images/gclcimages_mobile/';
if ($handleGclc = opendir($gclcfolder)) {
	$gclcfiles = array();
	while (false !== ($file = readdir($handleGclc))) {
		$extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
		if (($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG')) {
			$gclcfiles[] = $file;
		}
	}
}
closedir($handleGclc);
if ($handleImage = opendir($imagefolder)) {	
	$imagefiles = array();
	while (false !== ($file = readdir($handleImage))) {
		$extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
		if (($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG')) {
			$imagefiles[] = $file;
		}
	}
}
closedir($handleImage);

$newfiles = array_diff($gclcfiles, $imagefiles);
$deletedfiles = array_diff($imagefiles, $gclcfiles);

if (!empty($newfiles)) {
	foreach ($newfiles as $key => $newfile) {
		$extension = strtoupper(pathinfo($newfile, PATHINFO_EXTENSION));
		if (($extension == 'JPG') || ($extension == 'JPEG')) {
			$i = imagecreatefromjpeg($gclcfolder.$newfile);
			if (!is_null($i)) {
				$thumb = thumbnail_cropped($i, 900, 360);
				imagejpeg($thumb, $imagefolder.$newfile, 60);
			
				$thumb = thumbnail_cropped($i, 320, 128);
				imagejpeg($thumb, $imagefoldermobile.$newfile, 60);
				imagedestroy($i);
			}
		} elseif ($extension == 'PNG') {
			$i = imagecreatefrompng($gclcfolder.$newfile);
			if (!is_null($i)) {
				$thumb = thumbnail_cropped($i, 900, 360);
				imagepng($thumb, $imagefolder.$newfile,5);
			
				$thumb = thumbnail_cropped($i, 320, 128);
				imagepng($thumb, $imagefoldermobile.$newfile,5);
				imagedestroy($i);
			}
		}

	}
}

if (!empty($deletedfiles)) {
	foreach ($deletedfiles as $deletedfile) {
	    if (file_exists($imagefolder.$deletedfile)) {
        	unlink($imagefolder.$deletedfile);
    	}
    	if (file_exists($imagefoldermobile.$deletedfile)) {
    		unlink($imagefoldermobile.$deletedfile);
    	}
	}
	
}

function thumbnail_cropped($img, $box_w, $box_h) {
	$new = imagecreatetruecolor($box_w, $box_h);
	
	if (imagesx($img) > $box_w) {
		$ratio = $box_w / imagesx($img);
		$sy = floor(imagesy($img) * $ratio);
		
		if(!imagecopyresampled($new, $img,
				0, 0, //dest x, y (margins)
				0, 0, //src x, y (0,0 means top left)
				$box_w, $sy,//dest w, h (resample to this size (computed above)
				imagesx($img), imagesy($img)) //src w, h (the full size of the original)
		) {
			imagedestroy($new);
			return null;
		}
		return $new;
	}
	return $img;
}