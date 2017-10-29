<?php

$config = require_once __DIR__ . '/../bootstrap.php';

$app = new \CalendarClient\App($config);
$app->run();
