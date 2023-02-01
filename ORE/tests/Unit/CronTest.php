<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;  
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Fcaore\Databucket\Http\cronController as Cron; 

class CronTest extends TestCase
{ 
    use WithoutMiddleware;
    public function testIndex()
{
    $response = $this->call('GET', 'posts');
    
    $response->assertStatus(404); 
}

# app/tests/TestCase.php
 
public function __call($method, $args)
{
    if (in_array($method, ['get', 'post', 'put', 'patch', 'delete']))
    {
        return $this->call($method, $args[0]);
    }
 
    throw new BadMethodCallException;
}
    /**
     * @return testRouter
     */
    public function testRouter() {
        $response = $this->call('GET', '/', [], [], [], ['HTTP_HOST' => env('APP_DOMAIN')]);
        $this->assertEquals(404, $response->getStatusCode());
        
    } 

    /**
     * @return testRouter
     */
    public function testSummary() {
        $response = $this->get('/summaryQuery'); 
        $response->assertStatus(404); 
    } 

    public function testZipcodeCache(){
        $zipcode = "48302";   
        $md5_zipcode = md5($zipcode);
        $data = \Databucket::makeCache($zipcode); 
         $this->assertSame($data, $md5_zipcode); 
   }


     

     
 
}
