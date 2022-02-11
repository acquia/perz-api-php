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

    $q = '{"query":"query getEntityVariations($entity_type_id: String!, $entity_uuid: String!) {\\n    entity_variations(entity_type_id: $entity_type_id, entity_uuid: $entity_uuid) {\\n      content_uuid,\\n      label,\\n      content_type,\\n      view_mode,\\n      language,\\n      updated,\\n      rendered_data,\\n      relations {\\n        field,\\n        terms\\n      }\\n    }\\n  }","variables":{"entity_type_id": "node","entity_uuid": "16df7063-96e6-4b2c-82cb-a93a9f05a5ba"}}';
    $d=  '{"query":"  query discoverEntities($page: Int! = 0) {\n    discover_entities(page: $page) {\n      page_info {\n        total_count\n        current_page_count\n        current_page\n        next_page\n        prev_page\n      }\n      items {\n        entity_type_id\n        entity_uuid\n      }\n    }\n  }","variables":{"page":0}}';
    $client = new PerzApiPhpClient($middleware, ['base_url' => 'http://acquiaperz.ddev.site']);
//    $options = [
//        'account_id' => 'QRWX-204485',
//        'origin' => '37t0s1',
//        'environment' => 'dev',
//        'uuid' => 'a57c108d-757c-46e8-9c5e-ff768982b337'
//    ];
    $response =  $client->graphql($d);
    var_dump($response->getBody()->getContents());

  }

}
