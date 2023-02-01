<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Fcaore\Databucket\Facade\Databucket; 

class DatabucketTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample(){
        $this->assertTrue(true);
    }
	
	/**
     * @return PHPUnit\DbUnit\Database\Connection
     */
    public function testConnection()
    {
        $pdo = new PDO('sqlite::memory:');
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }
	
	
	 
}
