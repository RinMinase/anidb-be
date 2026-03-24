<?php

namespace App\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
  schema: "RecipeAddEditRequest",
  required: ["title"],
  properties: [
    new OA\Property(property: "title", type: "string", example: "Sample Title"),
    new OA\Property(
      property: "description",
      type: "string",
      nullable: true,
      example: "Sample description"
    ),
    new OA\Property(
      property: "ingredients",
      type: "array",
      items: new OA\Items(type: "string"),
      example: ["Ingredient 1", "Ingredient 2", "Ingredient 3"]
    ),
    new OA\Property(
      property: "instructions",
      type: "string",
      nullable: true,
      example: "1. Boil water... 2. Add salt..."
    )
  ]
)]
class AddEditRequest extends FormRequest {

  public function rules() {
    return [
      'title' => ['required', 'string', 'max:127'],
      'description' => ['nullable', 'string', 'max:255'],
      'ingredients' => ['nullable', 'array'],
      'ingredients.*' => ['string'],
      'instructions' => ['nullable', 'string'],
    ];
  }

  protected function passedValidation(): void {
    $this->merge([
      'ingredients' => $this->ingredients ?? [],
    ]);
  }
}
