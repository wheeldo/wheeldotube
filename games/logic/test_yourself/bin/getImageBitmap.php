<?


//$file_to_read=$_FILES['image']['tmp_name'];
//
//function encode64($file){
//    $extension = explode(".", $file);
//    $extension = end($extension);
//
//    $binary = fread(fopen($file, "r"), filesize($file));
//
//    return 'data:image/'.$extension.';base64,'.base64_encode($binary);
//}

//$result=array();
//
//
//
//$bitmap="";
//$file_handle = fopen($file_to_read, "r");
//while (!feof($file_handle)) {
//   $line = fgets($file_handle);
//   $bitmap.=$line;
//}
//fclose($file_handle);
//
//




//if ($_FILES["image"]["error"] > 0){
//    echo "Return Code: " . $_FILES["image"]["error"] . "<br>";
//}
//else {
//    $ex=explode(".",$_FILES["image"]["name"]);
//    $ext=$ex[(count($ex)-1)];
//    $file_name="post_".$_POST['post_name']."_".time().".".$ext;
//    move_uploaded_file($_FILES["image"]["tmp_name"],"../media/uploads/" . $file_name);
//    echo "http://my.wheeldo.com/uploads/appsImages/".$file_name;
//}



$res=array();
if(!empty($_FILES)) {
    
    if ($_FILES["image"]["error"] > 0) {
        
    }
    else {
        set_time_limit(200);
        include '../vendor/cloudinary/Cloudinary.php';
        include '../vendor/cloudinary/Uploader.php';
        include '../vendor/cloudinary/Api.php';


        \Cloudinary::config(array( 
          "cloud_name" => "wheeldo", 
          "api_key" => "767556142719463", 
          "api_secret" => "JZkUVUZsUaNOS_YKzeXkOjQaXbE" 
        ));
        
        

        $res= \Cloudinary\Uploader::upload($_FILES["image"]["tmp_name"],
        array(
           "public_id" => $userID,
           "crop" => "limit", "width" => "156", "height" => "104"
        ));
        
        
        //var_dump($res);
        
        
        if(isset($res['url'])) {

            $photo=$res['url'];
            $result['url']=$res['url'];
            $note="Image uploaded successfully!";
        } 
    }
}



//header('Content-Type:application/json');
//echo json_encode($result);

echo $result['url'];



//echo encode64($file_to_read);


//$result['img']=$bitmap;
//
//header('Content-Type:application/json');
//echo json_encode($result);
