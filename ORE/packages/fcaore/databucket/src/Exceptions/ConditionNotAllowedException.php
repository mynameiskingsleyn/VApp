<?php
/**
 * @package Data Bucket
 * Whoops - php errors If condition not allowed
 * @author Sathishkumar <sathisha@v2soft.com>
 */
namespace Fcaore\Databucket\Exceptions;

class ConditionNotAllowedException extends \Exception
{
    public function __construct($message = "Condition not allowed exception", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
