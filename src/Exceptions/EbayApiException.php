<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Exceptions;

use Exception;

/**
 * Base exception for all eBay API errors
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/Reference/ebay/Errors/errormessages.htm
 * @link https://developer.ebay.com/devzone/xml/docs/Reference/ebay/Errors/errorsbycall.html
 */
class EbayApiException extends Exception
{
    protected string $errorCode;
    protected ?string $originalResponse;
    protected array $errors = [];

    public function __construct(
        string $message = '',
        string $errorCode = '',
        ?string $originalResponse = null,
        array $errors = [],
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->originalResponse = $originalResponse;
        $this->errors = $errors;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getOriginalResponse(): ?string
    {
        return $this->originalResponse;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode,
            'errors' => $this->errors,
            'code' => $this->getCode(),
        ];
    }
}
