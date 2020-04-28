# php-couchdb-watcher

This package provides a simple watcher to attach callbacks to CouchDB documents changes.

### Installation

The recommended installation method is by using [composer](https://getcomposer.org/)

    composer require schema31/php-couchdb-watcher

### Usage

Three steps are required in order to use the watcher.

Create it passing a key to save last change processed, CouchDB url and options.

```php
<?php

use Schema31\CouchDBWatcher\Watcher;

$options = [
  'since' => 'now',  // taken into account only if store is empty, default is '0'
  'store' => new Schema31\CouchDBWatcher\Store\FileStore() // FileStore is the default one. You can provide your own object, that implements the  StoreInterface
];

$watcher = new Watcher('main_db', 'https://couch.com/db', $options);
```

Then attach all the callbacks you want.

```php
<?php

$watcher->addCallback(function($change) {
   echo $change->id.PHP_EOL;
});
```
And just run it.

```php
<?php

$watcher->run();

```

### Error handling

If any of the defined callbacks produces an error program will exit, so you can fix it and then launch again.
The last change processed is saved in the store only if all the callbacks are executed without errors.
