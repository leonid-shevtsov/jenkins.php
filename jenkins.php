<?php
require_once __DIR__. '/INotifyAdapter.php';
require_once __DIR__. '/MailAdapter.php';
require_once  __DIR__.'/JenkinsLogAnalyzer.php';
require_once  __DIR__.'/JenkinsLogAnalyzer_CLI.php';
require_once  __DIR__.'/JenkinsLogAnalyzer_Error.php';
require_once  __DIR__.'/JenkinsLogAnalyzer_ErrorFactory.php';
require_once  __DIR__.'/JenkinsLogAnalyzer_ErrorStore.php';
require_once  __DIR__.'/JenkinsLogAnalyzer_HtmlGenerator.php';


if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    $cli = new JenkinsLogAnalyzer_CLI($_SERVER['argc'], $_SERVER['argv'], new MailAdapter);
    exit($cli->run());
}

?>
