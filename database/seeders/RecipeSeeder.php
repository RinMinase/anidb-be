<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Recipe::create([
      'title' => 'Classic Fluffy Pancakes',
      'description' => 'A simple, foolproof recipe for the best breakfast pancakes.',
      'ingredients' => [
        '1 ½ cups all-purpose flour',
        '3 ½ tsp baking powder',
        '1 tbsp white sugar',
        '1 ¼ cups milk',
        '1 egg',
        '3 tbsp melted butter'
      ],
      'instructions' => "## Preparation\n1. In a large bowl, sift together the **flour, baking powder, salt and sugar**.\n2. Make a well in the center and pour in the milk, egg and melted butter; mix until smooth.\n\n## Cooking\n* Heat a lightly oiled griddle or frying pan over medium-high heat.\n* Pour or scoop the batter onto the griddle, using approximately 1/4 cup for each pancake.\n* Brown on both sides and serve hot with **maple syrup**.",
    ]);

    Recipe::create([
      'title' => 'Quick Garlic Aglio e Olio',
      'description' => 'The ultimate "I have nothing in the fridge" pasta dish.',
      'ingredients' => [
        '200g Spaghetti',
        '4 cloves Garlic, thinly sliced',
        '1/2 cup Extra virgin olive oil',
        '1 tsp Red pepper flakes',
        'Fresh Parsley'
      ],
      'instructions' => "### Step 1\nBring a large pot of lightly salted water to a boil. Cook spaghetti in the boiling water, stirring occasionally, until cooked through but firm to the bite.\n\n### Step 2\nWhile pasta is cooking, heat olive oil in a skillet over medium heat. Sauté garlic until lightly browned. **Watch closely so it doesn't burn!**\n\n### Step 3\nStir in red pepper flakes. Drain pasta and toss with the garlic oil and fresh parsley.",
    ]);
  }
}
