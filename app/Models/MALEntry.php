<?php

namespace App\Models;

class MALEntry {

  private $url;
  private $title;
  private $synonyms;
  private $episodes;
  private $premiered;

  public function get() {
    return [
      'url' => $this->url,
      'title' => $this->title,
      'synonyms' => $this->synonyms,
      'episodes' => $this->episodes,
      'premiered' => $this->premiered,
    ];
  }

  public function getUrl(): string {
    return $this->url;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getSynonyms(): string {
    return $this->synonyms;
  }

  public function getEpisodes(): int {
    return $this->episodes;
  }

  public function getPremiered(): string {
    return $this->premiered;
  }

  public static function parse($input): MALEntry {
    $instance = new self();

    $instance->url = $input->filterXPath('//meta[@property=\'og:url\']')->attr('content');
    $instance->title = $input->filterXPath('//meta[@property=\'og:title\']')->attr('content');

    $instance->synonyms = $instance->parseSynonyms($input);
    $instance->episodes = $instance->parseEpisodes($input);
    $instance->premiered = $instance->parsePremiered($input);

    return $instance;
  }

  private function parseSynonyms($input): string {
    $title = $input->filterXPath('//span[text()="Synonyms:"]');

    if (!$title->count()) {
      $title = $input->filterXPath('//span[text()="English:"]');
    }

    return (!$title->count()) ? '' : $this->trim_dom_crawler($title);
  }

  private function parseEpisodes($input): int {
    $episodes = $input->filterXPath('//span[text()="Episodes:"]');
    if (!$episodes->count()) {
      return null;
    }

    $episodes = $this->trim_dom_crawler($episodes);
    if ($episodes === 'Unknown') {
      return null;
    }

    return (int)$episodes;
  }

  private function parsePremiered($input): string {
    $premiered = $input->filterXPath('//span[text()="Premiered:"]');
    if (!$premiered->count()) {
      $aired = $input->filterXPath('//span[contains(text(), "Aired")]/..')->text();
      $aired = trim(explode("\n", trim($aired))[1]);
      $airedMonth = explode(' ', $aired)[0];
      $airedYear = explode(' ', $aired)[2];

      switch ($airedMonth) {
        case 'Jan':
        case 'Feb':
        case 'Mar':
          $premiered = 'Winter ' . $airedYear;
          break;
        case 'Apr':
        case 'May':
        case 'Jun':
          $premiered = 'Spring ' . $airedYear;
          break;
        case 'Jul':
        case 'Aug':
        case 'Sep':
          $premiered = 'Summer ' . $airedYear;
          break;
        case 'Oct':
        case 'Nov':
        case 'Dec':
          $premiered = 'Fall ' . $airedYear;
          break;
      }

      return $premiered;
    }

    $premiered = $this->trim_dom_crawler($premiered);
    if ($premiered === '?') {
      return '';
    }

    return $premiered;
  }

  private function trim_dom_crawler($input) {
    return trim(str_replace($input->text(), '', $input->parents()->text()));
  }
}
