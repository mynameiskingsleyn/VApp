<?php
 /**
 * @package Data Bucket
 * Whoops - php errors If Custom Exception
 * @author Sathishkumar <sathisha@v2soft.com>
 */
namespace Fcaore\Databucket\Exceptions;
 
use Exception;
 
class CustomException extends Exception
{
 
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->view(
                'errors.custom',
                array(
                    'exception' => $this
                )
        );
    }
}