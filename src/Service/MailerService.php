<?php 

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
  private $replayTo;
  public function __construct(private MailerInterface $mailer, $replayTo) 
  {
    $this->replayTo = $replayTo;
  }
  public function sendEmail(
    $to = 'mathkr142@gmail.com', 
    $content = '<p>See Twig integration for better HTML integration!</p>', 
    $subject = 'Time for Symfony Mailer!'): void
    {
        $email = (new Email())
            ->from('kader.math142@hotmail.fr')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($this->replayTo)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            //->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);

        // ...
    }

  
}



?>