<?php
require "top.php";
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;
//var_dump($_FILES);

set_time_limit(60);
$data=$_POST;
$appengine_img_save=false;

$arr=array("img"=>"");




if (!function_exists('curl_init')) {
    require_once '../vendor/purl-master/src/Purl.php';
}


if($appengine_img_save) {
    $object_image_file = $_FILES['img_upload']['tmp_name'];
    $object_image_url = CloudStorageTools::getImageServingUrl($object_image_file);
    $arr['img']=$object_image_url;
}
else {

    $object_image_file = $_FILES['img_upload']['tmp_name'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, 'http://api.wheeldo.com/cloudinary_uploader.php' );
	//most importent curl assues @filed as file field
    
    
        //file_get_contents($object_image_file);
    $img_effect="";
    if(isset($_POST['img_effect']))
        $img_effect=$_POST['img_effect'];
    
    $post_array = array(
        "img_effect"=>$img_effect,
        "file_name"=>$_FILES['img_upload']['name'],
        "file"=>file_get_contents($object_image_file)
    );
    

    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
    $response = curl_exec($ch);
    $arr['img']=$response;
}


$fields=array();
$fields['res_mark']=$data['res_mark'];
$fields['data']=json_encode($arr);
$fields['time']=time();

//var_dump($fields);

//echo "file upload is complete! ".'<img src="'.$response.'">';
$id=$dbop->insertDB("file_data",$fields);


//echo json_encode(array(
//    "url"=>$response
//));



$img_effect="c_lpad,e_auto_color,h_77,w_115";
if(true) {
    //unlink($img_url);
    $url=$response;
    if($img_effect!="") {
        $ex=explode("upload/",$url);
        $thumbnail=implode("upload/".$img_effect."/",$ex);
    }
}


$ex=explode(".",$_POST['file_name']);

$user_id=$_POST['user_id'];
$dbop->insertDB("user_library",array(
    "user_id"=>$user_id,
    "name"=>$_POST['file_name'],
    "thumbnail"=>$thumbnail,
    "url"=>$response,
    "type"=>$ex[count($ex)-1],
    "time"=>time()
));

echo '"'.$_POST['file_name'].'" upload is complete';



//file_put_contents('test.txt',"\r\n"."insert id: ".$id,FILE_APPEND);



