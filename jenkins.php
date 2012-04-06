#!env php
<?php

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
  $cli = new JenkinsLogAnalyzer_CLI($_SERVER['argc'], $_SERVER['argv']);
  exit($cli->run());
}

  

class JenkinsLogAnalyzer {
  const VERSION = '0.3';

  public $errors;
  public $line_count = 0;

  function __construct($log_stream, $error_factory = null, $error_store = null) {
    $this->log_stream = $log_stream;
    $this->error_factory = $error_factory ? $error_factory : new JenkinsLogAnalyzer_ErrorFactory();
    $this->errors = $error_store ? $error_store : new JenkinsLogAnalyzer_ErrorStore();
  }

  function process() {
    while ($line = trim(fgets($this->log_stream))) {
      $this->line_count += 1;
      $this->processLine($line);
    }
  }
  
  private function processLine($line) {
    if ($error = $this->error_factory->fromLine($line)) {
      $this->errors->register($error);
    }
  }
}

class JenkinsLogAnalyzer_ErrorStore {
  public $errors_hash = array();

  function register($error) {
    if (!array_key_exists($error->key, $this->errors_hash)) {
      $this->errors_hash[$error->key] = $error;
    } else {
      $this->errors_hash[$error->key]->register($error->time);
    }
  }
  
  function count() {
    if (!isset($this->_php_error_count)) {
      $this->_php_error_count=0;
      foreach($this->errors_hash as $error) {
        $this->_php_error_count += $error->occurence_count;
      }
    }
    return $this->_php_error_count;
  }

  function uniqueCount() {
    return sizeof($this->errors_hash);
  }
}

class JenkinsLogAnalyzer_Error {
  public $type;
  public $message;
  public $filename;
  public $line;
  public $occurence_count = 1;
  public $occurence_times = array();
  public $key;

  function __construct($options) {
    $this->type = $options['type'];
    $this->message = $options['message'];
    $this->filename = $options['filename'];
    $this->line = $options['line'];
    $this->occurence_times[]= $options['time'];
    $this->key = $this->makeKey();
  }

  public function register($time) {
    $this->occurence_times[]= $time;
    $this->occurence_count += 1;
  }

  public function firstOccurence() {
    sort($this->occurence_times);
    return $this->occurence_times[0];
  }
  
  public function lastOccurence() {
    sort($this->occurence_times);
    return $this->occurence_times[sizeof($this->occurence_times)-1];
  }

  private function makeKey() {
    return md5( $this->type . $this->message . $this->filename . $this->line );
  }
}

class JenkinsLogAnalyzer_ErrorFactory {
  function fromLine($line) {
    preg_match('@^\[([^\]]+)] \[[^\]]+] \[[^\]]+] PHP ([^:]+): (.+) in (.+) on line (\d+)(, referer: (.+)|)$@',$line,$matches);

    if (sizeof($matches) > 0) {
      return new JenkinsLogAnalyzer_Error(array(
        'time'  => strtotime($matches[1]),
        'type' => trim($matches[2]),
        'message'  => trim($matches[3]),
        'filename'  => trim($matches[4]),
        'line'  => trim($matches[5])
      ));
    } else {
      return null;
    }
  }
}

class JenkinsLogAnalyzer_HtmlGenerator {
  private $colors = array(
    'Notice' => '#4a6d00',
    'Error' => '#ff9c00',
    'Warning' => '#dace48',
    'Fatal error' => '#cb0808'
  );

  function __construct($log_filename, $log_analyzer) {
    $this->log_filename = $log_filename;
    $this->log_analyzer = $log_analyzer;
    $this->errors = $log_analyzer->errors;
  }

  function generateReport() {
    ob_start();
    ?>
      <h1>Jenkins report for <?php echo htmlspecialchars($this->log_filename);?></h1>

      Total lines in log: <b><?php echo $this->log_analyzer->line_count ?></b><br> 
      Lines recognized as PHP errors: <b><?php echo $this->errors->count() ?></b><br>
      Unique PHP error messages: <b><?php echo $this->errors->uniqueCount() ?></b><br>
      
      <?php if ($this->errors->uniqueCount() == 0) { ?>
        Hooray, no errors!<br>
      <?php } else { ?>
        <h2>Error messages</h2>
        <?php foreach ($this->errors->errors_hash as $error) { ?>
          <span style="color:<?php echo $this->errorColor($error) ?>"><b><?php echo htmlspecialchars($error->type); ?>:</b></span> 
          <?php echo htmlspecialchars($error->message); ?> 
          <code>[<?php echo htmlspecialchars($error->filename); ?>:<?php echo htmlspecialchars($error->line); ?>]</code> 
          <span style="color:#aaaaaa">(<?php echo $error->occurence_count;?># 
          @<?php echo date('d.m.y H:i', $error->lastOccurence()); ?>)</span><br>
        <?php } 
      } ?>
      <hr>
      Report generated at <?php echo date('r'); ?><br>
      <a href="http://github.com/leonid-shevtsov/jenkins.php">Jenkins</a> <?php echo JenkinsLogAnalyzer::VERSION ?> - an PHP-on-Apache error log analyzer<br>
      &copy; 2012 <a href="http://leonid.shevtsov.me">Leonid Shevtsov</a> 
    <?php
    $report = ob_get_contents();
    ob_end_clean();
    return $report;
  }

  private function errorColor($error) {
    return isset($this->colors[$error->type]) ? $this->colors[$error->type] : '#000000';
  }
}

class JenkinsLogAnalyzer_CLI {
  function __construct($argc, $argv) {
    $this->argc = $argc;
    $this->argv = $argv;
  }

  function run() {
    $this->parseCommandLine();
    if ($this->log_files) {
      foreach ($this->log_files as $log_file) {
        $this->processLogFile($log_file);
      }
      return 0;
    } else {
      $this->printBanner();
      return -1;
    }
  }

  private function parseCommandLine() {
    $log_files = array();
    for ($i = 1; $i < $this->argc; ++$i) {
      if ($this->argv[$i][0] == '-') {
        $code = $this->argv[$i][1];
        $value = substr($this->argv[$i],2);
        
        switch($code) {
        case 'm':
          echo "DEPRECATED - use mail command to mail logs";
          die;
          break;
        case 'r':
          echo "DEPRECATED - use logrotate";
          die;
          break; 
        default:
          echo "Unrecognized parameter: -$code\n";
          die;
        }
      } else {
        $log_files[] = $this->argv[$i];
      }
    } 
    return $this->log_files = $log_files;
  }

  private function printBanner() {
    echo "Usage: jenkins.php <log_file> <log_file> ...\n";
  }

  private function processLogFile($log_filename) {
    $log_file = fopen($log_filename,'r');
    $analyzer = new JenkinsLogAnalyzer($log_file);
    $analyzer->process();
    fclose($log_file);

    $generator = new JenkinsLogAnalyzer_HtmlGenerator($log_filename, $analyzer);
    print $generator->generateReport(); 
  }
}

?>
