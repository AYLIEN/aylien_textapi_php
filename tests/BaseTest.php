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

class BaseTest extends PHPUnit_Framework_TestCase
{
  private $app_id;
  private $app_key;

  public function __construct($name = NULL, array $data = array(), $dataName = '')
  {
    $this->app_id = 'random';
    $this->app_key = 'random';

    parent::__construct($name, $data, $dataName);
  }

  public function getClient()
  {
    $client = new AYLIEN\TextAPI($this->app_id, $this->app_key);

    return $client;
  }
}
