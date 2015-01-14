<?

echo "1395619200";

echo date("d/m/Y H:i",1395619200);
die();
//ftp://wheeldo@192.227.128.71/domains/hr.amdocs.com/public_html/w/BCM/bin/cron.php
require "top_functions.php";
$appID=9741;
date_default_timezone_set('UTC');
$currTime=time();


//$url="http://api.wheeldo.com/APIAD.php";
//
//$postArray=array();
//$postArray['request']='sendMailFromName';
//$postArray['function_data[appID]']=$appID;
//$postArray['function_data[fromName]']="Cron BCM";
//$postArray['function_data[userID]']=71;
//$postArray['function_data[subject]']="Cron job BCM start";
//$postArray['function_data[content]']="Cron work...";
//
//$response=doRequest($url,$postArray);

$currH=(int)date('h', $currTime);

if($currH!=0)
    die("not now...");

$ans=$dbop->selectDB("game","WHERE `appID`>='{$appID}'");
for($i=0;$i<$ans['n'];$i++) {
    $row=mysql_fetch_assoc($ans['p']);
    
    $last_cron=(int)$row['last_cron'];
    
    if($last_cron==0){
        $dbop->updateDB("game",array('last_cron'=>$currTime),$row['id']);
    }
    elseif(($currTime-$last_cron)/3600>24) {
        $dbop->updateDB("game",array('last_cron'=>$currTime),$row['id']);
    }
}


$url="http://api.wheeldo.com/APIAD.php";

$postArray=array();
$postArray['request']='sendMailFromName';
$postArray['function_data[appID]']=$appID;
$postArray['function_data[fromName]']="Cron BCM";
$postArray['function_data[userID]']=71;
$postArray['function_data[subject]']="Cron job BCM working now...";
$postArray['function_data[content]']="Cron work done!";

$response=doRequest($url,$postArray);