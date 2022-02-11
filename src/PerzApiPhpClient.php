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
   * Get entity from Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site environment. Required.
   *     'language' => (string) Language of the entity. Required.
   *     'view_mode' => (string) View mode of the entity. Required.
   *     'uuid' => (string) UUID of the entity. Required.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function getEntity(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'origin' => $data['origin'],
        'environment' => $data['environment'],
        'language' => $data['language'],
        'view_mode' => $data['view_mode'],
      ],
    ];
    return $this->request('get', '/v3/contents/' . $data['uuid'], $options);
  }

  /**
   * Get entities in Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site environment. Required.
   *     'language' => (string) Entity Language. Optional.
   *     'view_mode' => (string) View mode of Entity. Optional.
   *     'q' => (string) Keywords to search. Optional.
   *     'content_type' => (string) Type of the Entity. Oprional.
   *     'tags' =>  (string) Tags to search, Optional.
   *     'all_tags' => (string) All tags to search. Optional.
   *     'date_start' => (datetime) Start date of Entity update. Optional.
   *     'date_end' => (datetime) End date of Entity update. Optional.
   *     'rows' => (integer) Number of rows in result. Default 10. Optional.
   *     'start' => (integer) Page start index. Default 0. Optional.
   *     'sort' => (string) Sort by field. Default modified. Optional.
   *     'sort_order' => (string) Sort order. Default desc. Optional.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function getEntities(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'origin' => $data['origin'],
        'environment' => $data['environment'],
        'language' => $data['language'] ?? NULL,
        'view_mode' => $data['view_mode'] ?? NULL,
        'q' => $data['q'] ?? NULL,
        'content_type' => $data['content_type'] ?? NULL,
        'tags' => $data['tags'] ?? NULL,
        'all_tags' => $data['all_tags'] ?? NULL,
        'date_start' => $data['date_start'] ?? NULL,
        'date_end' => $data['date_end'] ?? NULL,
        'rows' => $data['rows'] ?? 10,
        'start' => $data['start'] ?? 0,
        'sort' => $data['sort'] ?? 'modified',
        'sort_order' => $data['sort_order'] ?? 'desc',
      ],
    ];
    return $this->request('get', '/v3/contents', $options);
  }

  /**
   * Push entity to Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site environment. Required.
   *     'domain' => (string) Domain of the site. Required.
   *     'op' => (string) View mode of the entity. Required.
   *     'entity_type_id' => (string) Entity Type,
   *     'entity_uuid' => (string) Entity uuid,
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *    Guzzle Exception.
   */
  public function pushEntity(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'origin' => $data['origin'],
        'environment' => $data['environment'],
        'domain' => $data['domain'],
        'op' => $data['op'],
      ],
      'body' => json_encode([
        'entity_type_id' => $data['entity_type'],
        'entity_uuid' => $data['entity_uuid'],
      ]),
    ];
    return $this->request('post', '/v3/webhook', $options);
  }

  /**
   * Push multiple entities to Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function callWebhook(array $data) {
    $options['body'] = json_encode($data);
    return $this->request('post', '/v3/webhook', $options);
  }

  /**
   * Delete entity from Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site environment. Required.
   *     'language' => (string) Language of the entity. Optional.
   *     'view_mode' => (string) View mode of the entity. Optional.
   *     'uuid' => (string) UUID of the entity. Required.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function deleteEntity(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'origin' => $data['origin'],
        'environment' => $data['environment'],
        'language' => $data['language'],
        'view_mode' => $data['view_mode'],
      ],
    ];
    return $this->request('delete', '/v3/contents/' . $data['uuid'], $options);
  }

  /**
   * Delete entities from Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site environment. Required.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function deleteEntities(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'environment' => $data['environment'],
        'origin' => $data['origin'] ?? NULL,
        'content_uuid' => $data['content_uuid'] ?? NULL,
        'language' => $data['language'] ?? NULL,
        'view_mode' => $data['view_mode'] ?? NULL,
      ],
    ];
    return $this->request('delete', '/v3/contents', $options);
  }

  /**
   * Graphql request.
   *
   * * @param array $query
   *    Grahql Query data.
   */
  public function graphql(array $data) {

    $options['headers'] = [
      'Content-Type' => 'application/json',
    ];
    $options['body'] = json_encode($data);
    return $this->request('POST', '/perz3', $options);
  }

  /**
   * Push entity to Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site envireonment. Required.
   *      'domain' => (string) Site Domain. Required.
   *      'entity_variations' => (array) Entity variation data. Required.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function postVariations(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'origin' => $data['origin'],
        'environment' => $data['environment'],
      ],
      'body' => json_encode($data['entity_variations']),
    ];
    return $this->request('post', '/v3/contents', $options);
  }

  /**
   * Create entity in Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site hash. Required.
   *     'environment' => (string) Site envireonment. Required.
   *      'domain' => (string) Site Domain. Required.
   *      'entity_variations' => (array) Entity variation data. Required.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */
  public function putVariations(array $data) {
    $options = [
      'query' => [
        'account_id' => $data['account_id'],
        'origin' => $data['origin'],
        'environment' => $data['environment'],
      ],
      'body' => json_encode($data['entity_variations']),
    ];
    return $this->request('put', '/v3/contents', $options);
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
