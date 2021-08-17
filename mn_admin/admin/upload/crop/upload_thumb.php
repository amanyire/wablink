<?php
  include("../../../../connect.php");
  session_start();
 if(isset($_REQUEST['crane_id'])){
	  $photo_id=$_REQUEST['crane_id'];
  }else{
  	//var_dump($_REQUEST);
      echo "<h1 style='color:red'>Photo not foundxx</h1>";
	  return;
  }

//var_dump($_REQUEST); exit();
	//get id of the next photo to be edited;
	$sely=mysqli_query($conn,"SELECT * from item_photo where photo_id = '$photo_id' ");
	$valy=mysqli_fetch_assoc($sely);
	if($valy){
	
	}else{
	$sely=mysqli_query($conn,"SELECT * from item_photo order by update_time DESC ");
	$valy=mysqli_fetch_assoc($sely);
	}
	$nextid=$valy['photo_id'];
	$thmb=str_replace(".","_thumb.",$valy['photo_name']);
	$bigphoto=$valy['photo_name'];
	$nextphoto=$thmb;
	$photoname=$thmb;
	/*$ext= explode(".", $lastimage);//explode the image name
	$last_value=$ext[0];//the name of the image without the extension
	if($last_value==21){
	$next_value=1;
	}else{
	$next_value=$last_value+1;
	}*/
	$next_value=time()."".rand(100, 1000);
	
	
// If you want to ignore the uploaded files, 
// set $demo_mode to true;

$demo_mode = false;
$upload_dir = "../../../../images/products/";//where to upload images
$new_image=$thmb;//image name
//$allowed_ext = array('jpg','jpeg','png','gif','JPG','PNG');

error_reporting (E_ALL ^ E_NOTICE);
session_start(); //Do not remove this
//only assign a new timestamp if the session variable is empty
if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key'])==0){
    $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
	$_SESSION['user_file_ext']= "";
}
#########################################################################################################
# CONSTANTS																								#
# You can alter the options below																		#
#########################################################################################################
//$upload_dir = "upload_pic"; 				// The directory for the images to be saved in
//$upload_path = $upload_dir."/";				// The path to where the image will be saved
//$large_image_prefix = "bigsize"; 			// The prefix name to large image
//$thumb_image_prefix = "thumb";			// The prefix name to the thumb image
//$large_image_name = $large_image_prefix;     // New name of the large image (append the timestamp to the filename)
//$thumb_image_name = $thumb_image_prefix;     // New name of the thumbnail image (append the timestamp to the filename)
$max_file = "10"; 							// Maximum file size in MB
$max_width = "1300";// Max width allowed for the large image

$thumb_width = "425";						// Width of thumbnail image
$thumb_height = "425";						// Height of thumbnail image

// Only one of these image types should be allowed for upload
$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
$image_ext = "";	// initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext.= strtoupper($ext)." ";
}


##########################################################################################################
# IMAGE FUNCTIONS																						 #
# You do not need to alter these functions																 #
##########################################################################################################
function resizeImage($image,$width,$height,$scale) {
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$image); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);  
			break;
    }
	
	//chmod($image, 0777);
	return $image;
}

//You do not need to alter these functions
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	switch($imageType) {
		case "image/gif":
			$source=imagecreatefromgif($image); 
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image); 
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image); 
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	switch($imageType) {
		case "image/gif":
	  		imagegif($newImage,$thumb_image_name); 
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$thumb_image_name,90); 
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$thumb_image_name);  
			break;
    }
	//chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}

//You do not need to alter these functions
function getHeight($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}
//You do not need to alter these functions
function getWidth($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

//Image Locations
$large_image_location = $upload_dir.$bigphoto;
$thumb_image_location = $upload_dir.$photoname;

//Check to see if any images with the same name already exist
/*if (file_exists($large_image_location)){
	if(file_exists($thumb_image_location)){
		$thumb_photo_exists = "<img src='upload_pic/thumb.jpg' alt=\"Thumbnail Image\"/>";
	}else{
		$thumb_photo_exists = "";
	}
   	$large_photo_exists = "<img src='upload_pic/bigsize.jpg' alt=\"Large Image\"/>";
} else {
   	$large_photo_exists = "";
	$thumb_photo_exists = "";
}*/

$large_photo_exists=1;

if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0) {
	//Get the new coordinates to crop the image.
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w  = $_POST["w"];
	$h  = $_POST["h"];
	//Scale the image to the thumb_width set above
	$scale = $thumb_width/$w;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
	
	$time=time();
	//mysql_query("update last_edited set photo_name='$new_image' where category='$category'")or die(mysql_error());
	 //mysql_query("update crane_photo set update_time='$time',photo_name='$new_image' where photo_id='$nextid'")or die(mysql_error());
	 //$oldfile=$upload_dir.$nextphoto;	
	 try{
	 //unlink($oldfile);//delete previous file
	 }catch(Exception $e){
		 //file not found
	 }
	 
	//Reload the page again to view the thumbnail
	}
?>
<head>
	<script type="text/javascript" src="js/jquery-pack.js"></script>
	<script type="text/javascript" src="js/jquery.imgareaselect.min.js"></script>
</head>
<body bgcolor="#333333">
<?php
//Only display the javascript if an image has been uploaded
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("You must make a selection first");
			return false;
		}else{
			return true;
		}
	});
}); 

$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
});

</script>
<?php }?>
<?php
//Display error message if there are any
if(strlen($error)>0){
	echo "<ul><li><strong>Error!</strong></li><li>".$error."</li></ul>";
}
?>

<?php
		if(strlen($large_photo_exists)>0){?>
		<!--<p style="color:#999999;font-size:xx-large; position: fixed; top:0px; left: 0px; z-index: 1000">Photo thumb</p>-->
		<div style="text-align: center; width: 900px">
			<img src="<?php echo $large_image_location; ?>" style=" margin-right: 10px;float:left" id="thumbnail" alt="Create Thumbnail" />
			<div style="border:1px #e5e5e5 solid;position:fixed;right:10px;bottom:80px;  overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
				<img src="<?php echo $large_image_location; ?>" style="" alt="Thumbnail Preview" />
			</div>
			<br style="clear:both;"/>
			<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
				<input type="hidden" name="x1" value="" id="x1" />
				<input type="hidden" name="y1" value="" id="y1" />
				<input type="hidden" name="x2" value="" id="x2" />
				<input type="hidden" name="y2" value="" id="y2" />
				<input type="hidden" name="w" value="" id="w" />
				<input type="hidden" name="h" value="" id="h" />
				<input type="hidden" name="crane_id" value="<?php echo $photo_id; ?>" />
				<input style="position:fixed;right:10px;top:40px;border:1px solid #993333;color:#F7F7F7;padding:10px;border-radius:30px;background-color:#993333; z-index: 1000" type="submit" name="upload_thumbnail" value="SAVE IMAGE" id="save_thumb" />
			</form>
		</div>
	<?php 	}else{ ?>
	<form name="photo" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
	<input type="file" onChange="submit()" name="image" size="30" /> <input type="hidden" name="upload" value="Upload" />
	</form>
	<?php }
	
	?>
	<style type="text/css">
	::-webkit-scrollbar {
    width: 10px;
	background-color:#red;
	}
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.9); 
    border-radius: 5px;
}
 
::-webkit-scrollbar-thumb {
    border-radius: 5px;
    -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.9); 
}

	</style>
</body>
</html>
