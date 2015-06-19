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

class SendMailQueueCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('likez:mails:send')
			->setDescription('Send mail queue')
			->addArgument(
				'limit',
				InputArgument::OPTIONAL,
				"Limit")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$context = $this->getContainer()->get('router')->getContext();
		$context->setHost('www.escape-gamer.com');
		$context->setScheme('http');
		$context->setBaseUrl('');

		$limit = $input->getArgument('limit');
		if (!$limit) {
			$limit = 50;
		}

		$em = $this->getContainer()->get('doctrine')->getManager();
		$mailRepository = $em->getRepository('BrauneDigitalMailBundle:Mail');
		$mails = $mailRepository->findBy(array(
			'status' => Mail::STATUS_WAITING_FOR_SENDING
		), array(), $limit);

		foreach ($mails as $mail) {

			$output->writeln($mail->getRecipient());
			if ($mail->getRecipient()) {
				$mailService = $this->getContainer()->get('braunedigital.mail.service.mail');
				$mailService->handle($mail);
			} else {
				$output->writeln('No email');
				$mail->setStatus(Mail::STATUS_SENT_ERROR);
				$mail->setResponse(json_encode(array('message' => 'No recipient could be identified.')));
				$em->persist($mail);
			}


		}

		$em->flush();



	}
}