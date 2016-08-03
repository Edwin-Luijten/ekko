<?php

namespace EdwinLuijten\Ekko\Broadcasters;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogBroadcaster implements BroadcasterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogBroadcaster constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->logger = new Logger($config['name']);
        $this->configureHandlers($config['handlers']);
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

    /**
     * @param $handlers
     */
    private function configureHandlers($handlers)
    {
        foreach ($handlers as $handler) {
            $class           = new \ReflectionClass($handler['class']);
            $handlerInstance = $class->newInstanceArgs($handler['arguments']);

            $this->logger->pushHandler($handlerInstance);
        }
    }
}
