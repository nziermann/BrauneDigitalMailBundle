<?php


namespace BrauneDigital\MailBundle\Admin;

use BrauneDigital\TranslationBaseBundle\Admin\TranslationAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class MailTemplateAdmin extends TranslationAdmin
{



    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

		$this->setCurrentLocale();
		$this->buildTranslations($this->subject);

        $formMapper
            ->add('title')
			->add('translations', 'a2lix_translations', array(
				'locales' => $this->currentLocale,
				'required_locales' => $this->currentLocale,
				'fields' => array(
                    'senderName' => array(
                        'field_type' => 'text',
                        'label' => 'Sender Name',
                        'empty_data' => '',
                    ),
                    'senderMail' => array(
                        'field_type' => 'text',
                        'label' => 'Sender Mail',
                        'empty_data' => '',
                    ),
					'subject' => array(
						'field_type' => 'text',
						'label' => 'Subject',
						'empty_data' => '',
					),
					'body' => array(
						'field_type' => 'ckeditor',
						'label' => 'Description',
						'empty_data' => '',
						'required' => false,
						'config_name' => 'default'
					),

				)
			), array(
				'label' => ''
			))
            ->add('layout')
        ;
    }


    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('title')
            ->add('subject')
        ;
    }



}