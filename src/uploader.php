<?php
use Hunter\Hunter;

require __DIR__."/../vendor/autoload.php";

define(REPOSITORY_DIR, "");
define(PROBLEMS_DIR, ""); // We need two different folders.
define(FILE_REGEX_PATTERN, "/UVA[0-9]{5}.cpp/");
define(PROBLEM_NUMBER_REGEX_PATTERN, "/[0-9]{5}/");

define(NOTIFICATIONS, false);
define(EMAIL, "");
define(SUBJECT, "Empty problems folder");
define(MESSAGE, "You have a problem LOL");

define(COMMIT_MESSAGE_PATTERN, "%d %s");
define(COMMIT_WITH_URL, true);
define(COMMIT_COMMENT_PATTERN, "\n\nhttps://uva.onlinejudge.org/index.php?option=onlinejudge&page=show_problem&problem=%d");

function messageFormatter($number, $title, $id = NULL) {
    $commit = sprintf(COMMIT_MESSAGE_PATTERN, $number, $title);
    if ($id != NULL) $commit .= sprintf(COMMIT_COMMENT_PATTERN, $id);
    return $commit;
}

$hunter = new Hunter();

$ls = array();

exec("ls ".PROBLEMS_DIR, $ls);

$problems = array();

foreach ($ls as $file) {
    if (preg_match(FILE_REGEX_PATTERN, $file) === 1) $problems[] = $file;
}

if (!empty($problems)) {
    preg_match(PROBLEM_NUMBER_REGEX_PATTERN, $problems[0], $number);

    $problem = $hunter->problem($number[0], "num");
    $title = $problem["title"];
    $id = $problem["id"];

    $message = $title != NULL? COMMIT_WITH_URL? messageFormatter($number[0], $title, $id): messageFormatter($number[0], $title): $problems[0];

    exec("git -C ".REPOSITORY_DIR. " pull origin master");
    exec("mv ".PROBLEMS_DIR.$problems[0]." ".REPOSITORY_DIR);
    exec("git -C ".REPOSITORY_DIR. " add ".$problems[0]);
    exec("git -C ".REPOSITORY_DIR. " commit -m "."\"$message\"");
    exec("git -C ".REPOSITORY_DIR. " push origin master");
} else if (NOTIFICATIONS) {
    mail(EMAIL, SUBJECT, MESSAGE);
}
