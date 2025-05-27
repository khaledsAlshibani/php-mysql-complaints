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
                [
                    'id' => 2,
                    'username' => 'alice',
                    'first_name' => 'Alice',
                    'last_name' => 'Smith',
                    'birth_date' => '1990-01-01',
                    'photo_path' => 'uploads/profiles/defaults/user.webp',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'username' => 'bob',
                    'first_name' => 'Bob',
                    'last_name' => 'Johnson',
                    'birth_date' => '1991-02-02',
                    'photo_path' => 'uploads/profiles/defaults/user.webp',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 4,
                    'username' => 'carol',
                    'first_name' => 'Carol',
                    'last_name' => 'Williams',
                    'birth_date' => '1992-03-03',
                    'photo_path' => 'uploads/profiles/defaults/user.webp',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 5,
                    'username' => 'david',
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'birth_date' => '1993-04-04',
                    'photo_path' => 'uploads/profiles/defaults/user.webp',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 6,
                    'username' => 'eve',
                    'first_name' => 'Eve',
                    'last_name' => 'Davis',
                    'birth_date' => '1994-05-05',
                    'photo_path' => 'uploads/profiles/defaults/user.webp',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 7,
                    'username' => 'frank',
                    'first_name' => 'Frank',
                    'last_name' => 'Miller',
                    'birth_date' => '1995-06-06',
                    'photo_path' => 'uploads/profiles/defaults/user.webp',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ])
            ->save();

        echo "Seeder users created successfully\n";
    }
}
