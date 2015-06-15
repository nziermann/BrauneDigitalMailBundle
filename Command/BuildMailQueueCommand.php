<?php

namespace BrauneDigital\MailBundle\Command;

use BrauneDigital\MailBundle\Entity\OperatorMail;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Application\BrauneDigital\MailBundle\Entity\Mail;

class BuildMailQueueCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('likez:mails:create')
			->setDescription('Build mail queue')
			->addArgument(
				'entity',
				InputArgument::REQUIRED,
				"Entity")
			->addArgument(
				'template',
				InputArgument::REQUIRED,
				"Template")
			->addArgument(
				'notTemplate',
				InputArgument::OPTIONAL,
				"Not Template")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$em = $this->getContainer()->get('doctrine')->getManager();
		$objects = $em->getRepository($input->getArgument('entity'))->findAll(true);
		$template = $em->getRepository('BrauneDigitalMailBundle:MailTemplate')->find($input->getArgument('template'));

		if ($template) {
			switch ($input->getArgument('entity')) {
				default:
					$mailRepository = $em->getRepository('BrauneDigitalMailBundle:OperatorMail');
			}


			foreach ($objects as $object) {

				if ($input->getArgument('notTemplate')) {
					$notTemplate = $em->getRepository('BrauneDigitalMailBundle:MailTemplate')->find($input->getArgument('notTemplate'));
					$mailWithNotTemplate = $mailRepository->findOneBy(array(
						'object' => $object,
						'template' => $notTemplate
					));

				} else {
					$mailWithNotTemplate = false;
				}

				if (!$mailRepository->findBy(array(
						'object' => $object,
						'template' => $template
					)) && !$mailWithNotTemplate && !$object->hasUserRelation()) {
					switch ($input->getArgument('entity')) {
						default:
							$mail = new OperatorMail();
					}
					$mail->setTemplate($template);
					$mail->setStatus(Mail::STATUS_WAITING_FOR_SENDING);
					$mail->setObject($object);

					if (method_exists($object, 'getCity')) {
						$city = $object->getCity();
						if ($city && $city->getCountry() && $city->getCountry()->getCode() == 'DE') {
							$mail->setLocale('de');
						} else {
							$mail->setLocale('en');
						}
					} else {
						$mail->setLocale('en');
					}

					$em->persist($mail);
					$em->flush();

					$output->writeln('Created mailing for ' . $object->__toString());
				}
			}
		}



	}
}