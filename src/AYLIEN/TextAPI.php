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

namespace AYLIEN;

require_once 'IO/Abstract.php';
require_once 'IO/Curl.php';

/**
 * The AYLIEN TextAPI Client
 */
class TextAPI 
{
  /**
   * @var   string  Application ID
   */
  protected $application_id;

  /**
   * @var   string  Application Key
   */
  protected $application_key;

  /**
   * @var   boolean Whether to use HTTPS for web service calls
   */
  protected $use_https = true;

  /**
   * @var   IO_Abstract
   */
  private $io;

  /**
   * @var   string  Client Version
   */
  private $version = '0.7.0';

  /**
   * Constructs the AYLIEN TextAPI client.
   *
   * @param $application_id     Application ID
   * @param $application_key    Application Key
   * @param $use_https          Whether to use HTTPS for web service calls
   * @param $io_class           
   */
  public function __construct($application_id, $application_key, $use_https = true)
  {
    if (!is_string($application_id) || !is_string($application_key) ||
      $application_id == "" || $application_key == ""
    ) {
      throw new \BadMethodCallException("Invalid Application ID or Application Key.");
    }
    $this->application_id = $application_id;
    $this->application_key = $application_key;
    $this->use_https = $use_https;
  }

  public function setIo($io)
  {
    $this->io =  $io;
  }

  public function getIo()
  {
    if (!isset($this->io)) {
      $this->io = new TextAPI\IO_Curl();
      $this->io->setIsHttps($this->use_https);
    }

    return $this->io;
  }

  /**
   * Extracts the main body of article, including embedded media such as
   * images & videos from a URL and removes all the surrounding clutter
   *
   * <ul>
   *    <li>['url']         <i><u>string</u></i> URL</li>
   *    <li>['best_image']  <i><u>string</u></i> Whether to extract the best
   *    image of the article</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Extract($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['url']) && empty($params['html'])) {
      throw new \BadMethodCallException("You must either provide a url or html");
    }
    $httpRequest = $this->buildHttpRequest('extract', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Detects sentiment of a body of text in terms of polarity
   * ("positive" or "negative") and subjectivity
   * ("subjective" or "objective")
   *
   * <ul>
   *    <li>['url']     <i><u>string</u></i> URL</li>
   *    <li>['text']    <i><u>string</u></i> Text</li>
   *    <li>['mode']    <i><u>string</u></i> Analyze mode. tweet or document.
   *        default is tweet</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Sentiment($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('sentiment', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

    /**
     * Analyzes sentiment towards entities found in text. Extracts mentions
     * of named entities (Person, Organization, Location), associates a type
     * and links them to DBPedia (where possible), and evaluates sentiment
     * towards each of the entities. This endpoint includes some of the
     * functionality of the entities and concepts endpoints.
     *
     * <ul>
     *    <li>['url']     <i><u>string</u></i> URL</li>
     *    <li>['text']    <i><u>string</u></i> Text</li>
     * </ul>
     *
     * @param array   $params (See above)
     */
    public function EntityLevelSentiment($params)
    {
        $params = $this->normalizeInput($params);
        if (empty($params['text']) && empty($params['url'])) {
            throw new \BadMethodCallException("You must either provide url or text");
        }
        $httpRequest = $this->buildHttpRequest('elsa', $params);
        $response = $this->executeRequest($httpRequest);

        return $response;
    }

