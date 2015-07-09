<?php

namespace BrauneDigital\MailBundle\Model;

use Doctrine\ORM\Mapping as ORM;

interface NotifyInterface {

	/**
	 * @return mixed
	 */
	public function notify();

}