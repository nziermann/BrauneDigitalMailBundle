<?php


namespace BrauneDigital\MailBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class MailAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('status', null, array(
				'read_only' => true
			))
            ->add('template', null, array(
				'read_only' => true
			))
            ->add('response', null, array(
				'read_only' => true
			))
        ;
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
		$datagridMapper->add('locale');
		$datagridMapper->add('template');
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('object')
            ->add('template')
			->add('locale')
            ->add('status', 'string', array(
				'template' => 'BrauneDigitalMailBundle:MailCrud:list__status.html.twig')
			)
			->add('response', 'string', array(
					'template' => 'BrauneDigitalMailBundle:MailCrud:list__response.html.twig')
			)
			->add('_action', 'actions', array(
				'actions' => array(
					'sendMail' => array(
						'template' => 'BrauneDigitalMailBundle:MailCrud:list__action_sendMail.html.twig'
					)
				)
			))
        ;
    }

	protected function configureRoutes(RouteCollection $collection)
	{
		$collection->add('sendMail', $this->getRouterIdParameter().'/sendMail');
		$collection->add('previewMail', $this->getRouterIdParameter().'/previewMail');
		$collection->add('changeStatus', $this->getRouterIdParameter().'/changeStatus/{status}');
	}

	public function getTemplate($name)
	{
		switch ($name) {

			default:
				return parent::getTemplate($name);
				break;
		}
	}



}