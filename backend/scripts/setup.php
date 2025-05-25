<?php

echo "Running database migrations...\n";
passthru('php scripts/phinx.php migrate');

echo "\nInitializing admin user...\n";
passthru('php scripts/phinx.php seed:run -s AdminUserSeeder -s UserSeeder -s ComplaintsSeeder -s SuggestionsSeeder -s FeedbacksSeeder');
