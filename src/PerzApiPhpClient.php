<?php

namespace Acquia\PerzApiPhp;

use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\PerzApiPhp\Guzzle\Middleware\RequestResponseHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use function GuzzleHttp\default_user_agent;

/**
 * Class PerzApiPhpClient: A php client to Integrate Personalization APIs.
 *
 * @package Acquia\PerzApiPhp
 */
class PerzApiPhpClient extends Client {

  const LIBRARYNAME = 'AcquiaPerzApiPhp';

  const OPTION_NAME_LANGUAGES = 'client-languages';

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
    HmacAuthMiddleware $middleware,
    array $config = [],
    string $api_version = ''
  ) {

    if (!isset($config['base_uri']) && isset($config['base_url'])) {
      $config['base_uri'] = self::makeBaseURL($config['base_url'], $api_version);
    }
    else {
      $config['base_uri'] = self::makeBaseURL($config['base_uri'], $api_version);
    }

    // Setting up the User Header string.
    $user_agent_string = self::LIBRARYNAME . '/' . self::VERSION . ' ' . default_user_agent();
    if (isset($config['client-user-agent'])) {
      $user_agent_string = $config['client-user-agent'] . ' ' . $user_agent_string;
    }

    // Setting up the headers.
    $config['headers']['Content-Type'] = 'application/json';
    $config['headers']['User-Agent'] = $user_agent_string;

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
    $this->addRequestResponseHandler($config);

    parent::__construct($config);
  }

  /**
   * Push entity to Personalization.
   *
   * @param string $entity_type
   *   Type of the Entity.
   * @param string $entity_id
   *   ID of the Entity.
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   */
  public function pushEntity(string $entity_type, string $entity_id) {
    $options['body'] = json_encode([
      'entity_type_id' => $entity_type,
      'entity_uuid' => $entity_id,
    ]);
    return $this->request('post', '/v1/webhook', $options);
  }

  /**
   * Push multiple entities to Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   */
  public function pushEntities(array $data) {
    $options['body'] = json_encode($data);
    return $this->request('post', '/v1/webhook', $options);
  }

  /**
   * Graphql request.
   *
   * * @param string $query
   *    Grahql Query string.
   */
  public function graphql(array $data) {
    $options['body'] = $data;
    return $this->request('post', '/perz3', $options);
  }

  /**
   * Push entity to Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   */
  public function pushVariations(array $data) {
    $options['body'] = json_encode($data);
    return $this->request('post', '/api/push-variations-endpoint', $options);
  }

  /**
   * Make a base url out of components and add a trailing slash to it.
   *
   * @param string[] $base_url_components
   *   Base URL components.
   *
   * @return string
   *   Processed string.
   */
  protected static function makeBaseURL(...$base_url_components): string { // phpcs:ignore
    return self::makePath(...$base_url_components) . '/';
  }

  /**
   * Make path out of its individual components.
   *
   * @param string[] $path_components
   *   Path components.
   *
   * @return string
   *   Processed string.
   */
  protected static function makePath(...$path_components): string { // phpcs:ignore
    return self::gluePartsTogether($path_components, '/');
  }

  /**
   * Glue all elements of an array together.
   *
   * @param array $parts
   *   Parts array.
   * @param string $glue
   *   Glue symbol.
   *
   * @return string
   *   Processed string.
   */
  protected static function gluePartsTogether(array $parts, string $glue): string {
    return implode($glue, self::removeAllLeadingAndTrailingSlashes($parts));
  }

  /**
   * Removes all leading and trailing slashes.
   *
   * Strip all leading and trailing slashes from all components of the given
   * array.
   *
   * @param string[] $components
   *   Array of strings.
   *
   * @return string[]
   *   Processed array.
   */
  protected static function removeAllLeadingAndTrailingSlashes(array $components): array {
    return array_map(function ($component) {
      return trim($component, '/');
    }, $components);
  }

  /**
   * Attaches RequestResponseHandler to handlers stack.
   *
   * @param array $config
   *   Client config.
   *
   * @codeCoverageIgnore
   */
  protected function addRequestResponseHandler(array $config): void {
    if (empty($config['handler']) || empty($this->logger)) {
      return;
    }

    if (!$config['handler'] instanceof HandlerStack) {
      return;
    }

    $config['handler']->push(new RequestResponseHandler($this->logger));
  }

}
