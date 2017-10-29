<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = \Nette\Neon\Neon::decode(file_get_contents(__DIR__ . '/config/config.neon'));

return $config['parameters'];
