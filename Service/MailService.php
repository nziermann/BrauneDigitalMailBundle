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

    const RENDER_TYPE_HTML = 'html';
    const RENDER_TYPE_TXT = 'txt';

	protected $container;
	protected $templating;
	protected $layouts;
	protected $config;

	public function __construct(ContainerInterface $container, EngineInterface $templating, $layouts, $config) {
		$this->container = $container;
		$this->templating = $templating;
		$this->layouts = $layouts;
        $this->config = $config;
	}

	public function handle(Mail $object) {

		$mailer = $this->container->get('mailer');
        $em = $this->container->get('doctrine')->getManager();

        $template = $object->getTemplate();


		if(!$template) {
			return;
		}

        //try to use the request locale if none was set
        if((!array_key_exists('message', $this->config) ||  $this->config['message']['use_request_locale']) && $object->getLocale() == null) {
            $object->setLocale($this->getCurrentLocale());
        }

		if ($object->getLocale() != null) {
			$template->setForceLocale($object->getLocale());
		}

        $em->refresh($template);

		$htmlBody = $this->templating->render(
			$template->getLayout(),
			array('object' => $object)
		);

        $htmlBody = $this->beforeReplacement($htmlBody, $object, self::RENDER_TYPE_HTML);
		$htmlBody = $this->replaceMarkers($htmlBody);
        $htmlBody = $this->afterReplacement($htmlBody, $object, self::RENDER_TYPE_HTML);

		$txtBody = $this->templating->render(
			str_replace('html.twig', 'txt.twig', $template->getLayout()),
			array('object' => $object)
		);
        $txtBody = $this->beforeReplacement($txtBody, $object, self::RENDER_TYPE_TXT);
		$txtBody = $this->replaceMarkers($txtBody);
        $txtBody = $this->afterReplacement($txtBody, $object, self::RENDER_TYPE_TXT);

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

        if (isset($this->config['message'])) {
            if (isset($this->config['message']['headers']) && is_array($this->config['message']['headers'])) {
                $headerSet = $message->getHeaders();
                foreach($this->config['message']['headers'] as $key => $value) {
                    $headerSet->addTextHeader($key, $value);
                }
            }
        }

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

		preg_match_all('/' . preg_quote('###', '/') . '([A-Z0-9_|\\-]*)' . preg_quote('###', '/') . '/is', $content, $matches);
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

	public function beforeReplacement($bodyText, Mail $object, $renderType) {
	    return $bodyText;
    }

	public function afterReplacement($bodyText, Mail $object, $renderType) {
	    return $bodyText;
    }

	/**
	 * Send an email to a user to confirm the account creation
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendConfirmationEmailMessage(UserInterface $user) {
        $this->sendUserMail($user, $this->layouts['confirm']);
	}

	/**
	 * Send an email to a user to confirm the password reset
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendResettingEmailMessage(UserInterface $user) {
		$this->sendUserMail($user, $this->layouts['password_reset']);
	}

    /**
     * Send an email to a user based on a user instance, a template and an optional locale
     * @param UserInterface $user
     * @param $layout
     * @param null $locale
     */
    protected function sendUserMail(UserInterface $user, $layout, $locale = null) {
        $em = $this->container
            ->get('doctrine')
            ->getManager();
        $template = $em
            ->getRepository('BrauneDigitalMailBundle:MailTemplate')
            ->findOneBy(array('layout' => $layout));
        if ($template) {
            $mail = new UserMail();
            $mail->setTemplate($template);
            $mail->setObject($user);

            if ($this->config['message']['use_request_locale']) {
                $mail->setLocale($this->getCurrentLocale());
            }

            $em->persist($mail);
            $em->flush();
            $this->handle($mail);
        }
    }

    /**
     * Get the current locale from a request when sending
     * @return null
     */
    protected function getCurrentLocale() {
        $requestStack = $this->container->get('request_stack');

        if ($requestStack) {
            $request = $requestStack->getCurrentRequest();

            if ($request) {
                return $request->getLocale();
            }
        }
        return null;
    }
}