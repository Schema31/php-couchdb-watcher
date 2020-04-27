<?php

namespace Schema31\CouchDBWatcher;

/**
 * Description of Watcher
 *
 * @author Antonio Turdo <aturdo@schema31.it>
 */
class Watcher {
    private $couchDBUri;
    private $store;
    private $callables;
    private $options;
    private $storeKey;
    
    public function __construct(string $storeKey, string $couchDBUri, array $options = []) {
        $allowedOptions = ["since", "store"];
        $optionsDiff = array_diff(array_keys($options), $allowedOptions);
        if (count($optionsDiff) > 0) {
            throw new \Exception("Only these options are allowed: ". implode(",", $allowedOptions));
        }
        
        $this->couchDBUri = $couchDBUri;
        $this->options = $options;
        $this->store = null;
        $this->callables = [];
        $this->storeKey = $storeKey;
        
        if (isset($this->options["store"])) {
            if (!($this->options["store"] instanceof StoreInterface)) {
                throw new \Exception("Store option must be an object that implements StoreInterface");
            }
            
            $this->store = $this->options["store"];
        } else {
            $this->store = new Store\FileStore();
        }
        
    }
    
    public function addCallback(callable $callable) {
        $callableDefinition = new \stdClass();
        $callableDefinition->callable = $callable;
        
        $this->callables[] = $callableDefinition;
    }
      
    public function run() {   
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });   
        
        if (count($this->callables) === 0) {
            throw new \Exception("No callback defined");
        }
        
        $query = [
            'since' => $this->store->get($this->storeKey) ?? $this->options["since"] ?? "0",
            'include_docs' => 'true',
            'feed' => 'continuous',
            'heartbeat' => 15000
        ];
        
        $completeUri = $this->couchDBUri . "/_changes?" . http_build_query($query);
        
        $fp = fopen($completeUri, 'r');
        
        if ($fp) {
            while($line = fgets($fp)) {
                if ($line === PHP_EOL){
                    continue;
                }

                $change = json_decode(trim($line));
                
                if (is_null($change)) {
                    throw new \Exception("Impossible to decode change json. Error: ". json_last_error_msg());
                }
                
                // last output doesn't have a seq
                if (!isset($change->seq)) {
                    continue;
                }                
                
                $this->handleChange($change);
            }
            
            fclose($fp);
        }   
    }
    
    private function handleChange(\stdClass $change) {    
        // call all the registerd callables
        foreach ($this->callables as $callable) {
            $realCallable = $callable->callable;
            $realCallable($change);
        }
        
        // mark the change as processed saving the seq
        if (isset($change->seq)) {
            $this->store->set($this->storeKey, $change->seq);
        }         
    }
}
