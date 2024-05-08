<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class FailedAction extends Exception
{
    private $statusCode;

    public function __construct($message = '', $statusCode = 400)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function report()
    {
    }

    public function render()
    {
        Log::channel('failedActions')->warning($this->message, ['status' => $this->statusCode]);

        if (request()->header('content-type') == "application/json")
            return response(['message' => $this->message], $this->statusCode);
        abort($this->statusCode, $this->message);
    }
}
