<?php

require __DIR__."/../vendor/autoload.php";

use Schema31\CouchDBWatcher\Watcher;

$watcher = new Watcher('https://couch.com/db');

$watcher->addCallback(function($change) {
   echo $change->id.PHP_EOL;
});

$watcher->run();
