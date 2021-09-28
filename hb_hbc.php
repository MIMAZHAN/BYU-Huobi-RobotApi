<?php
header('Content-Type:application/json; charset=utf-8');
include("public/hb_u_hy.php");

//这里没有判断是否传入合法的值，您自行开发时，需要增加判断。
$hbc = new HuobiApi($_GET['key1'], $_GET['key2']);
$res = $hbc->all_u_fuck($_GET['start'], $_GET['up1'], $_GET['down1'], $_GET['direction'], $_GET['beishu'], $_GET['zhangshu']);
exit(json_encode($res));
