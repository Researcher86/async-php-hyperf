<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Elasticsearch\Client;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Psr\Container\ContainerInterface;

return [
    Client::class => static function (ContainerInterface $container) {
        $clientBuilder = $container->get(ClientBuilderFactory::class)->create();
        $client = $clientBuilder->setHosts([
            env('ELASTICSEARCH_HOST', 'http://es:9200')
        ])->build();

        return $client;
    }
];
