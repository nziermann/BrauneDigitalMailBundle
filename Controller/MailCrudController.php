<?php


namespace BrauneDigital\MailBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Application\BrauneDigital\MailBundle\Entity\Mail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MailCrudController extends Controller
{

	public function previewMailAction()
	{
		$object = $this->admin->getSubject();

		if (false === $this->admin->isGranted('PREVIEW_MAIL')) {
			throw new AccessDeniedException();
		}

		$template = $object->getTemplate();

		if ($object->getLocale() != null) {
			$template->setForceLocale($object->getLocale());
		}

		$content = $this->renderView($object->getTemplate()->getLayout(), array(
			'object' => $object
		));

		$mailService = $this->get('likez.base.service.mail');
		$content = $mailService->replaceMarkers($content);

		/**
		 * Just for testing plain text rendering
		 */
		$plainText = $this->renderView(str_replace('html.twig', 'txt.twig', $object->getTemplate()->getLayout()), array(
			'object' => $object
		));
		$plainText = $mailService->replaceMarkers($plainText);

		$content .= $plainText;

		return new Response($content);
	}


	public function sendMailAction() {
		$object = $this->admin->getSubject();

		if (false === $this->admin->isGranted('SEND_MAIL')) {
			throw new AccessDeniedException();
		}

		$mailService = $this->get('likez.base.service.mail');
		$mailService->handle($object);
		$this->addFlash("sonata_flash_success", "The mail has been sent.");

		return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));

	}

	/**
	 * @param $status
	 * @return RedirectResponse
	 * @throws AccessDeniedException
	 */
	public function changeStatusAction($status) {

		$object = $this->admin->getSubject();

		if (false === $this->admin->isGranted('CHANGE_STATUS')) {
			throw new AccessDeniedException();
		}

		$em = $this->container->get('doctrine')->getManager();
		$allowed = false;
		if (
			in_array($object->getStatus(), array(Mail::STATUS_DISABLED, Mail::STATUS_WAITING_FOR_SENDING))
		) {
			$allowed = true;
		}

		if ($allowed) {
			$object->setStatus($status);
			$em->persist($object);
			$em->flush();
			$this->addFlash("sonata_flash_success", "The Status has been changed.");
		} else {
			$this->addFlash("sonata_flash_error", "An error occured.");
		}

		return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));

	}



}