<?php
function var_dumpi($var) {
    echo '<pre style="border:1px solid gray;padding:10px;margin:10px;background-color:#D6D6D6;">';
    print_r($var);
    echo "</pre>";
}
$versions=array(1);

$full_url_explode=explode("?",$_SERVER['REQUEST_URI']);

$uri_explode=explode("/",$full_url_explode[0]);

if(count($uri_explode)<4)
    die("URI error");

$api_version=$uri_explode[2];
if(!in_array($api_version, $versions))
    die("Unavailible version: ".$api_version);

if(!isset($uri_explode[3]))
    die("No action specified");

require_once "../top.php";
require_once "versions/api_{$api_version}.php";

unset($_POST);
$_POST['uid']="fe8c3861f3";
$_POST['email']="aviadblu@gmail.com";

$action=$uri_explode[3];
$request_data=$_POST;

$api = new api();

if(!method_exists($api,$action))
     die("api::$action() isn't callable");   

        
$c=$api->$action($request_data);
header('Content-Type:application/json');
echo json_encode($c);