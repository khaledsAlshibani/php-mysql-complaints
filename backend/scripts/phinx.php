<?php

$cmd = 'php vendor/bin/phinx ' . implode(' ', array_slice($argv, 1));
passthru($cmd);
