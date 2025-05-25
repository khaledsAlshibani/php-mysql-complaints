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
                'user_id' => 2,
                'content' => 'Suggestion Example 1',
                'created_at' => date('Y-m-d H:i:s')
            ])
            ->save();

        echo "Complaints created successfully\n";
    }
}
