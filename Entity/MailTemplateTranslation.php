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
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $senderMail;

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

    /**
     * Set senderName
     *
     * @param string $senderName
     *
     * @return MailTemplate
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;

        return $this->getTranslatable();
    }

    /**
     * Get senderName
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set senderMail
     *
     * @param string $senderMail
     *
     * @return MailTemplate
     */
    public function setSenderMail($senderMail)
    {
        $this->senderMail = $senderMail;

        return $this->getTranslatable();
    }

    /**
     * Get senderMail
     *
     * @return string
     */
    public function getSenderMail()
    {
        return $this->senderMail;
    }
}
