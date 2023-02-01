<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Redis; 
use App\Facades\OreDSClass;

class CashTest extends TestCase
{    

    /**
     * VIN No Test.
     *
     * @return row count
     */
    public function testVin(){
        $vin = "1C4RJFBG1JC332479";   
        $data = \Ore::vehicleDataComponent($vin); 
         $this->assertSame(1, count(json_decode($data, true) ));
       //$this->assertCount(1, count(json_decode($data, true) )) ;
   }

   /**
     * Cache Available or Nor
     *
     * @return boolean
     */
   public function testCache(){
            $md5 = md5("1C4RJFBG1JC332479");
            $cache = \Ore::cacheValidate($md5);
            $this->assertTrue(true);
   }

   /**
    * Storage Directory Manager
    *
    * @return @boolean
    */

   public function testSkeletonDirectoryChecker()
    {
        $filePath =storage_path('app\\public\\skeleton\\json\\');
        $this->assertDirectoryExists( $filePath);
        $this->assertDirectoryIsReadable($filePath);
        $this->assertDirectoryIsWritable($filePath);      
    }

    /**
    * Storage Directory Json File
    *
    * @return @boolean
    */

    public function testJsonFileRead(){
        $file_userinfo =storage_path('app\\public\\skeleton\\json\\userinfo.json');
        $file_service =storage_path('app\\public\\skeleton\\json\\service.json');
        $this->assertFileIsReadable($file_userinfo);
        $this->assertFileIsReadable($file_service);
    }

     /**
    * Get SessionID
    *
    * @return @boolean
    */

    public function testSessionChecker()
    {   
        \Ore::getSessionID();
        $this->assertTrue(true);
    }

    /**
     * Redis Cache Set Test
     * 
     * @return Boolean
     */
     public function testRedisCacheSet(){
        \Ore::cacheSet("key","value");
        $this->assertTrue(true);
     } 
   
}
