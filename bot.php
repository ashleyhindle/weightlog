<?php
$token = getenv('TELEGRAM_BOT_TOKEN');
if (empty($token)) {
    die('No token');
}

require 'src/bootstrap.php';
use Telegram\Bot\Api;
use WeightLog\WeightLog;
use WeightLog\Db;

$db = Db::getInstance();
$telegram = new Api($token);
$weightLog = new WeightLog($db);

$command = new \WeightLog\TelegramCommands\OutputCommand();
$telegram->addCommand($command);

$command = new \WeightLog\TelegramCommands\HelpCommand();
$telegram->addCommand($command);

$run = true;
$lastUpdateId = 0;

while ($run) {
    sleep(1);

    $updates = $telegram->commandsHandler(false);

    if (empty($updates)) {
        continue;
    }

    foreach ($updates as $update) {
        $lastUpdateId = $update['update_id'];

        if (substr(trim($update['message']['text']), 0, 1) == '/') {
            continue;
        }

        $weightGiven = preg_replace('/[^0-9\.]/', '', $update['message']['text']);
        $person = $weightLog->getPersonFromUpdate($update);
        $lastWeight = $person['currentweight'];

        if (empty($weightGiven)) {
            $message = "Huh? I'm afraid that wasn't a valid weight :(";
        } else {
            $result = $db->perform("INSERT INTO weights (timestamp, weight, personid) VALUES (?, ?, ?)", [
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
