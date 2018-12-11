<?php
/**
 * Created by PhpStorm.
 * User: ram
 * Date: 14/01/18
 * Time: 20:02
 */

namespace AppBundle\Service;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use AppBundle\Entity\User;


class Mailer
{
    protected $mailer;
    public $templating;
    protected $container;
    private $from = "clevertest@clevermind.fr";
    private $reply = "contact@example.fr";
    private $name = "Equipe Clevermind";

    public function __construct($mailer, Container $container, EngineInterface $templating, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->container = $container;
        $this->templating = $templating;
        $this->requestStack = $requestStack;
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
    
    /**
     * Permet d'envoyer un email à l'utilisateur lorsqu'un compte est créé à son nom
     *
     * @param User $user
     */
    public function sendRegistrationMessage(User $user)
    {
        $url = $this->requestStack->getCurrentRequest()->getScheme() . '://' . $this->requestStack->getCurrentRequest()->getHost();
        $this->sendMessage(
            $user->getEmail(),
            'Création de votre compte Clevertest',
            $this->templating->render('AppBundle:Mails:register.html.twig', array('user' => $user, 'url' => $url))
        );
    }
    
    public function test()
    {
        $to = 'saliu.diallo@gmail.com';
        $subject = 'Test mail'; 
        $body = '<html> Bonjour tout le monde.</html>';
        
        //dump($to);
        $this->sendMessage($to, $subject, $body);
        //dump($sent);
    }

}


