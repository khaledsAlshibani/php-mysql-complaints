<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SuggestionsSeeder extends AbstractSeed
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
        $this->table('suggestions')
            ->insert([
                [
                    'user_id' => 2,
                    'content' => 'Add a dark mode option to improve usability at night.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 3,
                    'content' => 'Provide downloadable PDF reports for user activity.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 4,
                    'content' => 'Enable two-factor authentication for better security.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 2,
                    'content' => 'Allow users to customize their dashboard widgets.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 5,
                    'content' => 'Add a search bar to quickly find previous suggestions.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 3,
                    'content' => 'Implement email notifications for important updates.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 6,
                    'content' => 'Support exporting data to Excel and CSV formats.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 2,
                    'content' => 'Add a FAQ section to help new users get started.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 7,
                    'content' => 'Allow users to tag suggestions for better organization.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 4,
                    'content' => 'Provide a mobile app version for easier access on the go.',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ])
            ->save();

        echo "Suggestions created successfully\n";
    }
}
