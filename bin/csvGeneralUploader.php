<?php
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;

$res=array();
//header('Content-Type:application/json');
header('Content-Type: text/html; charset=utf-8');
$allowed_ext=array();
$allowed_ext[]="csv";
$allowed_ext[]="xls";
$allowed_ext[]="xlsx";



$ds=DIRECTORY_SEPARATOR;
$result2=array();

if ($_FILES["csv"]["error"] > 0){
    echo "Return Code: " . $_FILES["csv"]["error"] . "<br>";
}
else {
    $ex=explode(".",$_FILES["csv"]["name"]);
    $ext=strtolower($ex[(count($ex)-1)]);
    /////////// security checks: ///////////////////
    // check no 1:
    if(!in_array($ext, $allowed_ext)) {
        $res['status']="faild";
        $res['error']="Your file is not a csv or excel file!";
        echo json_encode($res);
        die();
    }
    
    
            
    if($ext=="xls"||$ext=="xlsx") {
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        require_once dirname(__FILE__) . $ds . '..' . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel' . $ds .'IOFactory.php';
        
        $file_name=$_FILES['csv']['tmp_name'];
        
        if (!file_exists($file_name)) {
            exit("Please run Aviadtest.php first." . EOL);
        }
        echo "here 1";
        //CloudStorageTools::serve($file_name);
        
//        $object_public_url = CloudStorageTools::getPublicUrl($file_name, false);
//        echo $object_public_url;
        
        $content=file_get_contents($_FILES['csv']['tmp_name']);

        $object_url = 'gs://my-bucket/'.time().rand(0,1000).'.'.$ext;
        $options = stream_context_create(['gs'=>['acl'=>'public-read']]);
        
        $my_file = fopen($object_url, 'w', false, $options);
        fwrite($my_file, $content);
        fclose($my_file);
        $object_public_url = CloudStorageTools::getPublicUrl($object_url, false);
        
        echo $object_public_url;
        
        $objPHPExcel = PHPExcel_IOFactory::load($object_public_url);
        

        echo "here 2";
        $rowIterator=$objPHPExcel->getActiveSheet()->getRowIterator();
        
        echo "here 3";
        $i=0;
        $result=array();
        $array_data = array();
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            if(1 == $row->getRowIndex ()) continue;//skip first row
            $rowIndex = $row->getRowIndex ();
            $array_data[$rowIndex] = array('A'=>'', 'B'=>'','C'=>'','D'=>'');
            set_time_limit(5);


            foreach ($cellIterator as $cell) {
                if('A' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('B' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('C' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('D' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('E' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('F' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('G' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('H' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('I' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('J' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('K' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                } else if('L' == $cell->getColumn()){
                    $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                }

            }
            
            
            $i++;
        }
        
        
        $i=0;
        foreach($array_data as $row) {
            $c=0;
            foreach($row as $cell):
                $result[$c][]=$cell;
                $c++;
            endforeach;
            
            
            
            
            
            $j=0;
            $result2[$i]['question']=$row['A'];
            $result2[$i]['rank']=$row['B'];
            $result2[$i]['answer']=$row['C'];
            foreach($row as $cell):
                if($j>2)
                    $result2[$i]['answers'][]=$cell;
                $j++;
            endforeach;
            $i++;
        }
        
        //var_dump($result);
        
        
        $base_path=dirname(__FILE__);
        $file_location=$base_path.$ds."..".$ds."uploads".$ds."csv".$ds.$_POST['res_mark'].".txt";
        
        file_put_contents($file_location, json_encode($result2)); 
        $res['status']="ok";
        $res['data']=$result;
        echo json_encode($res);
        die();
    }

    elseif(($handle = fopen($_FILES['csv']['tmp_name'], 'r')) !== FALSE) {
        $result=array();
        // necessary if a large csv file
        set_time_limit(5);

        $row = 0;
        
        while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // number of fields in the csv
            $num = count($data);
            
            $result2[]=$data;

            for($i=0;$i<$num;$i++):
                $result[$i][]=utf8_encode($data[$i]);
            endfor;

            $row++;
        }
        fclose($handle);

        
        $ds=DIRECTORY_SEPARATOR;
        $base_path=dirname(__FILE__);
        $file_location=$base_path.$ds."..".$ds."uploads".$ds."csv".$ds.$_POST['res_mark'].".txt";
        //unlink($file_location);
        file_put_contents($file_location, utf8_encode(json_encode($result2, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE))); 
        $res['status']="ok";
        $res['data']=$result;
        echo json_encode($res);
        die();
    }
}