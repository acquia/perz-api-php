<?php

namespace Acquia\PerzApiPhp;


use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\Key;
use GuzzleHttp\Client;
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
  public static function getGuzzleClient(array $config): Client {
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


}
