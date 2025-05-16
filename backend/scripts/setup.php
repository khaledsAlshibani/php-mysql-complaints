<?php

echo "Running database migrations...\n";
passthru('php scripts/phinx.php migrate');

echo "\nInitializing admin user...\n";
passthru('php scripts/phinx.php seed:run -s AdminUserSeeder');
