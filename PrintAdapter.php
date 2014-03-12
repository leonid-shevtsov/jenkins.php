<?php
use JenkinsLogAnalyzer\INotifyAdapter;

/**
 * Class adapter for notify onlu Printing the report
 *
 * @author Alexsandro Souza
 */
class PrintAdapter implements INotifyAdapter {
    public function notify($report, $logAnalyzer)
    {
        print $report;
    }
}
