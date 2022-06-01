<?php

namespace App\Models;

use Symfony\Component\DomCrawler\Crawler;

class AnimeSearch {

  private $results;

  public function get() {
    return $this->results;
  }

  public static function parse($input): AnimeSearch {
    $instance = new self();
    $instance->results = $instance->parseResults($input);

    return $instance;
  }

  private function parseResults($input) {
    $tempResults = $input
      ->filterXPath('//div[contains(@class, "js-categories-seasonal")]/table/tr[1]');

    if (!$tempResults->count()) {
      return [];
    }

    $results = $tempResults->nextAll()
      ->each(
        function (Crawler $c) {
          $url = $c->filterXPath('//td[2]//a')->attr('href');
          $urlId = explode('/', $url)[4]; // URL retrieved is http://domain.com/route/<id>/sub-route

          return [
            'id' => $urlId,
            'title' => $c->filterXPath('//td[2]//a/strong')->text(),
          ];
        }
      );

    return (count($results)) ? array_slice($results, 0, 5) : [];
  }
}
