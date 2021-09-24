<?php

namespace Acquia\PerzApiPhp;

use Exception;
use GuzzleHttp\Client;
use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;


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

  public function __call($method, $args) {
    parent::__call($method, $args);
  }


  /**
   * @param $method
   * @param $url
   * @param $entity_type
   * @param $entity_id
   * @return \Psr\Http\Message\ResponseInterface|void
   * @throws Exception
   */
  public function pushEntity($method, $url, $entity_type, $entity_id){
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
    } catch (Exception $exception) {
      $this->exceptionHandler($exception);
    }
  }

  /**
   * @param $method
   * @param $url
   * @param $data
   * @return \Psr\Http\Message\ResponseInterface|void
   * @throws Exception
   */
  public function pushEntities($method, $url, $data){
    try {
      return $this->request(
        $method,
        $url, [
          'headers' =>$this->getDefaultRequestHeaders(),
          'body' => json_encode($data),
        ]
      );
    } catch (Exception $exception) {
      $this->exceptionHandler($exception)
;    }
  }


  /**
   * @param string $method
   * @param string $base_url
   * @param array $request_body
   * @param array $settings
   * @param array $request_headers
   * @param string $environment
   * @param string $origin
   * @return \Psr\Http\Message\ResponseInterface|void
   * @throws Exception
   */
  public function pushEntitiesToCisDocker(
    $method = 'PUT',
    $base_url = NULL,
    $request_body = [],
    $settings = [],
    $request_headers = [],
    $environment = NULL,
    $origin = NULL
  ){
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
    try{
      return parent::request(
        $method,
        $uri,
        $request_options
      );
    }catch (Exception $exception){
      $this->exceptionHandler($exception);
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultRequestHeaders(){
    return [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function getGlobalValue($argument, $property){
    if (!empty($argument)) {
      return $argument;
    }
    elseif (!empty($property)) {
      return $property;
    }
    return FALSE;
  }

  /**
   * @param $exception
   * @throws Exception
   */
  protected function exceptionHandler($exception){
    if ($exception instanceof BadResponseException) {
      $message = sprintf('Error registering client (Error Code = %d: %s)',
        $exception->getResponse()->getStatusCode(),
        $exception->getResponse()->getReasonPhrase());
      throw new RequestException($message, $exception->getRequest(),
        $exception->getResponse());
    }
    if ($exception instanceof RequestException) {
      $message = sprintf('Could not get authorization to register client %s. Are your credentials inserted correctly? (Error message = %s)',
        $exception->getMessage());
      throw new RequestException($message, $exception->getRequest(),
        $exception->getResponse());
    }
    $message = sprintf("An unknown exception was caught. Message: %s",
      $exception->getMessage());
    throw new Exception($message);
  }

}
