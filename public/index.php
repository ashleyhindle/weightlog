<?php
header('Content-Type: application/json');
$token = (isset($_GET['token'])) ? $_GET['token'] : '';
if (empty($token)) {
    die(json_encode([
        'error' => 'Empty token'
    ]));
}

require '../src/bootstrap.php';
use WeightLog\WeightLog;
use WeightLog\Db;

$db = Db::getInstance();
$weightLog = new WeightLog($db);

die(json_encode($weightLog->getAllWeightsByToken($token)));
