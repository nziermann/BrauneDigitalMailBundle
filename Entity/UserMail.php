<?php

namespace BrauneDigital\MailBundle\Entity;

use Application\BrauneDigital\MailBundle\Entity\Mail;
use Doctrine\ORM\Mapping as ORM;

class UserMail extends Mail
{

	private $object;

    private $object2;

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
    public function getObject2()
    {
        return $this->object2;
    }

    /**
     * @param mixed $object2
     */
    public function setObject2($object2)
    {
        $this->object2 = $object2;
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
