<?php


namespace BrauneDigital\MailBundle\Service;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Application\BrauneDigital\MailBundle\Entity\Mail;
use BrauneDigital\MailBundle\Entity\OperatorMail;
use BrauneDigital\MailBundle\Entity\UserMail;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class MailService implements MailerInterface
{

	protected $container;
	protected $templating;

	public function __construct(ContainerInterface $container, EngineInterface $templating) {
		$this->container = $container;
		$this->templating = $templating;
	}

	public function handle(Mail $object) {

		$mailer = $this->container->get('mailer');
        $em = $this->container->get('doctrine')->getManager();

        $template = $object->getTemplate();

		if ($object->getLocale() != null) {
			$template->setForceLocale($object->getLocale());
		}

        $em->refresh($template);

		$htmlBody = $this->templating->render(
			$template->getLayout(),
			array('object' => $object)
		);

		$htmlBody = $this->replaceMarkers($htmlBody);

		$txtBody = $this->templating->render(
			str_replace('html.twig', 'txt.twig', $template->getLayout()),
			array('object' => $object)
		);
		$txtBody = $this->replaceMarkers($txtBody);

		if ($object->getLocale() != null) {
			$subject = $template->translate($object->getLocale())->getSubject();
		} else {
			$subject = $template->translate()->getSubject();
		}

		$message = $mailer
			->createMessage()
			->setSubject($subject)
			->setFrom(array(
				$template->getSenderMail() => $template->getSenderName()
			))->setBody(
                $htmlBody,
                'text/html'
            )
            ->addPart(
                $txtBody,
                'text/plain'
            );

        if ($object->getRecipient() != null) {
            $message->setTo($object->getRecipient());
        }
        else {
            $message->setTo("recipient@example.com");
        }

		$response = $mailer->send($message);

		$object->setResponse($response);
		$object->setStatus(Mail::STATUS_SENT_SUCCESS);
		$em->persist($object);
		$em->flush();

		return true;
	}

	/**
	 * @param $content
	 * @return mixed
	 */
	public function replaceMarkers($content) {
		preg_match_all("/\###(.*)\###/", $content, $matches);
		$delete = array();
		foreach ($matches[0] as $i => $match) {
			preg_match("/\---" . $matches[1][$i] . "(.*)\---/", $content, $replacement);
			if (isset($replacement[0])) {
				$content = str_replace($match, $replacement[1], $content);
				array_push($delete, $replacement[0]);
			}
		}
		foreach ($delete as $d) {
			$content = str_replace($d, '', $content);
		}
		return $content;
	}

	/**
	 * Send an email to a user to confirm the account creation
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendConfirmationEmailMessage(UserInterface $user)
	{
		$em = $this->container
			->get('doctrine')
			->getManager();
		$template = $em
			->getRepository('BrauneDigitalMailBundle:MailTemplate')
			->findOneBy(array('layout' => 'ApplicationAppBundle:Mail:Registration/confirm.html.twig'));
		if ($template) {
			$mail = new UserMail();
			$mail->setTemplate($template);
			$mail->setObject($user);
			$em->persist($mail);
			$em->flush();
			$this->handle($mail);
		}
	}

	/**
	 * Send an email to a user to confirm the password reset
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendResettingEmailMessage(UserInterface $user)
	{
		$em = $this->container
			->get('doctrine')
			->getManager();
		$template = $em
			->getRepository('BrauneDigitalMailBundle:MailTemplate')
			->findOneBy(array('layout' => 'ApplicationAppBundle:Mail:Resetting/resetPassword.html.twig'));
		if ($template) {
			$mail = new UserMail();
			$mail->setTemplate($template);
			$mail->setObject($user);
			$em->persist($mail);
			$em->flush();
			$this->handle($mail);
		}
	}

}