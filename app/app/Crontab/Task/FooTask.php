<?php

namespace App\Crontab\Task;

use App\Amqp\Producer\DemoProducer;
use Hyperf\Amqp\Producer;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\WebSocketClient\ClientFactory;
use Hyperf\WebSocketClient\Frame;
use Hyperf\WebSocketServer\Sender;

/**
 * @Crontab(name="Foo", rule="*\/5 * * * *", callback="execute", memo="This is an example scheduled task")
 */
class FooTask
{
    /**
     * @Inject()
     */
    private StdoutLoggerInterface $logger;

    /**
     * @Inject()
     */
    private Producer $producer;

    /**
     * @Inject()
     */
    protected ClientFactory $clientFactory;

    /**
     * @Inject()
     */
    protected Sender $sender;

    public function execute()
    {
        $this->logger->info(date('Y-m-d H:i:s', time()));
    }

    /**
     * @Crontab(rule="*\/5 * * * * *", memo="amqp")
     */
    public function sendAmqp()
    {
        $message = new DemoProducer('AMQP Message From CrontabTask');
        $this->producer->produce($message);
    }

    /**
     * @Crontab(rule="*\/10 * * * * *", memo="WebSocket")
     */
    public function sendWebSocket()
    {
        // The address of the peer service. If there is no prefix like ws:// or wss://, then the ws:// would be used as default.
        $host = '127.0.0.1:9502';

        // Create Client object through ClientFactory. Short-lived objects will be created.
        $client = $this->clientFactory->create($host);

        // Send a message to the WebSocket server
        $client->push('WebSocket Message From CrontabTask');
//
//        // Get a response from the server. The server should use 'push()' to send messages to fd of the client, only in this way, can the response be received.
//        // A Frame object is taken as an example in following with 2 seconds timeout.
//        /** @var Frame $msg */
//        $msg = $client->recv(2);
//
//        // Get text data: $res_msg->data
//        var_dump('Get text data from WebSocket: ' . $msg->data);

//        $this->sender->push()
    }
}