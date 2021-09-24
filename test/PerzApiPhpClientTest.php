<?php

namespace Acquia\PerzApiPhp\test;

use Acquia\PerzApiPhp\PerzApiPhpClient;
use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\Key;
use Drupal\Component\Serialization\Json;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;


class PerzApiPhpClientTest extends TestCase {

  /**
   * Guzzle client.
   *
   * @var \GuzzleHttp\Client|\Mockery\MockInterface
   */
  private $guzzle_client; // phpcs:ignore

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    parent::tearDown();
  }

  public function testGetClient() {

    $key_id = 'QRWX-204485';
    $key_secret = '4dfac8a81d5d1a7a491206bc0a61d51e';
    $key = new Key($key_id, $key_secret);
//    $headers = [
//      'X-Custom-1' => 'value1',
//      'X-Custom-2' => 'value2',
//    ];
    $realm = 'Acquia';
    $middleware = new HmacAuthMiddleware($key, $realm);

    $client = new PerzApiPhpClient($middleware);
    $response =  $client->pushEntity(
      'post',
      'http://acquia-perz.ddev.site/api/entity-save-endpoint',
      'node',
      '6bcc1820-5171-43fd-ad61-39b4ce078406'
    );
//    var_dump($response);

    var_dump($response->getHeader('X-Server-Authorization-HMAC-SHA256'));//    return $client;
  }

}
