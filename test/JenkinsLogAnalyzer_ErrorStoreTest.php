<?php

require_once __DIR__ .'/../jenkins.php';

class JenkinsLogAnalyzer_ErrorStoreTest extends PHPUnit_Framework_TestCase {
  public function testRegisterShouldAppendTheError() {
    $store = new JenkinsLogAnalyzer_ErrorStore();

    $error = $this->mockError();
    $store->register($error);

    $this->assertCount(1, $store->errors_hash);
    $this->assertEquals($error, $store->errors_hash[key($store->errors_hash)]);
  }

  public function testTwoErrorsShouldRegisterSeparately() {
    $store = new JenkinsLogAnalyzer_ErrorStore();
    $error1 = $this->mockError('key1');
    $store->register($error1);

    $error2 = $this->mockError('key2');
    $store->register($error2);
    
    $this->assertCount(2, $store->errors_hash);
  }

  public function testSameErrorShouldRegisterIntoTheOldOne() {
    $store = new JenkinsLogAnalyzer_ErrorStore();
    $error1 = $this->mockError('same key');
    $store->register($error1);

    $error2 = $this->mockError('same key');
    $error2->time = 'newtime';

    $error1->expects($this->once())->method('register')->with($this->equalTo($error2->time));
    $store->register($error2);

    $this->assertCount(1, $store->errors_hash);
  }

  public function testCountShouldReturnSumOfAllErrorCounts() {
    $store = new JenkinsLogAnalyzer_ErrorStore();
    $error1 = $this->mockError('key1');
    $error1->occurence_count = 2;
    $store->register($error1);

    $error2 = $this->mockError('key2');
    $error2->occurence_count = 3;
    $store->register($error2);

    $this->assertEquals(5, $store->count());
  }
  
  public function testUniqualCountShouldReturnNumberOfErrors() {
    $store = new JenkinsLogAnalyzer_ErrorStore();
    $error1 = $this->mockError('key1');
    $error1->occurence_count = 2;
    $store->register($error1);

    $error2 = $this->mockError('key2');
    $error2->occurence_count = 3;
    $store->register($error2);

    $this->assertEquals(2, $store->uniqueCount());
  }

  private function mockError($key='key') {
    $error = $this->getMock('Error', array('register'));
    $error->key = $key;
    return $error;
  }
}
