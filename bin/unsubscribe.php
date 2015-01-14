<?php
require_once 'top.php';

$cid=$_GET['cid'];
$uid=$_GET['uid'];

$channel=$dbop->selectAssocRow("channels","WHERE `unique_id`='{$cid}'");
$user=$dbop->selectAssocRow("users","WHERE `unique_link`='{$uid}'");



$check=$dbop->selectAssocRow("channel_user","WHERE `user_id`='{$user['id']}' AND `channel_id`='{$channel['id']}'");

if($check) {
    $dbop->deleteDB("channel_user", $check['id']);
}
?>
Thank you for your interest in <?=$channel['name']?>. You have successfully unsubscribed. <br>
<a href="http://www.wheeldo.co">Wheeldo home page</a>

