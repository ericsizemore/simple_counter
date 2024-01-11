<?php

$config = new PhpCsFixer\Config();

$config
    ->setRules([
        '@PER-CS' => true,
        '@PSR2' => true,
        '@PSR12' => true,
        '@PHP83Migration' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    )
;

return $config;
