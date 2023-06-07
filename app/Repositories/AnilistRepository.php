<?php

namespace App\Repositories;

use Exception;

use Illuminate\Support\Facades\Http;

use App\Resources\ErrorResponse;

class AnilistRepository {

  protected $anilistURI;

  public function __construct() {
    $this->anilistURI = 'https://' . config('app.anilist_base_uri');
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
        Page (page: 1, perPage: 15) {
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
      $retry = $response->header('Retry-After');

      return ErrorResponse::unavailable(
        'AniList rate limit was reached. Please retry in ' . $retry . ' seconds.'
      );
    }

    if ($response->status() >= 500) {
      return ErrorResponse::unavailable('Issues in connecting to AniList Servers');
    }

    $body = json_decode($response->body(), true);

    // var_dump(isset($body['errors']));
    // die;

    if (isset($body['errors'])) {
      throw new Exception;
      return ErrorResponse::unavailable('Issues in parsing AniList response');
    }

    return $body['data'];
  }
}
