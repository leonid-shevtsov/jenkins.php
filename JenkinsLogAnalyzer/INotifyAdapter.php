<?php
namespace JenkinsLogAnalyzer;

/**
 *
 * @author Alexsandro Souza
 */
interface INotifyAdapter {
    public function notify($report, $logAnalyzer);
}
