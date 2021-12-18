<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\DemoProducer;
use App\Model\User;
use Hyperf\Amqp\Producer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;

/**
 * @Controller()
 */
class TestController extends AbstractController
{
    private Producer $producer;
    private Redis $redis;

    public function __construct(Producer $producer, Redis $redis)
    {
        $this->producer = $producer;
        $this->redis = $redis;
    }

    /**
     * Hyperf will automatically generate a `/index/index` route for this method, allowing GET requests
     * @RequestMapping(path="index", methods="get")
     */
    public function index(RequestInterface $request)
    {
        $message = new DemoProducer('AMQP Message From Controller');
        $this->producer->produce($message);

        $this->redis->set('test', 42);

        User::create(['name' => time()])->save();

        // Retrieve the id parameter from the request
        $id = $request->input('id', 1);
        return (string) $id;
    }
}