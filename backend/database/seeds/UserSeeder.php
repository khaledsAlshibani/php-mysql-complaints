<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $this->table('users')
            ->insert([
                'username' => 'seeder',
                'password' => password_hash('Seeder123!', PASSWORD_DEFAULT),
                'first_name' => 'Seeder',
                'last_name' => 'Tester',
                'birth_date' => date('Y-m-d'),
                'role' => 'user',
                'photo_path' => 'uploads/profiles/defaults/user.webp',
                'created_at' => date('Y-m-d H:i:s')
            ])
            ->save();

            echo "Seeder user created successfully\n";
    }
}
