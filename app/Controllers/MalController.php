<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Anime;
use App\Models\AnimeSearch;

class MalController extends Controller {

  protected $scrapeURI;

  public function __construct() {
    $this->scrapeURI = env('SCRAPER_BASE_URI', null);
  }

  public function index($params) {
    if (!env('DISABLE_SCRAPER')) {
      if (env('SCRAPER_BASE_URI')) {
        $this->scrape($params);
      } else {
        throw new Exception('Web Scraper configuration not found');
      }
    }
  }

  private function scrape($params) {
    if (is_numeric($params)) {
      return $this->getAnime($params);
    } else {
      return $this->searchAnime($params);
    }
  }

  private function getAnime($id = 37430) {
    try {
      $html = Http::get($this->scrapeURI . '/anime/' . $id)->body();

      return Anime::parse(new Crawler($html));
    } catch (Exception $e) {
      if (env('APP_DEBUG')) {
        throw new Exception('Issues in connecting to MAL Servers');
      } else {
        return response([
          'status' => 503,
          'message' => 'Issues in connecting to MAL Servers',
        ], 503);
      }
    }

    return response()->json($data);
  }

  private function searchAnime($query) {
    $data = app('mal')->search($query)->get();

    try {
      $html = Http::get($this->scrapeURI . '/anime.php?q=' . urldecode($query))->body();

      return AnimeSearch::parse(new Crawler($html));
    } catch (Exception $e) {
      if (env('APP_DEBUG')) {
        throw new Exception('Issues in connecting to MAL Servers');
      } else {
        return response([
          'status' => 503,
          'message' => 'Issues in connecting to MAL Servers',
        ], 503);
      }
    }

    return response()->json($data);
  }

}
