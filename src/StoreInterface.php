<?php

namespace Schema31\CouchDBWatcher;

/**
 *
 * @author Antonio Turdo <aturdo@schema31.it>
 */
interface StoreInterface {
    
    public function set(string $key, string $seq): void;
    
    public function get(string $key): ?string;
}
