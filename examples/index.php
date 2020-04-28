<?php

require __DIR__."/../vendor/autoload.php";

use Schema31\CouchDBWatcher\Watcher;

$options = [
  'since' => 'now',  // taken into account only if store is empty, default is '0'
  'store' => new Schema31\CouchDBWatcher\Store\FileStore() // FileStore is the default one. You can provide your own object, that implements the  StoreInterface
];

$watcher = new Watcher('main_db', 'https://couch.com/db', $options);

$watcher->addCallback(function($change) {
   echo $change->id.PHP_EOL;
});

$watcher->run();
