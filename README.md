About
=====

This is the PHP client library for AYLIEN's APIs. If you haven't already done so, you will need to [sign up](https://developer.aylien.com/signup).

Installation
============

To install, simply added it to your `composer.json`:

```json
{
  "require": {
    "aylien/textapi": "0.6.*"
  }
}
```

See the [Developers Guide](https://developer.aylien.com/docs) for additional documentation.

Example
=======

```php
$textapi = new AYLIEN\TextAPI("YourApplicationId", "YourApplicationKey");
$sentiment = $textapi->Sentiment(array('text' => 'John is a very good football player!'));
```
