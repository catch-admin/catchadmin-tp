<?php
namespace app\admin\exceptions;

use Exception;
use think\exception\HttpException;

abstract class CatchException extends HttpException
{
    protected const HTTP_SUCCESS = 200;

    public function __construct(string $message = '', int $code = 0, Exception $previous = null, array $headers = [], $statusCode = 0)
    {
        parent::__construct($statusCode, $message ? : $this->getMessage(), $previous, $headers, $code);
    }

    public function getStatusCode(): int
    {
        return self::HTTP_SUCCESS;
    }


    public function getHeaders()
    {
        return config('catch.cross_headers');
    }
}
