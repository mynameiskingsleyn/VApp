<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

trait CacheTrait
{
	 private $array, $tmp_key_chunk;
	 
	 private $cache_dealer_admin_prefix = "da:"; // DelaerAdmin
	 
	public function CacheFlushAll(){
		Redis::flushall();
	}
	
	 public function isCacheExists($rKey){
		if(Redis::exists($this->cache_dealer_admin_prefix.$rKey)) return true; else return false;
	}

    public function isCacheSet($key, $value){
        Redis::set($this->cache_dealer_admin_prefix.$key, $value); 
        return true;	 
	} 
	
	public function bulk_delete($key){
		$this->$tmp_key_chunk = $key;
		
		 Redis::pipeline(function ($pipe) {
                 foreach (Redis::keys($this->$tmp_key_chunk.'*') as $key) {
                     $pipe->del($key);
                 }
           });
	}
	/************ HM KEYS *******************/
	
			public function isCacheHMSet($key, $name, $value){    
				Redis::hmset($this->cache_dealer_admin_prefix.$key, $name,$value);
				return true;	 
			}
			
			public function isCacheHMGet($key, $name){  
				return Redis::hmget($this->cache_dealer_admin_prefix.$key, $name); 
			}   
			
			public function isCacheGetAll($key){ 
				return Redis::hgetall($this->cache_dealer_admin_prefix.$key); 
			}
			
			public function isCacheHvals($key){ 
				return Redis::hvals($this->cache_dealer_admin_prefix.$key); 
			}
			
			public function isCacheHKeys($key){ 
				return Redis::hvals($this->cache_dealer_admin_prefix.$key); 
			}
			
			public function hexists($key, $field){   
				return Redis::hexists($this->cache_dealer_admin_prefix.$key, $field); 
			}
	
 
}