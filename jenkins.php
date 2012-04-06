#!/usr/local/bin/php
<?php

//Jenkins 0.2a - a php-on-apache log analyzer
//by coldFlame [http://coldflame.in.ua]
//Feel free to send comments or suggestions to [me@coldflame.in.ua]
//Feel free to modify the script to suit your personal needs.
//Do not redistribute the script in any form.
//
//Crontab line to run Jenkins daily, rotate logs, and send a report to your morning mail:
//05 00 * * * www ~/scripts/jenkins.php -mme@coldflame.in.ua -r/var/log/apache2/*.error.log


//Usage: jenkins.php <errorlog> <errorlog> ...
//options: -m<email_address> emails the log instead of displaying it
//         -r<file_mask> emails the log instead of displaying it
$log_files = array();

for ($i = 1; $i < $_SERVER['argc']; ++$i) {
	if ($_SERVER['argv'][$i][0] == '-') {
		$code = $_SERVER['argv'][$i][1];
		$value = substr($_SERVER['argv'][$i],2);
		
		switch($code) {
		case 'm':
			$mailto = $value;
			break;
		case 'r':
			$rotatelogmask = $value;
			break; 
		default:
			echo "Unrecognized parameter: -$code\n";
			die;
		}
	} else {
		$log_files[] = $_SERVER['argv'][$i];
	}
} 
 
if (isset($rotatelogmask)) {
	
	$glob = glob($rotatelogmask);
	
	//rotate logs
	foreach ($glob as $filename) {
		$filename = strval($filename);
	    if (is_file($filename)) {
			$new_log_filename = $filename.'.j'; 
			if (is_file($new_log_filename)) {
				unlink($new_log_filename);
			}
			
			rename($filename, $new_log_filename);
			$log_files[] = $new_log_filename;
		} 
	}
	
	//HUP the apache server
	$pid = intval(trim(file_get_contents('/var/run/httpd.pid')));
	posix_kill($pid, 1);
}
 
if (empty($log_files)) {
	echo "Usage: jenkins.php <log_file> <log_file> ...\n";
	echo "    -m<email> - send report to <email> instead of displaying it\n";
	echo "    -r<file_mask> - rotate logs and sighup apache";
	die; 
}
 
if (isset($mailto)) {
	ob_start();
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
Report generated at <?php echo date('r'); ?><br>
<a href="http://coldflame.in.ua/jenkins">Jenkins</a> 0.2a - an PHP-on-Apache error log analyzer<br>
&copy; 2008 <a href="http://coldflame.in.ua">coldFlame</a> 
<?php 

if (isset($mailto)) {
	$contents = ob_get_contents();
	ob_end_clean();
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	mail($mailto, 'Jenkins analyzer report for '.date('d.m.Y'), $contents, $headers);
}

?>