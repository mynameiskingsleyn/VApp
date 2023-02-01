<?php

namespace App\Helpers;

class FilterHelper
{
    /**
     * @param $result_array
     * @param string $extractKey
     * @param string $extractValue
     * @return mixed
     * Function extract keys to value from group when grouped by key.
     */
    public function extractTrimsFromCodeList($result_array,$extractKey='trim_code',$extractValue='trim_desc')
    {
        if(is_array($result_array) && !empty($result_array))
        {
            $grouped = [];
           foreach($result_array as $current){
               $focusItem = array_shift($current);
               if(!empty($focusItem) and is_array($focusItem)){
                   $key = $focusItem[$extractKey];
                   $value = $focusItem[$extractValue];
                   $grouped[$key] = $value;
               }
           }
           return array_unique($grouped);
        }

        return $result_array;
    }

}
