<?php

require_once __DIR__ . '/Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

use JenkinsLogAnalyzer\INotifyAdapter;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

/**
 * Class adapter for notify by Email
 *
 * @author Alexsandro Souza
 */
class MailAdapter implements INotifyAdapter {

    private $message;
    private $transport;

    private function hasFatalError($logAnalyzer)
    {
        if (!is_array($logAnalyzer->errors->errors_hash)) {
            return false;
        }

        foreach ($logAnalyzer->errors->errors_hash as $log) {
            if ($log->type == 'Fatal error') {
                return true;
            }
        }

        return false;
    }

    public function notify($html, $logAnalyzer)
    {
        if ($this->hasFatalError($logAnalyzer)) {
            $this->setMessage($html)
                    ->setTransport();
            try {
                $this->transport->send($this->message);
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }
    }

    private function setMessage($html)
    {
        $this->message = new Message();
        $this->message->addFrom("alex@agenciasalve.com.br", "Alexsandro Souza")
                ->addTo("")
                ->addCc("")
                ->addReplyTo("", "Alex")
                ->setSender("", "Salve")
                ->setEncoding("UTF-8")
                ->setSubject("Erro no site salveqa.");

        $html = new MimePart($html);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $this->message->setBody($body);
        return $this;
    }

    public function setTransport()
    {
        // Setup SMTP transport using LOGIN authentication
        $this->transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => 'agenciasalve',
            'host' => 'smtp.agenciasalve.com.br',
            'port' => 587,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => '',
                'password' => '',
//                'ssl'      => 'tls',
            ),
        ));
        $this->transport->setOptions($options);
        return $this;
    }

}
