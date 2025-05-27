<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class FeedbacksSeeder extends AbstractSeed
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
        $this->table('feedback')
            ->insert([
                // Feedback for complaints (IDs 1-10)
                [
                    'admin_id' => 1,
                    'complaint_id' => 1,
                    'content' => 'We are investigating the file upload crash. Thank you for reporting.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'complaint_id' => 2,
                    'content' => 'Please check your spam folder for the reset email. We are also reviewing our email system.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'complaint_id' => 3,
                    'content' => 'We are working on optimizing dashboard performance.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'complaint_id' => 4,
                    'content' => 'Timeouts are being addressed in the next update.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'complaint_id' => 5,
                    'content' => 'Mobile accessibility improvements are planned.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'complaint_id' => 6,
                    'content' => 'We are looking into the 500 error on form submission.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'complaint_id' => 7,
                    'content' => 'We will improve the search algorithm for better results.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'complaint_id' => 8,
                    'content' => 'Real-time notifications are on our roadmap.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'complaint_id' => 9,
                    'content' => 'We will add the option to change your email soon.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'complaint_id' => 10,
                    'content' => 'We are investigating the unexpected logout issue.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                // Feedback for suggestions (IDs 1-10)
                [
                    'admin_id' => 1,
                    'suggestion_id' => 1,
                    'content' => 'Dark mode is a great idea! We will consider it for a future release.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'suggestion_id' => 2,
                    'content' => 'PDF report downloads are being discussed by the team.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'suggestion_id' => 3,
                    'content' => 'Two-factor authentication is on our security roadmap.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'suggestion_id' => 4,
                    'content' => 'Dashboard customization is a popular request. Stay tuned!',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'suggestion_id' => 5,
                    'content' => 'A search bar for suggestions will be added soon.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'suggestion_id' => 6,
                    'content' => 'Email notifications are coming in the next update.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'suggestion_id' => 7,
                    'content' => 'Exporting to Excel and CSV will be supported.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'suggestion_id' => 8,
                    'content' => 'A FAQ section is being prepared for new users.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 1,
                    'suggestion_id' => 9,
                    'content' => 'Tagging suggestions is a great idea for organization.',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'admin_id' => 2,
                    'suggestion_id' => 10,
                    'content' => 'A mobile app is under consideration for future development.',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ])
            ->save();

        echo "Feedbacks created successfully\n";
    }
}
