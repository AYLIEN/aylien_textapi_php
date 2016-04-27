<?php
/**
 * Copyright 2016 Aylien, Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AYLIEN\TextAPI;

abstract class IO_Abstract
{
  const API_HOST_AND_PATH = 'api.aylien.com/api/v1';

  protected $url;
  protected $endpoint;
  protected $isHttps = true;
  protected $headers = array();
  protected $parameters = array();

  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }

  public function getEndpoint()
  {
    return $this->endpoint;
  }

  public function getUrl() {
    $protocol = $this->isHttps ? 'https' : 'http';
    return $protocol . '://' . self::API_HOST_AND_PATH . '/' . $this->endpoint;
  }

  public function setIsHttps($isHttps) {
    $this->isHttps = $isHttps;
  }

  public function getIsHttps() {
    return $this->isHttps;
  }

  public function setHeaders($headers) {
    $this->headers = $headers;
  }

  public function setParameters($parameters) {
    $this->parameters = $parameters;
  }

  public function getParameters() {
    return $this->parameters;
  }

  public function getRequestHeaders() {
    return $this->headers;
  }

  abstract public function execute();

  abstract public function getLastResponseHeaders();
}
