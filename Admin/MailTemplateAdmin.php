<?php


namespace BrauneDigital\MailBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use BrauneDigital\MailBundle\Service\TemplateSearcherInterface;
use BrauneDigital\TranslationBaseBundle\Admin\TranslationAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MailTemplateAdmin extends TranslationAdmin
{
	protected $mailConfig = array();
	protected $templateSearcher;

	public function __construct($code, $class, $baseControllerName, $config, TemplateSearcherInterface $templateSearcher = null) {
		parent::__construct($code, $class, $baseControllerName);
		$this->mailConfig = $config;
		$this->templateSearcher = $templateSearcher;
	}

	/**
	 * @return array get a list of layout choices
	 */
	protected function getLayoutChoices() {

		if($this->templateSearcher == null) {
			return array();
		}

		$basePaths = array();

		if(is_array($this->mailConfig['base_template_path'])) {
			$basePaths = $this->mailConfig['base_template_path'];
		} else {
			$basePaths = array($this->mailConfig['base_template_path']);
		}

		return $this->templateSearcher->getTemplates($basePaths);
	}

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

		$this->setCurrentLocale();
		$this->buildTranslations($this->subject);

        $formMapper
            ->add('title')
			->add('translations', TranslationsType::class, array(
				'locales' => $this->currentLocale,
				'required_locales' => $this->currentLocale,
				'fields' => array(
                    'senderName' => array(
                        'field_type' => TextType::class,
                        'label' => 'Sender Name',
                        'empty_data' => '',
                    ),
                    'senderMail' => array(
                        'field_type' => TextType::class,
                        'label' => 'Sender Mail',
                        'empty_data' => '',
                    ),
					'subject' => array(
						'field_type' => TextType::class,
						'label' => 'Subject',
						'empty_data' => '',
					),
					'body' => array(
						'field_type' => CKEditorType::class,
						'label' => 'Description',
						'empty_data' => '',
						'required' => false,
						'config_name' => 'default'
					),

				)
			), array(
				'label' => ''
			));


			$layoutChoices = $this->getLayoutChoices();

			if($this->mailConfig['base_template_path'] &&
				$layoutChoices != null &&
				count($layoutChoices) > 0) {
					if($this->getSubject()->getLayout() && !in_array($this->getSubject()->getLayout(), $layoutChoices)) {
						$layoutChoices[] = $this->getSubject()->getLayout();
					}

					$layoutChoices = array_combine($layoutChoices, $layoutChoices);
					$formMapper->add('layout', 'choice', array(
					'choices' => $layoutChoices
					));
			} else {
				$formMapper->add('layout');
			}
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