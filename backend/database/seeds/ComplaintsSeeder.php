<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ComplaintsSeeder extends AbstractSeed
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
        $this->table('complaints')
            ->insert([
                [
                    'user_id' => 2,
                    'content' => 'The application crashes when I try to upload a large file.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 3,
                    'content' => 'I am not receiving password reset emails.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 4,
                    'content' => 'The dashboard loads very slowly during peak hours.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 5,
                    'content' => 'There are frequent timeouts when saving my profile.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 2,
                    'content' => 'Some buttons are not accessible on mobile devices.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 6,
                    'content' => 'I encountered a 500 error when submitting a form.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 3,
                    'content' => 'The search feature does not return relevant results.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 7,
                    'content' => 'Notifications are not updating in real time.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 4,
                    'content' => 'I cannot change my email address in the settings.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => 2,
                    'content' => 'The app logs me out unexpectedly after a few minutes.',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ])
            ->save();

        echo "Complaints created successfully\n";
    }
}
