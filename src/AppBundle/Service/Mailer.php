<?php
/**
 * Created by PhpStorm.
 * User: ram
 * Date: 14/01/18
 * Time: 20:02
 */

namespace AppBundle\Service;

use Symfony\Component\Templating\EngineInterface;


class Mailer
{
    protected $mailer;
    public $templating;
    private $from = "clevertest@clevermind.fr";
    private $reply = "contact@example.fr";
    private $name = "Equipe Clevermind";

    public function __construct($mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    protected function sendMessage($to, $subject, $body, $path)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setFrom($this->from,$this->name)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
             ->attach(\Swift_Attachment::fromPath($path))
            //->setReplyTo($this->reply,$name)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    public function sendResultReport($user, $evaluation, $path)
    {

        $subject = "Rapport des résultat test cleverTest" . $user->getFirstName() . ' ' . $user->getLastName();
        //$templatePdf = 'Emails/reportResult.html.twig';
        $templateEmail = 'Emails/reportResultText.html.twig';
        $to = $evaluation->getAuthor()->getEmail();
        $body = $this->templating->render($templateEmail, array('candidat' => $user,
                                                                'manager'=> $evaluation->getAuthor()));
        $this->sendMessage($to, $subject, $body, $path);
    }

}


