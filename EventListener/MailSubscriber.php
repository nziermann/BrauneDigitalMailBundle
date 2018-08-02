<?php

namespace BrauneDigital\MailBundle\EventListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Util\ClassUtils;


class MailSubscriber implements EventSubscriber
{

	/**
	 * @var
	 */
	protected $entity;

	public function getSubscribedEvents()
	{
		return array(
			'postPersist',
			'postUpdate',
		);
	}

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		$this->index($args, 'postUpdate');
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$this->index($args, 'postPersist');
	}

	public function index(LifecycleEventArgs $args, $eventType)
	{
		$entity = $args->getEntity();
		$entityManager = $args->getEntityManager();
		$config = $this->container->getParameter('braune_digital_mail');

		$entities = array_keys($config['entities']);
		$className = ClassUtils::getClass($entity);

		if (substr($className, -11) == 'Translation') {
			$className = substr($className, 0, strlen($className) - 11);
		}

		if (in_array($className, $entities)) {

			$entityConfig = $config['entities'][$className];

			if (in_array($eventType, $entityConfig['events'])) {

				$entityIsTranslation = false;
				if ($entityConfig['check_translation']) {
					$entityIsTranslation = (ClassUtils::getClass($this->entity) == $className . 'Translation') ? true : false;
				}

				if (ClassUtils::getClass($this->entity) == $className or $entityIsTranslation) {
					if ($entityIsTranslation) {
						$this->entity = $this->entity->getTranslatable();
					}
				}

				$service = $this->container->get($entityConfig['service']);
				$service->notify($this->entity);

			}
		}
	}
}