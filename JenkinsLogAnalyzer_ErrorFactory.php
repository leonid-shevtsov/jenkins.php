<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JenkinsLogAnalyzer_ErrorFactory
 *
 * @author User
 */

class JenkinsLogAnalyzer_ErrorFactory {

    function fromLine($line)
    {
        preg_match('@^\[([^\]]+)] \[[^\]]+] \[[^\]]+] PHP ([^:]+): (.+) in (.+) on line (\d+)(, referer: (.+)|)$@', $line, $matches);

        if (sizeof($matches) > 0) {
            return new JenkinsLogAnalyzer_Error(array(
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
