<?php
namespace JenkinsLogAnalyzer;

require_once __DIR__ .'/../../jenkins.php';

class ErrorTest extends \PHPUnit_Framework_TestCase {
  public function testKeyGeneration() {
    $error = new Error(array('time'=>1, 'type'=>'type', 'message'=>'message', 'filename'=>'filename', 'line'=>1));

    $this->assertEquals(md5('typemessagefilename1'), $error->key);
  }

  public function testOccurenceCounter() {
    $error = new Error(array('time'=>1, 'type'=>'type', 'message'=>'message', 'filename'=>'filename', 'line'=>1));
    $error->register(2);

    $this->assertEquals(2, $error->occurence_count);
    $this->assertEquals(array(1, 2), $error->occurence_times);
  }
  
  public function testFirstAndLastOccurence() { 
    $error = new Error(array('time'=>20, 'type'=>'type', 'message'=>'message', 'filename'=>'filename', 'line'=>1));
    $error->register(10);

    $this->assertEquals(10, $error->firstOccurence());
    $this->assertEquals(20, $error->lastOccurence());
  }
}
