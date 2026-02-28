<?php

namespace App\Repositories;

use App\Models\Recipe;

class RecipeRepository {

  public function get_all() {
    return Recipe::select('id', 'title', 'description', 'created_at', 'updated_at')->get();
  }

  public function get($id) {
    return Recipe::where('id', $id)->firstOrFail();
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
