<?php
namespace JenkinsLogAnalyzer;
/**
 * Description of ErrorFactory
 *
 * @author User
 */

class ErrorFactory {

    function fromLine($line)
    {
        preg_match('@^\[([^\]]+)] \[[^\]]+] \[[^\]]+] PHP ([^:]+): (.+) in (.+) on line (\d+)(, referer: (.+)|)$@', $line, $matches);

        if (sizeof($matches) > 0) {
            return new Error(array(
                'time' => strtotime($matches[1]),
                'type' => trim($matches[2]),
                'message' => trim($matches[3]),
                'filename' => trim($matches[4]),
                'line' => trim($matches[5])
            ));
        } else {
            return null;
        }
    }

}
