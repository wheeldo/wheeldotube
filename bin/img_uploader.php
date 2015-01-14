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

$id=$dbop->insertDB("file_data",$fields);
//file_put_contents('test.txt',"\r\n"."insert id: ".$id,FILE_APPEND);



