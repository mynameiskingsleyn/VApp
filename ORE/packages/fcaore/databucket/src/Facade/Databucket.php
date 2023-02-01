<?php namespace Fcaore\Databucket\Facade;
 
use Illuminate\Support\Facades\Facade;
 
class Databucket extends Facade {
 
    protected static function getFacadeAccessor() { return 'databucket'; }
 
}