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

class IO_Curl extends IO_Abstract
{
  private $lastResponseRawHeaders;

  public function execute()
  {
    $ch = curl_init($this->getUrl());
    curl_setopt($ch, CURLOPT_POST,              true);
    curl_setopt($ch, CURLOPT_HEADER,            true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,        $this->getRequestHeaders());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
      preg_replace("/%5B[0-9]+%5D=/i", "=", http_build_query($this->getParameters())));

    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $this->lastResponseRawHeaders = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http_code >= 300) {
      $decoded_error = json_decode($body);
      if ($decoded_error && isset($decoded_error->error)) {
        throw new \UnexpectedValueException($decoded_error->error);
      }

      throw new \UnexpectedValueException($body);
    }

    return $body;
  }

  public function getRequestHeaders()
  {
    return array_merge(
      array(
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded'
      ),
      $this->headers
    );
  }

  public function setLastResponseRawHeaders($headers)
  {
    $this->lastResponseRawHeaders = $headers;
  }

  public function getLastResponseHeaders()
  {
    $headers = array_map(function($h) {
      $h = trim($h);
      if (stripos($h, 'HTTP/1.') === false) {
        $parts = explode(':', $h, 2);
        if (count($parts) == 2) {
          return array($parts[0], trim($parts[1]));
        }
      }
    }, explode("\n", $this->lastResponseRawHeaders));

    $headers = array_filter($headers, function($h) {
      return strlen($h[0]);
    });

    $parsedHeaders = array();
    foreach ($headers as $header) {
      $parsedHeaders[$header[0]] = $header[1];
    }

    return $parsedHeaders;
  }
}
