<?php

namespace TickTackk\DeveloperTools\XF\Mail\XF23B1;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use TickTackk\DeveloperTools\Repository\EmailLog as EmailLogLog;
use TickTackk\DeveloperTools\XF\Mail\XFCP_Mailer;

/**
 * @since 1.5.0
 */
class Mailer extends XFCP_Mailer
{
    /**
     * @param Email $email
     * @param AbstractTransport|null $transport
     *
     * @return bool|\Symfony\Component\Mailer\SentMessage|null
     *
     * @throws \XF\PrintableException
     */
    public function send(Email $email, AbstractTransport $transport = null)
    {
        $sent = parent::send($email, $transport);

        if ($sent)
        {
            /** @var EmailLogLog $emailLogRepo */
            $emailLogRepo = \XF::app()->repository('TickTackk\DeveloperTools:EmailLog');
            $emailLogRepo->log($email);
        }

        return $sent;
    }
}