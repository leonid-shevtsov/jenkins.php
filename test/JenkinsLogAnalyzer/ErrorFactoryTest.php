<?php
namespace JenkinsLogAnalyzer;

require_once __DIR__ .'/../../jenkins.php';

class ErrorFactoryTest extends \PHPUnit_Framework_TestCase {
  public function testFromLineIncorrect() {
    $factory = new ErrorFactory;
    $error = $factory->fromLine('This is not a PHP error');

    $this->assertEmpty($error);
  }

  public function testFromLineCorrect() {
    $time = date('r', 1000000);
    $type = 'ErrorType';
    $message = 'This is an error message';
    $filename = '/var/www/myfile.php';
    $line = '123';

    $factory = new ErrorFactory;
    $error = $factory->fromLine("[$time] [error] [client 192.168.1.103] PHP $type:  $message in $filename on line $line");

    $this->assertEquals(1000000, $error->occurence_times[0]);
    $this->assertEquals($type, $error->type);
    $this->assertEquals($message, $error->message);
    $this->assertEquals($filename, $error->filename);
    $this->assertEquals($line, $error->line);
  }
}
