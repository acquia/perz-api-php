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
    $graphQLQuery = <<<GQL
  query discoverEntities(\$page: Int! = 0) {
    discover_entities(page: \$page) {
      page_info {
        total_count
        current_page_count
        current_page
        next_page
        prev_page
      }
      items {
        entity_type_id
        entity_uuid
      }
    }
  }
GQL;

    $q = '{"query":"query getEntityVariations($entity_type_id: String!, $entity_uuid: String!) {\\n    entity_variations(entity_type_id: $entity_type_id, entity_uuid: $entity_uuid) {\\n      content_uuid,\\n      label,\\n      content_type,\\n      view_mode,\\n      language,\\n      updated,\\n      rendered_data,\\n      relations {\\n        field,\\n        terms\\n      }\\n    }\\n  }","variables":{"entity_type_id": "node","entity_uuid": "e549e7fc-1c15-4caa-b201-6f10abb06906"}}';
    $client = new PerzApiPhpClient(['base_url' => 'http://acquia-perz.ddev.site/'], $middleware);
    $response =  $client->graphql($q);
    var_dump($response->getBody()->getContents());

  }

}
