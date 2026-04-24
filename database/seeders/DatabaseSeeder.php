<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin EcoShop',
            'email' => 'admin@ecoshop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Client User
        User::create([
            'name' => 'Jean Client',
            'email' => 'client@ecoshop.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        // Categories
        $hygiene = Category::create(['name' => 'Hygiène & Soins']);
        $cuisine = Category::create(['name' => 'Cuisine Zéro Déchet']);
        $maison = Category::create(['name' => 'Maison Durable']);

        // Products
        Product::create([
            'name' => 'Savon Bio au Miel',
            'description' => 'Un savon artisanal fabriqué avec du miel local et des huiles bio. Doux pour la peau et pour la planète.',
            'price' => 5.90,
            'stock' => 50,
            'category_id' => $hygiene->id,
            'image' => 'https://images.unsplash.com/photo-1600857062241-98e5dba7f214?q=80&w=2000&auto=format&fit=crop'
        ]);

        Product::create([
            'name' => 'Brosse à Dents en Bambou',
            'description' => 'Alternative écologique au plastique, cette brosse à dents en bambou est biodégradable.',
            'price' => 3.50,
            'stock' => 100,
            'category_id' => $hygiene->id,
            'image' => 'https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?q=80&w=2000&auto=format&fit=crop'
        ]);

        Product::create([
            'name' => 'Sacs à Vrac en Coton',
            'description' => 'Lot de 5 sacs en coton bio pour faire vos courses sans emballages plastiques.',
            'price' => 12.00,
            'stock' => 30,
            'category_id' => $cuisine->id,
            'image' => 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?q=80&w=2000&auto=format&fit=crop'
        ]);

        Product::create([
            'name' => 'Gourde Inox 750ml',
            'description' => 'Gourde réutilisable en acier inoxydable, garde vos boissons fraîches pendant 24h.',
            'price' => 24.90,
            'stock' => 20,
            'category_id' => $cuisine->id,
            'image' => 'https://images.unsplash.com/photo-1602143352538-4663f8202c6b?q=80&w=2000&auto=format&fit=crop'
        ]);
    }
}
