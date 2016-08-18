<?php
declare(strict_types=1);

namespace HttpClient;

/**
 * Class Response
 * @package HttpClient
 */
class Response
{
    /** @var array|string */
    private $body;
    /** @var bool  */
    private $complete;
    /** @var string|null */
    private $contentType;
    /** @var array */
    private $headers;
    /** @var int */
    private $headersCount;
    /** @var int */
    private $responseCode;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        $this->complete =   false;
        $this->headers  =   [];
        $this->headersCount =   0;
        $this->responseCode =   0;
    }

    /**
     * @param $body
     * @param string $type
     * @return bool
     * @throws \HttpClientException
     */
    public function write($body, string $type) : bool
    {
        if($this->complete) {
            return false;
        }

        if(empty($this->responseCode)) {
            throw new \HttpClientException(
                'Request received an unexpected HTTP response code "%1$d"',
                $this->responseCode
            );
        }

        if($type    === "application/json") {
            $body   =   json_decode($body, true);
            if(!is_array($body)) {
                throw new \HttpClientException('Failed to parse JSON response body');
            }
        }

        $this->body =   $body;
        $this->contentType  =   $type;
        $this->complete =   true;
        return true;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function contentType() : string
    {
        return $this->contentType;
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function responseCode() : int
    {
        return $this->responseCode;
    }

    /**
     * @param string $header
     * @return bool
     * @throws \HttpClientException
     */
    public function writeHeader(string $header) : bool
    {
        if($this->complete) { // Request was completed
            return false;
        }

        $line   =   $this->headersCount;
        $this->headersCount++;

        $header =   trim($header);
        if($line    === 0) {
            $header =   explode(" ", $header);
            $responseCode   =   intval($header[1]);
            $this->responseCode =   $responseCode;
        } else {
            // Write header
            if(preg_match('/^[a-z0-9\-]+\:\s.*$/i', $header)) {
                $header =   preg_split('/:/', $header, 2);
                $this->headers[strtolower($header[0])]  =   trim($header[1]);
            }
        }

        return false;
    }

    /**
     * Get all headers in an indexed Array
     *
     * @return array
     */
    public function getAllHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Retrieve a header value with given key (case-insensitive)
     * If header/value is not found, an empty string ("") will be returned
     *
     * @param string $key
     * @return string
     */
    public function getHeader(string $key) : string
    {
        return $this->headers[strtolower($key)] ?? "";
    }

    /**
     * Get response body
     * @return array|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Purpose of this function is to use PHP return type declaration to ensure expected body is Array
     *
     * @return array
     */
    public function getBodyArray() : array
    {
        return $this->body;
    }

    /**
     * Purpose of this function is to use PHP return type declaration to ensure expected body is String
     *
     * @return string
     */
    public function getBodyString() : string
    {
        return $this->body;
    }
}