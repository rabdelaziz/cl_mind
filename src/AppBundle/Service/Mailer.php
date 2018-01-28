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

    /**
     * @param $to
     * @param $subject
     * @param $body
     * @param null $path
     */
    public function sendMessage($to, $subject, $body, $path = null)
    {
        $mail = \Swift_Message::newInstance();

        $mail->setFrom($this->from,$this->name)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            //->setReplyTo($this->reply,$name)
            ->setContentType('text/html');
        if (!empty($path)) {
            $mail->attach(\Swift_Attachment::fromPath($path));
        }

        $this->mailer->send($mail);
    }

    /**
     * @param $user
     * @param $evaluation
     * @param $path
     */
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


