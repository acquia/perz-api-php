<?php

namespace Acquia\PerzApiPhp;

use GuzzleHttp\Client;
use Acquia\PerzApiPhp\PerzClientInterface;

/**
 * Class PerzApiPhpClient.
 *
 * @package Acquia\PerzApiPhp
 */
class PerzApiPhpClient extends Client implements PerzClientInterface {

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
  public function __construct(
    array $config = []
  ) {
    if (isset($config['base_url'])) {
      $this->baseUrl = $config['base_url'];
    }
    if (isset($config['environment'])) {
      $this->environment = $config['environment'];
    }
    if (isset($config['origin'])) {
      $this->origin = $config['origin'];
    }
    parent::__construct($config);
  }

  public function __call($method, $args) {
    parent::__call($method, $args);
  }

  /**
   * {@inheritdoc}
   * @throws \Exception
   */
  public function pushEntityById($id, $request_body = [], $settings = [], $request_headers = [], $method = 'PUT', $base_url = NULL, $environment = NULL, $origin = NULL) {
    if (!$base_url = $this->getGlobalValue($base_url, $this->baseUrl)) {
      throw new \Exception("Base url is not set");
    }
    if (!$environment = $this->getGlobalValue($environment, $this->environment)) {
      throw new \Exception("Environment is not set");
    }
    if (!$origin = $this->getGlobalValue($origin, $this->origin)) {
      throw new \Exception("Origin is not set");
    }
    $default_headers = $this->getDefaultRequestHeaders();
    $request_options = [
      'headers' => array_merge($default_headers, $request_headers),
    ];
    if (!empty($request_body)) {
      $request_options['body'] = json_encode($request_body);
    }
    $request_options = array_merge($request_options, $settings);
    $query_string = http_build_query([
      'environment' => $environment,
      'origin' => $origin,
    ]);
    $uri = $base_url;
    $uri .= "?{$query_string}";
    return $this->request(
      $method,
      $uri,
      $request_options
    );
  }

  /**
   * {@inheritdoc}
   * @throws \Exception
   */
  public function pushEntities($request_body = [], $settings = [], $request_headers = [], $method = 'PUT', $base_url = NULL, $environment = NULL, $origin = NULL) {
    if (!$base_url = $this->getGlobalValue($base_url, $this->baseUrl)) {
      throw new \Exception("Base url is not set");
    }
    if (!$environment = $this->getGlobalValue($environment, $this->environment)) {
      throw new \Exception("Environment is not set");
    }
    if (!$origin = $this->getGlobalValue($origin, $this->origin)) {
      throw new \Exception("Origin is not set");
    }
    $default_headers = $this->getDefaultRequestHeaders();
    $request_options = [
      'headers' => array_merge($default_headers, $request_headers),
    ];
    if (!empty($request_body)) {
      $request_options['body'] = json_encode($request_body);
    }

    $request_options = array_merge($request_options, $settings);
    $query_string = http_build_query([
      'environment' => $environment,
      'origin' => $origin,
    ]);
    $uri = $base_url;
    $uri .= "?{$query_string}";
    return $this->request(
      $method,
      $uri,
      $request_options
    );
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

  /**
   * {@inheritdoc}
   */
  protected function getGlobalValue($argument, $property) {
    if (!empty($argument)) {
      return $argument;
    }
    elseif (!empty($property)) {
      return $property;
    }
    return FALSE;
  }

}
