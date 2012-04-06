<?php
  
require_once 'jenkins.php';

class JenkinsLogAnalyzer_ErrorTest extends PHPUnit_Framework_TestCase {
  public function testKeyGeneration() {
    $error = new JenkinsLogAnalyzer_Error(array('time'=>1, 'type'=>'type', 'message'=>'message', 'filename'=>'filename', 'line'=>1));

    $this->assertEquals(md5('typemessagefilename1'), $error->key);
  }

  public function testOccurenceCounter() {
    $error = new JenkinsLogAnalyzer_Error(array('time'=>1, 'type'=>'type', 'message'=>'message', 'filename'=>'filename', 'line'=>1));
    $error->register(2);

    $this->assertEquals(2, $error->occurence_count);
    $this->assertEquals(array(1, 2), $error->occurence_times);
  }
  
  public function testFirstAndLastOccurence() { 
    $error = new JenkinsLogAnalyzer_Error(array('time'=>20, 'type'=>'type', 'message'=>'message', 'filename'=>'filename', 'line'=>1));
    $error->register(10);

    $this->assertEquals(10, $error->firstOccurence());
    $this->assertEquals(20, $error->lastOccurence());
  }
}
