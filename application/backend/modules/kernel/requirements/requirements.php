<?php

$dbName = [];

if (extension_loaded('pdo_pgsql')) {
    $dbName[] = 'PDO PostgreSQL extension';
}

if (extension_loaded('pdo_mysql')) {
    $dbName[] = 'PDO MySQL extension';
}

if (!extension_loaded('pdo_pgsql') && !extension_loaded('pdo_mysql')) {
    $dbName = ['PDO PostgreSQL extension or PDO MySQL extension'];
}

$requirements = [
    [
        'name' => 'PDO extension',
        'mandatory' => true,
        'condition' => extension_loaded('pdo'),
        'by' => 'All DB-related classes',
    ],
    [
        'name' => implode(' or ', $dbName),
        'mandatory' => true,
        'condition' => extension_loaded('pdo_pgsql') || extension_loaded('pdo_mysql'),
        'by' => 'All DB-related classes',
        'memo' => 'Required for application.'
    ],
    [
        'name' => 'Expose PHP',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff("expose_php"),
        'by' => 'Security reasons',
        'memo' => '"expose_php" should be disabled at php.ini',
    ],
    [
        'name' => 'PHP allow url include',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff("allow_url_include"),
        'by' => 'Security reasons',
        'memo' => '"allow_url_include" should be disabled at php.ini',
    ],
];

if (strpos(strtolower($requirementsChecker->getServerInfo()), 'nginx') !== false) {
    $requirements[] = [
        'name' => 'cgi.fix_pathinfo PHP',
        'mandatory' => false,
        'condition' => $this->requirementsChecker->checkPhpIniOff("cgi.fix_pathinfo"),
        'by' => 'Nginx pathinfo',
        'memo' => '"cgi.fix_pathinfo" should be 0 at php.ini',
    ];
}

return $requirements;