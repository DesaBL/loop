<?php

namespace App\Exceptions;

use App\Models\Log;
use Exception;
use Throwable;

class ImportDataFailed extends Exception
{
    private array $details;

    public function __construct(array $details, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->details = $details;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        Log::query()->create([
            'message' => $this->message,
            'details' => json_encode($this->details),
            'is_critical' => true,
        ]);
    }
}
