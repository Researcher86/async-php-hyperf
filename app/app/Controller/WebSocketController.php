<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\DemoProducer;
use Hyperf\Amqp\Producer;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Redis\Redis;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

class WebSocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    private Producer $producer;
    private Redis $redis;

    public function __construct(Producer $producer, Redis $redis)
    {
        $this->producer = $producer;
        $this->redis = $redis;
    }

    public function onMessage($server, Frame $frame): void
    {
        $message = new DemoProducer('AMQP Message From WebSocketController: ' . $frame->data);
        $this->producer->produce($message);

        $wsClientIds = $this->redis->hKeys('ws_client_ids');
        foreach ($wsClientIds as $wsClientId) {
            if ($frame->fd !== (int) $wsClientId) {
                $server->push((int) $wsClientId, $frame->data);
            }
        }
    }

    public function onOpen($server, Request $request): void
    {
        $this->redis->hSet('ws_client_ids', (string) $request->fd, '');

//        $server->push($request->fd, 'Opened');
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        $this->redis->hDel('ws_client_ids', (string) $fd);
    }
}