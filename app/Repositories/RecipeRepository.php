<?php

namespace App\Repositories;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Transformation\Resize;

use App\Models\Recipe;

class RecipeRepository {

  private $cloudinary = null;

  public function __construct() {
    $config = new Configuration(config('app.cloudinary_url'));
    $cloudinary = new Cloudinary($config);

    $this->cloudinary = $cloudinary;
  }

  public function get_all() {
    $data = Recipe::select('id', 'title', 'description', 'image_id', 'created_at', 'updated_at')->get();

    foreach ($data as $item) {
      if ($item['image_id']) {
        $image_id = $item['image_id'];
        unset($item['image_id']);

        $url = $this->cloudinary->image('recipes/' . $image_id)
          ->resize(Resize::fill(256, 256))
          ->toUrl();

        $item['image_url'] = $url;
      } else {
        unset($item['image_id']);
        $item['image_url'] = null;
      }
    }

    return $data;
  }

  public function get($id) {
    $data = Recipe::where('id', $id)->firstOrFail();

    if ($data['image_id']) {
      $image_id = $data['image_id'];
      unset($data['image_id']);

      $url = $this->cloudinary->image('recipes/' . $image_id)
        ->resize(Resize::fill(256, 256))
        ->toUrl();

      $url_lg = $this->cloudinary->image('recipes/' . $image_id)
        ->resize(Resize::fill(1024, 320))
        ->toUrl();

      $data['image_url'] = $url;
      $data['image_url_lg'] = $url_lg;
    } else {
      unset($data['image_id']);
      $data['image_url'] = null;
      $data['image_url_lg'] = null;
    }

    return $data;
  }

  public function add(array $values) {
    Recipe::create($values);
  }

  public function edit(array $values, $id) {
    Recipe::where('id', $id)->firstOrFail()->update($values);
  }

  public function delete($id) {
    Recipe::where('id', $id)->firstOrFail()->delete();
  }
}
