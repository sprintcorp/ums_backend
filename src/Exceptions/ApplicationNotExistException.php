<?php


namespace App\Exceptions;


use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class ApplicationNotExistException
 *
 * Custom exception to be thrown when the AppController cannot find an application
 * via the AuthenticatedApplicationTrait.
 *
 * @package App\Exceptions
 * @author @dev1 -> Ore Richard
 */
class ApplicationNotExistException extends Exception
{
}