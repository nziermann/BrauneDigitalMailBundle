<?php

namespace BrauneDigital\MailBundle\Model;

use Doctrine\ORM\Mapping as ORM;

interface MailObjectInterface {

	/**
	 * @return mixed
	 */
	public function getEmail();

	/**
	 * @return mixed
	 */
	public function hasUserRelation();

	/**
	 * @return mixed
	 */
	public function getLocale();

}