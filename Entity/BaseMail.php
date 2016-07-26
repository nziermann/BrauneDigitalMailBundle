<?php

namespace BrauneDigital\MailBundle\Entity;



/**
 * Class Mail
 * @package BrauneDigital\MailBundle\Entity
 */
abstract class BaseMail
{


    abstract public function getObject();

	const STATUS_WAITING_FOR_SENDING = 1;
	const STATUS_SENT_SUCCESS = 2;
	const STATUS_SENT_ERROR = 3;
	const STATUS_DISABLED = 4;

    /**
     * @var integer
     *
     */
    private $status = self::STATUS_WAITING_FOR_SENDING;

    /**
     * @var string

     */
    private $response;

	/**
	 */
	private $template;

    /**
     * @var string
     *
     */
    protected $locale;

    /**
     * @var string
     */
    protected $recipient;

	/**
	 *
	 */
	public function __construct() {
		$this->status = self::STATUS_WAITING_FOR_SENDING;
        $this->locale = null;
	}

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Mail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set response
     *
     * @param string $response
     *
     * @return Mail
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set template
     *
     * @param \BrauneDigital\MailBundle\Entity\MailTemplate $template
     *
     * @return Mail
     */
    public function setTemplate(\BrauneDigital\MailBundle\Entity\MailTemplate $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \BrauneDigital\MailBundle\Entity\MailTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }
}
