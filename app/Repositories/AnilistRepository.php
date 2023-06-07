<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

use App\Exceptions\Anilist\ConfigException;
use App\Exceptions\Anilist\ConnectionException;
use App\Exceptions\Anilist\ParsingException;
use App\Exceptions\Anilist\RateLimitException;

class AnilistRepository {

  protected $anilistURI;

  public function __construct() {
    $this->anilistURI = 'https://' . config('app.anilist_base_uri');

    if (!config('app.anilist_base_uri')) {
      throw new ConfigException();
    }
  }

  public function get($id = 101280) {
    $query = '
      query ($id: Int) {
        Media (id: $id, type: ANIME) {
          id
          episodes
          siteUrl
          season
          seasonYear
          title {
            romaji
          }
        }
      }
    ';

    $variables = [
      "id" => $id,
    ];

    return $this->query_anilist($query, $variables);
  }

  public function search(array $values) {
    $query = '
      query ($id: Int, $search: String) {
        Page (page: 1, perPage: 10) {
          media (id: $id, search: $search, sort: FAVOURITES_DESC) {
            id
            title {
              romaji
            }
          }
        }
      }
    ';

    $variables = [
      "search" => $values['query'],
    ];

    return $this->query_anilist($query, $variables);
  }

  private function query_anilist(string $query, array $variables) {
    $response = Http::acceptJson()
      ->post($this->anilistURI, [
        'query' => $query,
        'variables' => $variables,
      ]);

    if ($response->status() >= 429) {
      $retry = $response->header('Retry-After') ?? 'unknown';

      throw new RateLimitException($retry);
    }

    if ($response->status() >= 500) {
      throw new ConnectionException();
    }

    $body = json_decode($response->body(), true);

    if (isset($body['errors'])) {
      throw new ParsingException();
    }

    return $body['data'];
  }
}
