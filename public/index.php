<?php
$token = (isset($_GET['token'])) ? $_GET['token'] : '';

if (empty($token)) { // Please ignore this terrbile code for now
    readfile('index.html');
    exit;
}

header('Content-Type: application/json');

require '../src/bootstrap.php';
use WeightLog\WeightLog;
use WeightLog\Db;

$db = Db::getInstance();
$weightLog = new WeightLog($db);

die(json_encode($weightLog->getAllWeightsByToken($token)));
