# Acquia Perz CIS Client for PHP

Provides an API client for PHP applications communicating with the Acquia Personalization Content Index Service .

## Installation

Install the latest version with [Composer](https://getcomposer.org/):

```bash
$ composer require acquia/perz-api-php
```

## Usage

#### Create API Client

```php
use Acquia\PerzApiPhp\ObjectFactory;
use Acquia\PerzApiPhp\PerzApiPhpClient;

$base_uri = ' https://dummy.api.endpoint';
$api_key = 'XXX-XXX-XXX';
$secret_key = 'XXX-XXX-XXX';

$key = ObjectFactory::getAuthenticationKey($api_key, $secret_key);
$middleware = ObjectFactory::getHmacAuthMiddleware($key);
$config = [
  'base_url' => $base_uri,
];
$api_client =  new PerzApiPhpClient($middleware, $config);
```
#### Create entity in Personalization

```php
 /**
   * Create entity in Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'origin' => (string) Site ID. Required.
   *     'environment' => (string) Site envireonment. Required.
   *      'domain' => (string) Site Domain. Required.
   *      'entity_variations' => (array) Entity variation data. Required.
   *      'site_hash' => (string) Site hash. Optional.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */

$response = $api_client->putVariations($data);
```
#### Delete entities from Personalization.
```php
 /**
   * Delete entities from Personalization.
   *
   * @param array $data
   *   An array of Entity data.
   *   $data = [
   *     'account_id' => (string) Acquia Account ID. Required.
   *     'environment' => (string) Site environment. Required.
   *     'origin' => (string) Site ID. Optional.
   *     'content_uuid' => (string) UUID of the entity. Optional.
   *     'language' => (string) UUID of the entity. Optional.
   *     'view_mode' => (string) UUID of the entity. Optional.
   *     'site_hash' => (string) Site hash. Optional.
   *   ].
   *
   * @return \Psr\Http\Message\ResponseInterface|void
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Guzzle Exception.
   */

$response = $api_client->getEntities($data);
```
#### Get entities from Personalisation.

```php
<?php
/**
 * Get entities from Personalisation.
 *
 * @param array $data
 *   An array of Entity data.
 *   $data = [
 *     'account_id' => (string) Acquia Account ID. Required.
 *     'origin' => (string) Site ID. Required.
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
 *     'site_hash' => (string) Site hash. Optional.
 *   ].
 *
 * @return \Psr\Http\Message\ResponseInterface|void
 *   Response.
 *
 * @throws \GuzzleHttp\Exception\GuzzleException
 *   Guzzle Exception.
 */

    $response = $api_client->getEntities($data);

```

