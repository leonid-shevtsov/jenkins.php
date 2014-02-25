<?php


/**
 * Description of MailAdapter
 *
 * @author Alexsandro Souza
 */
class MailAdapter implements NotifyAdapter {
    public function notify($report)
    {
        echo "Email enviado";
    }
}
