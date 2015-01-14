<?php
require "bin/top.php";

$data=$_POST;

$fields=array();
$fields['res_mark']=$data['res_mark'];
$fields['data']=$data['data'];
$fields['time']=time();

$id=$dbop->insertDB("file_data",$fields);
echo mysql_error();
echo "insert id: ".$id;