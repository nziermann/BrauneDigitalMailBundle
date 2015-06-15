<?php

namespace BrauneDigital\MailBundle\Entity;

use Application\BrauneDigital\MailBundle\Entity\Mail;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("likez_user_mails")
 *
 */
class UserMail extends Mail
{
	/**
	 * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="mails", cascade={"persist"})
	 * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
	 */
	private $object;

	/**
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->object;
	}

	/**
	 * @param mixed $object
	 */
	public function setObject($object)
	{
		$this->object = $object;
	}

    /**
     * @return mixed
     */
    public function getLocale()
    {
        if($this->locale != null) {
            return parent::getLocale();
        }
        //get locale from user
        if($this->object != null) {
            return $this->object->getLocale();
        }
        return null;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        if($this->recipient != null) {
            return parent::getRecipient();
        }
        if($this->object != null) {
            return $this->object->getEmail();
        }
        return null;
    }
}
