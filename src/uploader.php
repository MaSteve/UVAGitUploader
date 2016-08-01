<?php

require __DIR__."/../vendor/autoload.php";

use Hunter\Hunter;

loadConfig();

function messageFormatter($number, $title, $id = NULL) {
    $commit = sprintf($_ENV['COMMIT_MESSAGE_PATTERN'], $number, $title);
    if ($id != NULL) $commit .= sprintf($_ENV['COMMIT_COMMENT_PATTERN'], $id);
    return $commit;
}

$hunter = new Hunter();

$ls = array();

exec("ls ".$_ENV['PROBLEMS_DIR'], $ls);

$problems = array();

foreach ($ls as $file) {
    if (preg_match($_ENV['FILE_REGEX_PATTERN'], $file) === 1) $problems[] = $file;
}

if (!empty($problems)) {
    preg_match($_ENV['PROBLEM_NUMBER_REGEX_PATTERN'], $problems[0], $number);

    $problem = $hunter->problem($number[0], "num");
    $title = $problem["title"];
    $id = $problem["id"];

    $message = $title != NULL? $_ENV['COMMIT_WITH_URL'] ? messageFormatter($number[0], $title, $id): messageFormatter($number[0], $title): $problems[0];

    exec("git -C ".$_ENV['REPOSITORY_DIR']. " pull origin master");
    exec("mv ".$_ENV['PROBLEMS_DIR'].$problems[0]." ".$_ENV['REPOSITORY_DIR']);
    exec("git -C ".$_ENV['REPOSITORY_DIR']. " add ".$problems[0]);
    exec("git -C ".$_ENV['REPOSITORY_DIR']. " commit -m "."\"$message\"");
    exec("git -C ".$_ENV['REPOSITORY_DIR']. " push origin master");
} else if ($_ENV['NOTIFICATIONS']) {
    mail($_ENV['EMAIL'], $_ENV['SUBJECT'], $_ENV['MESSAGE']);
}

function loadConfig()
{
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
    $dotenv->required([
        'REPOSITORY_DIR',
        'PROBLEMS_DIR',
        'FILE_REGEX_PATTERN',
        'PROBLEM_NUMBER_REGEX_PATTERN',
        'COMMIT_MESSAGE_PATTERN',
        'COMMIT_COMMENT_PATTERN'
    ]);

    $dotenv->required(['NOTIFICATIONS', 'COMMIT_WITH_URL'])
        ->allowedValues(['true', 'false']);

    $_ENV['NOTIFICATIONS'] = ($_ENV['NOTIFICATIONS'] === 'true');
    $_ENV['COMMIT_WITH_URL'] = ($_ENV['COMMIT_WITH_URL'] === 'true');

    if ($_ENV['NOTIFICATIONS']) {
        $dotenv->required([
            'EMAIL',
            'SUBJECT',
            'MESSAGE'
        ]);
    }
}