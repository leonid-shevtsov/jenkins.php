<?php
namespace JenkinsLogAnalyzer;

/**
 * Description of CLI
 *
 * @author User
 */

class CLI {

    private $notifyAdapter;

    function __construct($argc, $argv, INotifyAdapter $notifyAdapter)
    {
        $this->argc = $argc;
        $this->argv = $argv;
        $this->notifyAdapter = $notifyAdapter;
    }

    function run()
    {
        $this->parseCommandLine();
        if ($this->log_files) {
            foreach ($this->log_files as $log_file) {
                $this->notifyAdapter->notify($this->processLogFile($log_file));
            }
            return 0;
        } else {
            $this->printBanner();
            return -1;
        }
    }

    private function parseCommandLine()
    {
        $log_files = array();
        for ($i = 1; $i < $this->argc; ++$i) {
            if ($this->argv[$i][0] == '-') {
                $code = $this->argv[$i][1];
                $value = substr($this->argv[$i], 2);

                switch ($code) {
                    case 'm':
                        echo "DEPRECATED - use mail command to mail logs";
                        die;
                        break;
                    case 'r':
                        echo "DEPRECATED - use logrotate";
                        die;
                        break;
                    default:
                        echo "Unrecognized parameter: -$code\n";
                        die;
                }
            } else {
                $log_files[] = $this->argv[$i];
            }
        }
        return $this->log_files = $log_files;
    }

    private function printBanner()
    {
        echo "Usage: jenkins.php <log_file> <log_file> ...\n";
    }

    private function processLogFile($log_filename)
    {
        $log_file = fopen($log_filename, 'r');
        $analyzer = new LogAnalyzer($log_file);
        $analyzer->process();
        fclose($log_file);

        $generator = new HtmlGenerator($log_filename, $analyzer);
        return $generator->generateReport();
    }

}
