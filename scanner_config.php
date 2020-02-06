<?php

declare(strict_types=1);

$projectPath = __DIR__;

$scanDirectories = [
    $projectPath . '/config/',
    $projectPath . '/public/',
    $projectPath . '/src/',
    $projectPath . '/tests/',
];

return [
    'composerJsonPath' => $projectPath . '/composer.json',
    'vendorPath'       => $projectPath . '/vendor/',
    'scanDirectories'  => $scanDirectories,

    'skipPackages' => [
        'symfony/asset',
        'symfony/flex',
        'symfony/intl',
        'symfony/yaml',
    ],
    'extensions'   => ['*.php'],
    'requireDev'   => false,
];
