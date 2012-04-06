#!env php
<?php

$log_files = parseCommandLine();
 
if (empty($log_files)) {
  echo "Usage: jenkins.php <log_file> <log_file> ...\n";
  die; 
}

foreach ($log_files as $log_filename) {
  
  $log_file = fopen($log_filename,'r');
  
  $error_times = array();
  $error_counts = array();
  
  $total_count = 0;
  $php_count = 0;
  $unique_count = 0;
  
  while ($line = trim(fgets($log_file))) {
    ++$total_count;
    if (preg_match('@^\[([^\]]+)] \[[^\]]+] \[[^\]]+] PHP ([^:]+): (.+) in (.+) on line (\d+)(, referer: (.+)|)$@',$line,$matches)) {
      //it's a php error message
      ++$php_count;
      
      $error_msg = trim($matches[2]) . "\n" . trim($matches[3]) . "\n" . trim($matches[4]) . "\n" . trim($matches[5]);
      $error_times[$error_msg] = date('d.m.y H:i',strtotime( $matches[1] ));
      if (!isset($error_counts[$error_msg])) {
        $error_counts[$error_msg] = 1;
      } else {
        ++$error_counts[$error_msg];
      }
    }
    
  }
  
  fclose($log_file);

  arsort($error_counts);
  
  $error_messages = array_keys( $error_counts );
  
  $unique_count = count($error_messages);
  
  ?>
  <h1>Jenkins report for <?php echo htmlspecialchars($log_filename);?></h1>
  Total lines in log: <b><?php echo $total_count?></b><br> 
  Lines recognized as PHP errors: <b><?php echo $php_count?></b><br>
  Unique PHP error messages: <b><?php echo $unique_count?></b><br>
  
<?php if ($unique_count == 0) { ?>
  Hooray, no errors!<br>
<?php } else { ?>
  <h2>Error messages</h2>
  <?php foreach ($error_messages as $message) {
    $count = $error_counts[$message];
    $time = $error_times[$message];
   
    $message = explode("\n",$message);
    
    $colors = array(
      'Notice' => '#4a6d00',
      'Error' => '#ff9c00',
      'Warning' => '#dace48',
      'Fatal error' => '#cb0808'
    );
      
    $message_color = isset($colors[$message[0]]) ? $colors[$message[0]] : '#000000';
    
  ?>
    <span style="color:<?php echo $message_color ?>"><b><?php echo htmlspecialchars($message[0]); ?>:</b></span> 
    <?php echo htmlspecialchars($message[1]); ?> 
    <code>[<?php echo htmlspecialchars($message[2]); ?>:<?php echo htmlspecialchars($message[3]); ?>]</code> 
    <span style="color:#aaaaaa">(<?php echo $count;?># 
    @<?php echo $time; ?>)</span><br>
<?php } 
} 
}
?>
<hr>
Report generated at <?php  date_default_timezone_set('UTC'); echo date('r'); ?><br>
<a href="http://github.com/leonid-shevtsov/jenkins.php">Jenkins</a> 0.3 - an PHP-on-Apache error log analyzer<br>
&copy; 2012 <a href="http://leonid.shevtsov.me">Leonid Shevtsov</a> 
<?php 

function parseCommandLine() {
  $log_files = array();
  for ($i = 1; $i < $_SERVER['argc']; ++$i) {
    if ($_SERVER['argv'][$i][0] == '-') {
      $code = $_SERVER['argv'][$i][1];
      $value = substr($_SERVER['argv'][$i],2);
      
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
      $log_files[] = $_SERVER['argv'][$i];
    }
  } 
  return $log_files;
}

?>
