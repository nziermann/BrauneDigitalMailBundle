<?php
namespace BrauneDigital\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 */
class MailTemplateTranslation
{

	use ORMBehaviors\Translatable\Translation;

	/**
	 * @var string
	 *
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param string $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}




}
