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
      'image_id' => 'aa69aa35-ae15-4ebb-8752-e00b7116706e',
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
      'image_id' => 'caed4605-f06d-41da-bb27-373ac0622b49',
    ]);

    Recipe::create([
      'title' => 'Honey Garlic Glazed Salmon',
      'description' => 'Sweet, savory, and perfectly caramelized salmon fillets.',
      'ingredients' => [
        '2 Salmon fillets',
        '3 tbsp Honey',
        '2 tbsp Soy sauce',
        '1 tbsp Lemon juice',
        '3 cloves Garlic, minced'
      ],
      'instructions' => "### Step 1\nIn a small bowl, whisk together honey, soy sauce, lemon juice, and minced garlic.\n\n### Step 2\nHeat a skillet over medium-high heat with a touch of oil. Sear the salmon skin-side up for **4 minutes** until a crust forms. Flip the fillets.\n\n### Step 3\nPour the honey garlic sauce into the pan. Let it bubble and thicken while spooning the glaze over the salmon for another 3 minutes.",
      'image_id' => 'c13a2eb3-037d-4adf-b472-4de876241c27',
    ]);

    Recipe::create([
      'title' => 'Homemade Roasted Red Pepper Hummus',
      'description' => 'Way better than store-bought and incredibly smooth.',
      'ingredients' => [
        '1 can (400g) Chickpeas, drained',
        '1/2 cup Roasted red peppers (from a jar)',
        '1/4 cup Tahini',
        '2 tbsp Olive oil',
        '1 Lemon, juiced',
        '1 clove Garlic'
      ],
      'instructions' => "### Step 1\nPlace the chickpeas, tahini, lemon juice, and garlic in a food processor. Pulse until the mixture starts to break down.\n\n### Step 2\nAdd the roasted red peppers and olive oil. **Process on high for 2 minutes** until the texture is completely smooth and creamy.\n\n### Step 3\nTaste and adjust salt as needed. Serve with pita bread or fresh vegetable sticks.",
      'image_id' => '6d265d65-a2a3-4c0a-99a7-3029c8e909a5',
    ]);

    Recipe::create([
      'title' => 'Classic Tomato Basil Soup',
      'description' => 'A warm, velvety soup that pairs perfectly with grilled cheese.',
      'ingredients' => [
        '800g Canned crushed tomatoes',
        '1 White onion, diced',
        '2 cups Vegetable broth',
        '1/2 cup Heavy cream',
        'Handful of fresh Basil leaves',
        '2 tbsp Butter'
      ],
      'instructions' => "### Step 1\nMelt butter in a large pot over medium heat. Sauté the onion until translucent and soft (about 5 minutes).\n\n### Step 2\nAdd the tomatoes and broth. Bring to a boil, then reduce heat and simmer for **20 minutes** to let the flavors develop.\n\n### Step 3\nStir in the fresh basil and use an immersion blender to purée until smooth. Stir in the heavy cream right before serving.",
      'image_id' => 'a83e5a39-1532-4c8a-83f6-e2cfc097bc46',
    ]);
  }
}
