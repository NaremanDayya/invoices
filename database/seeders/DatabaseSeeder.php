<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@fawateer.com',
        ]);

        // Run all seeders in order
        $this->call([
            ClientsTableSeeder::class,
            ServicesTableSeeder::class,
            InvoicesTableSeeder::class,
            PaymentsTableSeeder::class,
            CreditNotesTableSeeder::class,
        ]);

        $this->command->info('✅ All seeders completed successfully!');
        $this->command->info('📊 Database populated with comprehensive test data.');
    }
}
