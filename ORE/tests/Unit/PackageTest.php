<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Fcaore\Databucket\Http\cronController as CC;

class PackageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
	
	 /**
     * Flush all Cache By Key
     *
     * @return boolean
     */
    public function testPackageByFlushKey(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('POST', 'Fcaore\Databucket\Http\cronController@flushkey', ['key' => 'new2019155']);  		 
		 $this->assertEquals(404, $response->getStatusCode()); 
    }
	
	/**
     * Flush all Cache By Key
     *
     * @return boolean
     */
	 public function testPackageByViewKeys(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', 'Fcaore\Databucket\Http\cronController@ViewKeys'); 
		 $this->assertSame(gettype($response), 'object'); 
    }
	
	/**
     * Get All Keys
     *
     * @return boolean
     */
	public function testPackageByViewKeysCode(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', 'Fcaore\Databucket\Http\cronController@ViewKeys'); 
		 $this->assertEquals(404, $response->getStatusCode()); 
    }
	
	/**
     * Package and Options By Fiat
     *
     * @return boolean
     */
	public function testPackagePOByFiat(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', '\package_and_options\new\fiat'); 
		$this->assertEquals(404, $response->getStatusCode());  
    }
	
	/**
     * Package and Options By Dodge
     *
     * @return boolean
     */
	public function testPackagePOByDodge(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', '\package_and_options\new\dodge'); 
		$this->assertEquals(404, $response->getStatusCode());  
    }
	
	/**
     * Package and Options By Ram
     *
     * @return boolean
     */
	public function testPackagePOByRam(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', '\package_and_options\new\ram'); 
		$this->assertEquals(404, $response->getStatusCode());  
    }
	
	/**
     * Package and Options By Jeep
     *
     * @return boolean
     */
	public function testPackagePOByJeep(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', '\package_and_options\new\jeep'); 
		$this->assertEquals(404, $response->getStatusCode());  
    }
	
	/**
     * Package and Options By Chrysler
     *
     * @return boolean
     */
	public function testPackagePOByChrysler(){ 
		$cronController = new \Fcaore\Databucket\Http\cronController();
		$response = $this->call('GET', '\package_and_options\new\chrysler'); 
		$this->assertEquals(404, $response->getStatusCode());  
    }
}
