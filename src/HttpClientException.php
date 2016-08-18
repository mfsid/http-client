<?php
declare(strict_types=1);

/**
 * Class HttpClientException
 */
class HttpClientException extends Exception
{
    /**
     * @return string
     */
    public function htmlMessage() : string
    {
        return htmlentities($this->message);
    }
}