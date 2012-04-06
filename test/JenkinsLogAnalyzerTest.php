<?php

require_once 'jenkins.php';

class JenkinsLogAnalyzerTest extends PHPUnit_Framework_TestCase {
  public function testProcessingLineByLine() {
    $error_class = $this->getMock('ErrorFactory', array('fromLine'));
    $error_class->expects($this->exactly(3))->method('fromLine');

    $analyzer = new JenkinsLogAnalyzer(fopen("data://text/plain,".urlencode("1\n2\n3"),'r'), $error_class);
    $analyzer->process();
  }
  
  public function testStoringIntoStore() {
    $error_factory = $this->getMock('ErrorFactory', array('fromLine'));
    $error_factory->expects($this->any())->method('fromLine')->will($this->returnValue('TEST ERROR'));

    $error_store = $this->getMock('ErrorStore', array('register'));
    $error_store->expects($this->once())->method('register')->with($this->equalTo('TEST ERROR'));

    $analyzer = new JenkinsLogAnalyzer(fopen("data://text/plain,".urlencode("1"),'r'), $error_factory, $error_store);
    $analyzer->process();
  }

  public function testLineCount() {
    $error_class = $this->getMock('ErrorFactory', array('fromLine'));
    $error_class->expects($this->any())->method('fromLine');

    $analyzer = new JenkinsLogAnalyzer(fopen("data://text/plain,".urlencode("1\n2\n3"),'r'), $error_class);
    $analyzer->process();

    $this->assertEquals(3, $analyzer->line_count);
  }
}
