<?php


namespace BrauneDigital\MailBundle\Service;

use BrauneDigital\MailBundle\Model\NotifyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseNotifier implements NotifyInterface
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $recipients = array();

	/**
	 * @param ContainerInterface $container
	 * @param $recipients
	 */
	public function __construct(ContainerInterface $container, $recipients) {
		$this->container = $container;
		$userManager = $this->container->get('fos_user.user_manager');
		foreach (explode(',', $recipients) as $recipient) {
			if ($user = $userManager->findUserByEmail($recipient)) {
				$this->recipients[] = $user;
			}
		}
	}

	/**
	 * @param $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}
}