<?php
require_once __DIR__. '/JenkinsLogAnalyzer/INotifyAdapter.php';
require_once __DIR__. '/MailAdapter.php';
require_once __DIR__. '/PrintAdapter.php';
require_once  __DIR__.'/JenkinsLogAnalyzer/LogAnalyzer.php';
require_once  __DIR__.'/JenkinsLogAnalyzer/CLI.php';
require_once  __DIR__.'/JenkinsLogAnalyzer/Error.php';
require_once  __DIR__.'/JenkinsLogAnalyzer/ErrorFactory.php';
require_once  __DIR__.'/JenkinsLogAnalyzer/ErrorStore.php';
require_once  __DIR__.'/JenkinsLogAnalyzer/HtmlGenerator.php';


if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    $cli = new \JenkinsLogAnalyzer\CLI($_SERVER['argc'], $_SERVER['argv'], new MailAdapter);
    exit($cli->run(60));
}

?>
