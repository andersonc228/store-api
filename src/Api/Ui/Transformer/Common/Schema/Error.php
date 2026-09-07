<?php

namespace App\Api\Ui\Transformer\Common\Schema;

readonly class Error extends ValidationError
{
    public function __construct(string $type, string $title, string $detail, string $instance)
    {
        parent::__construct($type, $title, $detail, $instance, null);
    }

    public static function internal(): self
    {
        return new self(
            'INTERNAL',
            'Internal Server Error',
            'An internal server error occurred. Try again later.',
            'internal',
        );
    }

    public static function payloadTooLarge(int $maxBytes): self
    {
        $maxMb = round($maxBytes / 1024 / 1024, 1);

        return new self(
            'PAYLOAD_TOO_LARGE',
            'Payload Too Large',
            sprintf('Request body must not exceed %s MB.', $maxMb),
            'request',
        );
    }
}