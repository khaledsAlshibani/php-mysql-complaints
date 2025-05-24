<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AdminUserSeeder extends AbstractSeed
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
        $adminExists = $this->fetchRow('SELECT id FROM users WHERE role = "admin" LIMIT 1');

        if (!$adminExists) {
            $this->table('users')
                ->insert([
                    'username' => 'admin',
                    'password' => password_hash('Admin123!', PASSWORD_DEFAULT),
                    'first_name' => 'Administrator',
                    'birth_date' => date('Y-m-d'),
                    'role' => 'admin',
                    'photo_path' => 'uploads/profiles/defaults/admin.webp',
                    'created_at' => date('Y-m-d H:i:s')
                ])
                ->save();

            echo "Admin user created successfully\n";
        } else {
            echo "Admin user already exists\n";
        }
    }
}
