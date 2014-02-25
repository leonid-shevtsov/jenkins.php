<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JenkinsLogAnalyzer_Error
 *
 * @author User
 */


class JenkinsLogAnalyzer_Error {

    public $type;
    public $message;
    public $filename;
    public $line;
    public $occurence_count = 1;
    public $occurence_times = array();
    public $key;

    function __construct($options)
    {
        $this->type = $options['type'];
        $this->message = $options['message'];
        $this->filename = $options['filename'];
        $this->line = $options['line'];
        $this->occurence_times[] = $options['time'];
        $this->key = $this->makeKey();
    }

    public function register($time)
    {
        $this->occurence_times[] = $time;
        $this->occurence_count += 1;
    }

    public function firstOccurence()
    {
        sort($this->occurence_times);
        return $this->occurence_times[0];
    }

    public function lastOccurence()
    {
        sort($this->occurence_times);
        return $this->occurence_times[sizeof($this->occurence_times) - 1];
    }

    private function makeKey()
    {
        return md5($this->type . $this->message . $this->filename . $this->line);
    }

}