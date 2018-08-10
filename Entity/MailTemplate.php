<?php

namespace BrauneDigital\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * MailTemplate
 */
class MailTemplate
{


	use ORMBehaviors\Translatable\Translatable;
	use \BrauneDigital\TranslationBaseBundle\Model\Translatable\TranslatableMethods {
		\BrauneDigital\TranslationBaseBundle\Model\Translatable\TranslatableMethods::proxyCurrentLocaleTranslation insteadof ORMBehaviors\Translatable\Translatable;
	}

    /**
     * @var integer
     *
     */
    private $id;

    /**
     * @var string
     */
    private $title;


    /**
     * @var string
     */
    private $layout;

	/**
	 * @var string
	 */
	private $forceLocale;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set title
     *
     * @param string $title
     *
     * @return MailTemplate
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set layout
     *
     * @param string $layout
     *
     * @return MailTemplate
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

	public function __call($method, $arguments)
	{
		if (in_array($method, array('body', 'subject')) && $this->getForceLocale()) {
			$value = call_user_func_array(
				[$this->translate($this->getForceLocale()), 'get' . ucfirst($method)],
				$arguments
			);
			if ($value) {
				return $value;
			}
		}


		return $this->proxyCurrentLocaleTranslation($method, $arguments);
	}

	public function __toString() {
		return ($this->getTitle() !== null) ? $this->getTitle() : '';
	}

	/**
	 * @return string
	 */
	public function getForceLocale()
	{
		return $this->forceLocale;
	}

	/**
	 * @param string $forceLocale
	 */
	public function setForceLocale($forceLocale)
	{
		$this->forceLocale = $forceLocale;
	}


}
