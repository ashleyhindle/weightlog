<?php
$token = getenv('TELEGRAM_BOT_TOKEN');
if (empty($token)) {
	die('No token');
}

require 'vendor/autoload.php';
use Telegram\Bot\Api;
use Aura\Sql\ExtendedPdo;
use WeightLog\WeightLog;

$db = new ExtendedPdo('sqlite:weights.db');
$db->exec("CREATE TABLE IF NOT EXISTS persons (id INTEGER PRIMARY KEY, timeadded INTEGER, currentweight REAL, telegramid INTEGER, firstname TEXT, username TEXT, token TEXT)");
$db->exec("CREATE TABLE IF NOT EXISTS weights (id INTEGER PRIMARY KEY, timestamp INTEGER, weight REAL, personid INTEGER)");
$insertQuery = "INSERT INTO weights (timestamp, weight, personid) VALUES (?, ?, ?)";


$telegram = new Api($token);
$weightLog = new WeightLog($db);

$run = true;
$lastUpdateId = 0;

while ($run) {
	sleep(1);
	$updates = $telegram->getUpdates($lastUpdateId+1, 100, 3);
	if (empty($updates)) {
		continue;
	}

	foreach ($updates as $update) {
		$lastUpdateId = $update['update_id'];
		$weightGiven = preg_replace('/[^0-9\.]/', '', $update['message']['text']);
		$person = [
			'telegramid' => $update['message']['from']['id'],
			'firstname' => $update['message']['from']['first_name'],
			'username' => (isset($update['message']['from']['username'])) ? $update['message']['from']['username'] : 'Unknown',
		];

		// This will insert them if they're not there
		// Person will be an array with id, telegramid, firstname, username, currentweight, token
		$person = $weightLog->getPerson($person['telegramid'], $person['firstname'], $person['username']);
		$lastWeight = $person['currentweight'];

		if (empty($weightGiven)) {
			$message = "Huh? I'm afraid that wasn't a valid weight :(";
		} else {
			$result = $db->perform($insertQuery, [
				time(),
				$weightGiven,
				$person['id'],
			]);

			$weightLog->setCurrentWeight($person, $weightGiven);
			if (!empty($lastWeight) && $lastWeight > $weightGiven) {
				$diff = round($lastWeight - $weightGiven, 1);
				$message = "That's been logged for you.  You've lost {$diff} lbs or kgs since last time, that's awesome";
			} else {
				$message = "That's been logged for you.  Keep on keeping on!";
			}
		}

		$message .= "\nYou can view all of your logs here: http://weightlog.ashleyhindle.com/?token=" . urlencode($person['token']);
		$telegram->sendMessage($update['message']['chat']['id'], $message);
	}
}
