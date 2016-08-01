<?php

require '../vendor/autoload.php';

use Hunter\Hunter;

loadConfig();

$hunter = new Hunter();

exec("ls $_ENV[PROBLEMS_DIR]", $ls);

$problems = array();
foreach ($ls as $file) {
    if (preg_match($_ENV['FILE_REGEX_PATTERN'], $file) === 1) {
        $problems[] = $file;
    }
}

if (!empty($problems)) {
    $filename = $problems[0];
    preg_match($_ENV['PROBLEM_NUMBER_REGEX_PATTERN'], $filename, $number);
    $number = $number[0];

    $problem = $hunter->problem($number, 'num');
    $title = $problem['title'];
    $id = $problem['id'];

    if ($title !== null) {
        $message = commitMessageFormatter($number, $title, $id);
    } else { // The file problem number does not correspond to any UVa problem number. We'll use the filename as the commit message.
        $message = $filename;
    }

    // Perform the commit.
    exec("git -C $_ENV[REPOSITORY_DIR] pull origin master");
    exec("mv $_ENV[PROBLEMS_DIR]$filename $_ENV[REPOSITORY_DIR]");
    exec("git -C $_ENV[REPOSITORY_DIR] add $filename");
    exec("git -C $_ENV[REPOSITORY_DIR] commit -m \"$message\"");
    exec("git -C $_ENV[REPOSITORY_DIR] push origin master");
} else if ($_ENV['NOTIFICATIONS']) {
    mail($_ENV['EMAIL'], $_ENV['SUBJECT'], $_ENV['MESSAGE']);
}

function commitMessageFormatter($number, $title, $id = null)
{
    $commit = sprintf($_ENV['COMMIT_TITLE_PATTERN'], $number, $title);
    if ($_ENV['COMMIT_WITH_URL'] && $id !== null) {
        $commit .= sprintf("\nhttps://uva.onlinejudge.org/index.php?option=onlinejudge&page=show_problem&problem=%d", $id);
    }

    return $commit;
}

function loadConfig()
{
    $dotenv = new Dotenv\Dotenv('../');
    $dotenv->load();
    $dotenv->required([
        'REPOSITORY_DIR',
        'PROBLEMS_DIR',
        'FILE_REGEX_PATTERN',
        'PROBLEM_NUMBER_REGEX_PATTERN',
        'COMMIT_TITLE_PATTERN'
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