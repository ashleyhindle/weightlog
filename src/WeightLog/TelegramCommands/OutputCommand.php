<?php
namespace WeightLog\TelegramCommands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use WeightLog\WeightLog;
use WeightLog\Db;

use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;

class OutputCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "output";

    /**
     * @var string Command Description
     */
    protected $description = "Output your weight history in a nice easy to read format, with a lovely line chart";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $limit = 30;
        $update = $this->getUpdate();
        $weightLog = new WeightLog(Db::getInstance());
        $person = $weightLog->getPersonFromUpdate($update);
        $chartWeights = [];
        $weights = array_slice($weightLog->getAllWeightsByToken($person['token']), 0 - $limit);

        $chxl = [];
        $output = "Last {$limit} logs\nDate - Weight\n-----------------\n";

        foreach ($weights as $index => $weight) {
            $chxl[] = (($index % 5) === 0) ? date('d/m', $weight['timestamp']) : '';
            $chartWeights[] = $weight['weight'];
            $output .= date('jS \of M', $weight['timestamp']) . ' - ' . $weight['weight'] . 'lbs/kgs' . "\n";
        }

        $chartFile = sys_get_temp_dir() . '/weightlog-chart-' . $person['id'] . '.png';
        $url = "https://chart.googleapis.com/chart?chxt=y,x&chxl=1:|" . urlencode(implode('|', $chxl)) . "|&chs=900x280&chxr=0," . (min($chartWeights)-3) . "," . (max($chartWeights)+5) . "&chds=" . (min($chartWeights)-3) . "," . (max($chartWeights)+5) . "&cht=lc&chco=0077CC&chd=t:" . implode(',', $chartWeights);
        
        echo $url . PHP_EOL;

        file_put_contents(
            $chartFile,
            file_get_contents(
                $url
            )
        );

        $this->replyWithMessage($output);

        if (file_exists($chartFile)) {
            $this->getTelegram()->sendPhoto($update['message']['chat']['id'], $chartFile, 'Your Weight');
            unlink($chartFile);
        }
/*
        // This will update the chat status to typing...
        $this->replyWithChatAction(Actions::TYPING);

        // This will prepare a list of available commands and send the user.
        // First, Get an array of all registered commands
        // They'll be in 'command-name' => 'Command Handler Class' format.
        $commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        // Reply with the commands list
        $this->replyWithMessage($response);

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        $this->triggerCommand('subscribe');
        */
    }
}
