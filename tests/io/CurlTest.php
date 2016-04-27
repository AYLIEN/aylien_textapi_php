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

class CurlTest extends BaseTest
{

  public function testIfAddsAcceptAndContentTypeHeaders()
  {
    $io = new AYLIEN\TextAPI\IO_Curl();
    $this->assertContains('Accept: application/json',
      $io->getRequestHeaders());
    $this->assertContains('Content-Type: application/x-www-form-urlencoded',
      $io->getRequestHeaders());
  }

  public function testIsHttpsByDefault()
  {
    $io = new AYLIEN\TextAPI\IO_Curl();
    $this->assertTrue($io->getIsHttps());
  }

  public function testUrlBuilder()
  {
    $io = new AYLIEN\TextAPI\IO_Curl();
    $io->setEndpoint('sentiment');
    $this->assertStringStartsWith('https://', $io->getUrl());
    $this->assertStringEndsWith('sentiment', $io->getUrl());
    $io->setIsHttps(false);
    $this->assertStringStartsWith('http://', $io->getUrl());
  }

  public function testResponseHeaderParser()
  {
    $dir = __DIR__ . '/../fixtures/';
    $io = new AYLIEN\TextAPI\IO_Curl();
    $io->setLastResponseRawHeaders(file_get_contents($dir . 'curl_io_ratelimit_headers'));
    $headers = $io->getLastResponseHeaders();
    $this->assertEquals($headers['X-RateLimit-Reset'], "1420761600");
    $this->assertEquals($headers['X-RateLimit-Limit'], "1000");
    $this->assertEquals($headers['X-RateLimit-Remaining'], "953");
  }
}
