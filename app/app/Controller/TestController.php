<?php

declare(strict_types=1);

namespace App\Controller;

use App\Amqp\Producer\DemoProducer;
use App\Model\User;
use Elasticsearch\Client;
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
    private Client $client;

    public function __construct(Producer $producer, Redis $redis, Client $client)
    {
        $this->producer = $producer;
        $this->redis = $redis;
        $this->client = $client;
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

        $params = [
            'index' => 'my_index',
            'id'    => time(),
            'body'  => ['testField' => 'abc']
        ];

        $this->client->index($params);

        // Retrieve the id parameter from the request
        $id = $request->input('id', 1);
        return (string) $id;
    }
}