<?php

namespace App\Service;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    private string $logName = "";

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $currentTime = new DateTime();
        $currentTimeFormated = $currentTime->format("Y-m-d h:i:s");

        $messageToWrite = PHP_EOL
            . str_repeat("-", 150)
            . PHP_EOL
            . "[{$currentTimeFormated}]: [{$level}]: "
            . $message;

        $filePath = __DIR__ . "/../../log/" . $currentTime->format("Y-m-d-") . $this->getLogName() . ".log";

        file_put_contents(
            $filePath,
            $messageToWrite,
            FILE_APPEND
        );
    }

    public function logException(Exception $exception, $level = LogLevel::CRITICAL)
    {
        $logMessage = json_encode([
            "line" => $exception->getLine(),
            "file" => $exception->getFile(),
            "message" => $exception->getMessage(),
            "trace" => explode(PHP_EOL, $exception->getTraceAsString()),
        ], JSON_PRETTY_PRINT);

        $this->log($level, $logMessage);
    }

    /**
     * Get the value of logName
     */
    public function getLogName(): string
    {
        return $this->logName;
    }

    /**
     * Set the value of logName
     *
     * @return  self
     */
    public function setLogName(string $logName)
    {
        $this->logName = $logName;

        return $this;
    }
}
