<?php
namespace JenkinsLogAnalyzer;

/**
 * Description of ErrorStore
 *
 * @author User
 */
class ErrorStore {

    public $errors_hash = array();

    function register($error)
    {
        if (!array_key_exists($error->key, $this->errors_hash)) {
            $this->errors_hash[$error->key] = $error;
        } else {
            $this->errors_hash[$error->key]->register($error->time);
        }
    }

    function count()
    {
        if (!isset($this->_php_error_count)) {
            $this->_php_error_count = 0;
            foreach ($this->errors_hash as $error) {
                $this->_php_error_count += $error->occurence_count;
            }
        }
        return $this->_php_error_count;
    }

    function uniqueCount()
    {
        return sizeof($this->errors_hash);
    }

}

