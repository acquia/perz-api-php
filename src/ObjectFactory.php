<?php

namespace Acquia\PerzApiPhp;

use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\Key;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;

/**
 * Class ObjectFactory.
 *
 * @package Acquia\PerzApiPhp
 * @codeCoverageIgnore
 */
class ObjectFactory {

  /**
   * Creates Guzzle client.
   *
   * @param array $config
   *   Initial data.
   *
   * @return \GuzzleHttp\Client
   *   GuzzleClient instance.
   */
  public static function getGuzzleClient(array $config = []): Client {
    return new Client($config);
  }

  /**
   * Creates authentication key.
   *
   * @param string $api_key
   *   API key.
   * @param string $secret
   *   Secret.
   *
   * @return \Acquia\Hmac\Key
   *   Key instance.
   */
  public static function getAuthenticationKey($api_key, $secret): Key {
    return new Key($api_key, $secret);
  }

  /**
   * Creates HmacAuthMiddleware.
   *
   * @param \Acquia\Hmac\Key $key
   *   Key instance.
   *
   * @return \Acquia\Hmac\Guzzle\HmacAuthMiddleware
   *   HmacAuthMiddleware instance.
   */
  public static function getHmacAuthMiddleware(Key $key): HmacAuthMiddleware {
    return new HmacAuthMiddleware($key);
  }

  /**
   * Creates a default handler stack that can be used by clients.
   *
   * @return \GuzzleHttp\HandlerStack
   *   HandlerStack instance.
   */
  public static function getHandlerStack(): HandlerStack {
    return HandlerStack::create();
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
   * @throws \Exception
   */
  public static function pushEntities($method = 'PUT', $base_url = NULL, $request_body = [], $settings = [], $request_headers = [], $environment = NULL, $origin = NULL) {
    if (!$base_url) {
      throw new \Exception("Base url is not set");
    }
    if (!$environment) {
      throw new \Exception("Environment is not set");
    }
    if (!$origin) {
      throw new \Exception("Origin is not set");
    }
    $default_headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];
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
    try {
      $client = self::getGuzzleClient();
      return $client->request(
        $method,
        $uri,
        $request_options
      );
    }
    catch (\Exception $exception) {
      self::exceptionHandler($exception);
    }
  }

  /**
   * @param $exception
   * @throws \Exception
   */
  public static function exceptionHandler($exception) {
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
    throw new \Exception($message);
  }

}
