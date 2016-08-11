<?php

namespace EdwinLuijten\Ekko\Broadcast\Broadcasters;

use Psr\Log\LoggerInterface;

class LogBroadcaster implements BroadcasterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogBroadcaster constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Broadcast the given event.
     *
     * @param  array $channels
     * @param  string $event
     * @param  array $payload
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        $channels = implode(', ', $channels);

        $payload = json_encode($payload, JSON_PRETTY_PRINT);

        $this->logger->info(
            sprintf(
                'Broadcasting [%s] on channels [%s] with payload:' . PHP_EOL . $payload,
                $event,
                $channels
            )
        );
    }
}
