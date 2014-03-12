<?php
namespace JenkinsLogAnalyzer;

/**
 * Description of JenkinsLogAnalyzer
 *
 * @author User
 */

class LogAnalyzer {

    const VERSION = '0.3';

    public $errors;
    public $line_count = 0;

    function __construct($log_stream, $error_factory = null, $error_store = null)
    {
        $this->log_stream = $log_stream;
        $this->error_factory = $error_factory ? $error_factory : new ErrorFactory();
        $this->errors = $error_store ? $error_store : new ErrorStore();
    }

    function process()
    {
        while ($line = trim(fgets($this->log_stream))) {
            $this->line_count += 1;
            $this->processLine($line);
        }
    }

    private function processLine($line)
    {
        if ($error = $this->error_factory->fromLine($line)) {
            $this->errors->register($error);
        }
    }

}
