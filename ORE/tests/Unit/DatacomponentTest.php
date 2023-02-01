<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Facades\OreDSClass;

class DatacomponentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function url()
    {
        $this->assertTrue(true);
    }

    public function getSkeleton(){
       $vin = \Ore::vehicleDataComponent('1C4RJFBG1JC332479'); 
       $this->assertEmpty($vin); 
    }

    public function testFailingInclude()
    { 
    }
}
