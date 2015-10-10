<?php
namespace WeightLog\TelegramCommands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use WeightLog\WeightLog;
use WeightLog\Db;

class ApiCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "api";

    /**
     * @var string Command Description
     */
    protected $description = "Returns the URL to your JSON API";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $weightLog = new WeightLog(Db::getInstance());
        $person = $weightLog->getPersonFromUpdate($this->getUpdate());
        $this->replyWithMessage("http://weightlog.ashleyhindle.com/?token={$person['token']}");
    }
}
