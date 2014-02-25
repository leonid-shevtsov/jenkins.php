<?php


/**
 * Description of MailAdapter
 *
 * @author Alexsandro Souza
 */
class PrintAdapter implements NotifyAdapter {
    public function notify($report)
    {
        print $report;
    }
}
