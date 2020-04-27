<?php

require __DIR__."/../vendor/autoload.php";

use Schema31\CouchDBWatcher\Watcher;

$options = [
  'since' => 'now',  // taken into account only if store is empty
  'store' => new Schema31\CouchDBWatcher\Store\FileStore() // default store
];

$watcher = new Watcher('main_db', 'https://couch.com/db', $options);

$watcher->addCallback(function($change) {
   echo $change->id.PHP_EOL;
});

$watcher->run();
