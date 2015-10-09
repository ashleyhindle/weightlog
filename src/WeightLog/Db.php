<?php
namespace WeightLog;

use Aura\Sql\ExtendedPdo;

class Db
{
    public static function getInstance()
    {
        $basepath = $basepath = realpath(__DIR__ . '/../../');

        $db = new ExtendedPdo('sqlite:' . $basepath . 'weights.db');
        $db->exec("CREATE TABLE IF NOT EXISTS persons (id INTEGER PRIMARY KEY, timeadded INTEGER, currentweight REAL, telegramid INTEGER, firstname TEXT, username TEXT, token TEXT)");
        $db->exec("CREATE TABLE IF NOT EXISTS weights (id INTEGER PRIMARY KEY, timestamp INTEGER, weight REAL, personid INTEGER)");
        
        return $db;
    }
}
