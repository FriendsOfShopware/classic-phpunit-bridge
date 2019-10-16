<?php

$config = $this->loadConfig($this->AppPath() . 'Configs/Default.php');
if (file_exists(SEARCH_DIRECTORY . '/app/config/config.php')) {
    $config = require SEARCH_DIRECTORY . '/app/config/config.php';
}


return array_replace_recursive($config, [
     'front' => [
        'throwExceptions' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    ],
    'errorhandler' => [
        'throwOnRecoverableError' => true,
    ],
    'session' => [
        'unitTestEnabled' => true,
        'name' => 'SHOPWARESID',
        'cookie_lifetime' => 0,
        'use_trans_sid' => false,
        'gc_probability' => 1,
        'gc_divisor' => 100,
        'save_handler' => 'db',
    ],
    'mail' => [
        'type' => 'file',
        'path' => $this->getCacheDir(),
    ],
    'phpsettings' => [
        'error_reporting' => E_ALL,
        'display_errors' => 1,
        'date.timezone' => 'Europe/Berlin',
        'max_execution_time' => 0,
    ],
    'csrfprotection' => [
        'frontend' => false,
        'backend' => false,
    ],
]);
