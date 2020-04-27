<?php

namespace Schema31\CouchDBWatcher\Store;

use Schema31\CouchDBWatcher\StoreInterface;

/**
 * Description of FileStore
 *
 * @author Antonio Turdo <aturdo@schema31.it>
 */
class FileStore implements StoreInterface {
    
    private $path;
    
    public function __construct(string $path = __DIR__) {
        if (!is_dir($path)) {
            throw new \Exception("$path is not a valid directory");
        }
        
        $this->path = $path;
    }


    public function get(string $key): ?string {
        $fullPath = $this->getFullPath($key);      
        
        $seq = file_get_contents($fullPath);
        
        if ($seq === FALSE) {
            throw new \Exception("Error reading seq from {$fullPath}");
        }
        
        return strlen($seq) > 0 ? $seq : null;
    }

    public function set(string $key, string $seq): void {
        $fullPath = $this->getFullPath($key); 
        
        if (file_put_contents($fullPath, $seq) === FALSE) {
            throw new \Exception("Error writing seq to {$fullPath}");
        }
    }
    
    private function getFullPath(string $key) {
        $fullPath = $this->path."/seq_".$key;
        
        if (!file_exists($fullPath)) {
            touch($fullPath);
        } 
        
        return $fullPath;
    }

}
