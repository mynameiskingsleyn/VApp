<?php

namespace Tests\Unit;

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
    public function testMyPath()
    {
        $this->assertTrue(true);
    }

    /**
     * @return Summary Query Success
     */
    public function testSummarySuccess() {
        $array = count(Databucket::sniSummaryQuery());
        $this->assertGreaterThanOrEqual(1, $array);
    } 

     

     /**
     * @return Summary Query Performance
     */
    public function testSummaryQueryPerformance() {
        $maxTime = 100;
        $tStart = microtime( true );
        //Databucket::sniSummaryQuery();
        $tDiff = microtime( true ) - $tStart;
        $this->assertLessThan( $maxTime, $tDiff, 'Took too long' );
    }


     /**
     * @return Summary Query Success
     */
    public function testsniSegregateSummary() {
        $params_vechType="New"; $params_year="2017"; $params_subcatid="2";
        $array = count(Databucket::sniSegregateSummaryQuery($params_vechType, $params_year, $params_subcatid));
        $this->assertGreaterThanOrEqual(1, $array);
    } 

     

     /**
     * @return Summary Query Performance
     */
    public function testsniSegregateSummaryPerformance() {
        $maxTime = 100;
        $params_vechType="New"; $params_year="2018"; $params_subcatid="248";

        $tStart = microtime( true );
        Databucket::sniSegregateSummaryQuery($params_vechType, $params_year, $params_subcatid);
        $tDiff = microtime( true ) - $tStart;
        $this->assertLessThan( $maxTime, $tDiff, 'Took too long' );
    } 

    /**
     * Data bucket Cache 
     *
     * @return boolean
     */
   public function testCache(){
            $md5 = md5("1C4RJFBG1JC332479");
            $cache = Databucket::isCacheExists($md5);
            $this->assertTrue(true);
    }
     
}
