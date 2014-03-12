<?php
namespace JenkinsLogAnalyzer;

/**
 * Description of JenkinsLogAnalyzer_HtmlGenerator
 *
 * @author User
 */

class HtmlGenerator {

    private $colors = array(
        'Notice' => '#4a6d00',
        'Error' => '#ff9c00',
        'Warning' => '#dace48',
        'Fatal error' => '#cb0808'
    );

    function __construct($log_filename, $log_analyzer)
    {
        $this->log_filename = $log_filename;
        $this->log_analyzer = $log_analyzer;
        $this->errors = $log_analyzer->errors;
    }

    function generateReport()
    {
        ob_start();
        ?>
        <h1>Jenkins report for <?php echo htmlspecialchars($this->log_filename); ?></h1>

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
                <span style="color:#aaaaaa">(<?php echo $error->occurence_count; ?># 
                    @<?php echo date('d.m.y H:i', $error->lastOccurence()); ?>)</span><br>
            <?php }
        }
        ?>
        <hr>
        Report generated at <?php echo date('r'); ?><br>
        <a href="http://github.com/leonid-shevtsov/jenkins.php">Jenkins</a> <?php echo LogAnalyzer::VERSION ?> - an PHP-on-Apache error log analyzer<br>
        &copy; 2012 <a href="http://leonid.shevtsov.me">Leonid Shevtsov</a> 
        <?php
        $report = ob_get_contents();
        ob_end_clean();
        return $report;
    }

    private function errorColor($error)
    {
        return isset($this->colors[$error->type]) ? $this->colors[$error->type] : '#000000';
    }

}