  /**
   * Given a review for a product or service, analyzes the sentiment of the
   * review towards each of the aspects of the product or review that are
   * mentioned in it.
   *
   * <ul>
   *    <li>['url']    <i><u>string</u></i> URL</i>
   *    <li>['text']   <i><u>string</u></i> Text</i>
   *    <li>['domain'] <i><u>string</u></i> Domain</i>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function AspectBasedSentiment($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    if (empty($params['domain'])) {
      throw new \BadMethodCallException("You must specify the domain");
    }
    $domain = $params['domain'];
    unset($params['domain']);
    $httpRequest = $this->buildHttpRequest('absa/' . $domain, $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Classifies a body of text according to IPTC NewsCode standard into more
   * than 500 categories.
   *
   * <ul>
   *    <li>['url']         <i><u>string</u></i> URL</li>
   *    <li>['text']        <i><u>string</u></i> Text</li>
   *    <li>['language']    <i><u>string</u></i> Language of text</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Classify($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('classify', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Classifies a body of text according to the specified taxonomy.
   *
   * <ul>
   *    <li>['url']         <i><u>string</u></i> URL</i>
   *    <li>['text']        <i><u>string</u></i> Text</i>
   *    <li>['language']    <i><u>string</u></i> Language</i>
   *    <li>['taxonomy']    <i><u>string</u></i> Taxonomy</i>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function ClassifyByTaxonomy($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    if (empty($params['taxonomy'])) {
      throw new \BadMethodCallException("You must specify the taxonomy");
    }
    $taxonomy = $params['taxonomy'];
    unset($params['taxonomy']);
    $httpRequest = $this->buildHttpRequest('classify/' . $taxonomy, $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Extracts named entities mentioned in a document, disambiguates and
   * cross link them to DBPedia and Linked Data entities, along with their
   * semantic types (including DBPedia and schema.org)
   *
   * <ul>
   *    <li>['url']         <i><u>string</u></i> URL</li>
   *    <li>['text']        <i><u>string</u></i> Text</li>
   *    <li>['language']    <i><u>string</u></i> Language of text</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Concepts($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('concepts', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Suggests hashtags describing the document
   *
   * <ul>
   *    <li>['url']         <i><u>string</u></i> URL</li>
   *    <li>['text']        <i><u>string</u></i> Text</li>
   *    <li>['language']    <i><u>string</u></i> Language of text</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Hashtags($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('hashtags', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Extracts named entities (people, organizations and locations) and values
   * (URLs, emails, telephone numbers, currency amounts and percentages)
   * mentioned in a bod of text
   *
   * <ul>
   *    <li>['url']     <i><u>string</u></i> URL</li>
   *    <li>['text']    <i><u>string</u></i> Text</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Entities($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('entities', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Detects the main language of a document is written in
   *
   * <ul>
   *    <li>['url']     <i><u>string</u></i> URL</li>
   *    <li>['text']    <i><u>string</u></i> Text</li>
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Language($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('language', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Summarizes an article into a few key sentences.
   *
   * <ul>
   *    <li>['title']                   <i><u>string</u></i> Title</li>
   *    <li>['text']                    <i><u>string</u></i> Text</li>
   *    <li>['url']                     <i><u>string</u></i> URL</li>
   *    <li>['mode']                    <i><u>string</u></i> Summarize mode.
   *    Either default or short. Default is default</li>
   *    <li>['sentences_number']        <i><u>integer</u></i> Number of
   *    sentences to be returned in default mode (not applicable to short mode)
   *    </li>
   *    <li>['sentences_percentage']    <i><u>integer</u></i> Percentage of
   *    sentences to be returned in default mode (not applicable to shor mode)
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Summarize($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['url']) && (empty($params['text']) || empty($params['title']))) {
      throw new \BadMethodCallException("You must either provide url or a pair of text and title");
    }
    $httpRequest = $this->buildHttpRequest('summarize', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Assigns relevant tags to an image
   *
   * <ul>
   *    <li>['url'] <i><u>string</u></i> URL</li>
   * </ul>
   *
   * @param array   $param (See above)
   */
  public function ImageTags($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['url'])) {
      throw new \BadMethodCallException("You must provide a url");
    }
    $httpRequest = $this->buildHttpRequest('image-tags', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Runs multiple analysis operations in one API call
   *
   * <ul>
   *    <li>['url']         <i><u>string</u></i> URL</li>
   *    <li>['text']        <i><u>string</u></i> Text</li>
   *    <li>['endpoint']    <i><u>array</u></i> List of endpoints to call
   * </ul>
   *
   * @param array   $params (See above)
   */
  public function Combined($params)
  {
    $params = $this->normalizeInput($params);
    if (empty($params['text']) && empty($params['url'])) {
      throw new \BadMethodCallException("You must either provide url or text");
    }
    $httpRequest = $this->buildHttpRequest('combined', $params);
    $response = $this->executeRequest($httpRequest);

    return $response;
  }

  /**
   * Returns client's rate limits
   *
   * <ul>
   *    <li>['limit']       <i><u>int</u></i> Plan's limit</li>
   *    <li>['reset']       <i><u>int</u></i> Unix UTC timestamp indicating
   *    the exact time remaining resets</li>
   *    <li>['remaining']   <i><u>int</u></i> Remaining calls</li>
   * </ul>
   *
   * @return    array (See above)
   */
  public function getRateLimits()
  {
    $headers = $this->getIo()->getLastResponseHeaders();
    $rateLimits = array();
    foreach ($headers as $key => $value) {
      if (stripos($key, 'X-RateLimit-') === 0) {
        $rateLimits[strtolower(str_replace('X-RateLimit-', '', $key))] = intval($value);
      }
    }

    return $rateLimits;
  }

  protected function buildHttpRequest($endpoint, $parameters) {
    $credentials = array(
      'X-AYLIEN-TextAPI-Application-ID: ' . $this->application_id,
      'X-AYLIEN-TextAPI-Application-Key: ' . $this->application_key,
      'User-Agent: Aylien Text API PHP ' . $this->version
    );
    $io = $this->getIo();
    $io->setEndpoint($endpoint);
    $io->setHeaders($credentials);
    $io->setParameters($parameters);

    return $io;
  }

  protected function executeRequest(TextAPI\IO_Abstract $request)
  {
    $response = $request->execute();

    return json_decode($response);
  }

  protected function normalizeInput($input) {
    if (is_string($input)) {
      if (preg_match('|^https?://|i', $input)) {
        return array('url' => $input);
      } else {
        return array('text' => $input);
      }
    }

    return $input;
  }
}
