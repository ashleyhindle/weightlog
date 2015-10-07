<?php
header('Content-Type: application/json');
$token = (isset($_GET['token'])) ? $_GET['token'] : '';
if (empty($token)) {
	die(json_encode([
		'error' => 'Empty token'
	]));
}

require '../vendor/autoload.php';
use Aura\Sql\ExtendedPdo;
use WeightLog\WeightLog;

$db = new ExtendedPdo('sqlite:../weights.db');
$weightLog = new WeightLog($db);

die(json_encode($weightLog->getAllWeightsByToken($token)));
