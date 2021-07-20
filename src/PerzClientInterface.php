<?php

namespace Acquia\PerzApiPhp;

/**
 * Perz client interface for sending HTTP requests.
 */
interface PerzClientInterface
{

  /**
   * @param $id
   * @param array $request_body
   * @param array $settings
   * @param array $request_headers
   * @param string $method
   * @param null $base_url
   * @param null $environment
   * @param null $origin
   *
   * @return mixed
   */
  public function pushEntityById($id, $request_body = [], $settings = [], $request_headers = [], $method = 'PUT', $base_url = NULL, $environment = NULL, $origin = NULL);

  /**
   * @param array $request_body
   * @param array $settings
   * @param array $request_headers
   * @param string $method
   * @param null $base_url
   * @param null $environment
   * @param null $origin
   *
   * @return mixed
   */
  public function pushEntities($request_body = [], $settings = [], $request_headers = [], $method = 'PUT', $base_url = NULL, $environment = NULL, $origin = NULL);
}
