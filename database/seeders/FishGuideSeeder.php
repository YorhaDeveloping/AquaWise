<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FishGuide;
use Illuminate\Database\Seeder;

class FishGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the expert user
        $expert = User::where('email', 'expert@aquawise.com')->first();

        // Create published guides
        FishGuide::create([
            'user_id' => $expert->id,
            'title' => 'Complete Guide to Betta Fish Care',
            'description' => 'Everything you need to know about caring for Betta fish, from tank setup to feeding.',
            'fish_species' => 'Betta splendens',
            'care_instructions' => 'Bettas need at least a 5-gallon tank with a filter and heater. They are labyrinth fish and need access to surface air.',
            'feeding_guide' => 'Feed 2-3 pellets twice daily. Can supplement with frozen bloodworms or brine shrimp.',
            'water_parameters' => [
                'temperature' => '76-82°F',
                'ph' => '6.5-7.5',
                'hardness' => '5-20 dGH'
            ],
            'common_diseases' => 'Fin rot, velvet, ich',
            'prevention_tips' => 'Regular water changes, proper feeding, and maintaining water parameters',
            'status' => 'published',
            'views' => 150
        ]);

        FishGuide::create([
            'user_id' => $expert->id,
            'title' => 'Guppy Breeding Guide',
            'description' => 'A comprehensive guide to breeding guppies successfully.',
            'fish_species' => 'Poecilia reticulata',
            'care_instructions' => 'Guppies need a well-planted tank with plenty of hiding spots. Minimum 10-gallon tank recommended.',
            'feeding_guide' => 'Feed high-quality flakes and live foods for breeding conditioning.',
            'water_parameters' => [
                'temperature' => '72-82°F',
                'ph' => '7.0-8.0',
                'hardness' => '8-12 dGH'
            ],
            'common_diseases' => 'Fungal infections, parasites',
            'prevention_tips' => 'Maintain clean water, avoid overcrowding',
            'status' => 'published',
            'views' => 200
        ]);

        // Create a draft guide
        FishGuide::create([
            'user_id' => $expert->id,
            'title' => 'Goldfish Care Basics',
            'description' => 'Basic care guide for keeping goldfish healthy and happy.',
            'fish_species' => 'Carassius auratus',
            'care_instructions' => 'Goldfish need large tanks or ponds. Single-tailed varieties need at least 55 gallons.',
            'feeding_guide' => 'Feed quality goldfish food 2-3 times daily. Include vegetables in diet.',
            'water_parameters' => [
                'temperature' => '65-75°F',
                'ph' => '7.0-8.4',
                'hardness' => '5-19 dGH'
            ],
            'common_diseases' => 'Swim bladder disease, white spot disease',
            'prevention_tips' => 'Avoid overfeeding, maintain good filtration',
            'status' => 'draft',
            'views' => 0
        ]);
    }
}
