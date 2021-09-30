<?php

namespace Acquia\PerzApiPhp;

use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use GuzzleHttp\Client;

/**
 * Class PerzApiPhpClient.
 *
 * @package Acquia\PerzApiPhp
 */
class PerzApiPhpClient extends Client {

  /**
   * {@inheritdoc}
   */
  protected $baseUrl;

  /**
   * {@inheritdoc}
   */
  protected $environment;

  /**
   * {@inheritdoc}
   */
  protected $origin;

  /**
   * {@inheritdoc}
   */
  public function __construct(HmacAuthMiddleware $middleware, array $config = []) {
    if (isset($config['base_url'])) {
      $this->baseUrl = $config['base_url'];
    }

    if (isset($config['environment'])) {
      $this->environment = $config['environment'];
    }

    if (isset($config['origin'])) {
      $this->origin = $config['origin'];
    }

    if (!isset($config['handler'])) {
      $config['handler'] = ObjectFactory::getHandlerStack();
    }
    $config['handler']->push($middleware);
    parent::__construct($config);
  }

  /**
   *
   */
  public function __call($method, $args) {
    parent::__call($method, $args);
  }

  /**
   * @param $method
   * @param $url
   * @param $entity_type
   * @param $entity_id
   * @return \Psr\Http\Message\ResponseInterface|void
   * @throws \Exception
   */
  public function pushEntity($method, $url, $entity_type, $entity_id) {
    try {
      return $this->request(
        $method,
        $url, [
          'headers' => $this->getDefaultRequestHeaders(),
          'body' => json_encode([
            'entity_type_id' => $entity_type,
            'entity_uuid' => $entity_id,
          ]),
        ]
      );
    }
    catch (\Exception $exception) {
      ObjectFactory::exceptionHandler($exception);
    }
  }

  /**
   * @param $method
   * @param $url
   * @param $data
   * @return \Psr\Http\Message\ResponseInterface|void
   * @throws \Exception
   */
  public function pushEntities($method, $url, $data) {
    try {
      return $this->request(
        $method,
        $url, [
          'headers' => $this->getDefaultRequestHeaders(),
          'body' => json_encode($data),
        ]
      );
    }
    catch (\Exception $exception) {
      ObjectFactory::exceptionHandler($exception);
    }
  }


  /**
   * {@inheritdoc}
   */
  protected function getDefaultRequestHeaders() {
    return [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];
  }

}
