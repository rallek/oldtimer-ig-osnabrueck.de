<?php
/**
 * Sponsoring.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\SponsoringModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use RK\SponsoringModule\Entity\Factory\SponsorFactory;
use RK\SponsoringModule\Helper\ListEntriesHelper;

/**
 * Sponsor editing form type base class.
 */
abstract class AbstractSponsorType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var SponsorFactory
     */
    protected $entityFactory;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * SponsorType constructor.
     *
     * @param TranslatorInterface $translator    Translator service instance
     * @param SponsorFactory        $entityFactory Entity factory service instance
     * @param ListEntriesHelper   $listHelper    ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, SponsorFactory $entityFactory, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->listHelper = $listHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addEntityFields($builder, $options);
        $this->addReturnControlField($builder, $options);
        $this->addSubmitButtons($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['logo'] as $uploadFieldName) {
                if ($entity[$uploadFieldName] instanceof File) {
                    $entity[$uploadFieldName] = [$uploadFieldName => $entity[$uploadFieldName]->getPathname()];
                }
            }
        });
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['logo'] as $uploadFieldName) {
                if (is_array($entity[$uploadFieldName])) {
                    $entity[$uploadFieldName] = $entity[$uploadFieldName][$uploadFieldName];
                }
            }
        });
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sponsorName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Sponsor name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the sponsor name of the sponsor')
            ],'required' => true,
            'max_length' => 255,
        ]);
        $builder->add('sponsoringEvent', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Sponsoring event') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('the year, the event, a project, or whatever')
            ],
            'help' => $this->__('the year, the event, a project, or whatever'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the sponsoring event of the sponsor')
            ],'required' => true,
            'max_length' => 255,
        ]);
        $builder->add('logo', 'RK\SponsoringModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Logo') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('upload an image tor this sponsoring. If there is no image a link will be shown.')
            ],
            'help' => $this->__('upload an image tor this sponsoring. If there is no image a link will be shown.'),
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the logo of the sponsor')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => 0
        ]);
        $builder->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
            'label' => $this->__('Description') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('some words about this sponsoring. But we do not talk about the amount of money.')
            ],
            'help' => $this->__('some words about this sponsoring. But we do not talk about the amount of money.'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the description of the sponsor')
            ],'required' => false,
            'max_length' => 2000,
        ]);
        $builder->add('startDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
            'label' => $this->__('Start date') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => ' validate-daterange-sponsor',
                'title' => $this->__('Enter the start date of the sponsor')
            ],'empty_data' => date('Y-m-d'),
            'required' => false,
            'widget' => 'single_text'
        ]);
        $builder->add('endDate', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
            'label' => $this->__('End date') . ':',
            'empty_data' => '2099-12-31',
            'attr' => [
                'class' => ' validate-daterange-sponsor',
                'title' => $this->__('Enter the end date of the sponsor')
            ],'empty_data' => '2099-12-31',
            'required' => true,
            'widget' => 'single_text'
        ]);
        $builder->add('sponsoringUser', 'RK\SponsoringModule\Form\Type\Field\UserType', [
            'label' => $this->__('Sponsoring user') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Is it an already registered user? In this case you can also select the user.')
            ],
            'help' => $this->__('Is it an already registered user? In this case you can also select the user.'),
            'empty_data' => '',
            'attr' => [
                'class' => ' validate-digits',
                'title' => $this->__('Enter the sponsoring user of the sponsor')
            ],'required' => false,
            'max_length' => 11,
            'inlineUsage' => $options['inlineUsage']
        ]);
    }

    /**
     * Adds the return control field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addReturnControlField(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] != 'create') {
            return;
        }
        $builder->add('repeatCreation', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'mapped' => false,
            'label' => $this->__('Create another item after save'),
            'required' => false
        ]);
    }

    /**
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $this->__(/** @Ignore */$action['title']),
                'icon' => ($action['id'] == 'delete' ? 'fa-trash-o' : ''),
                'attr' => [
                    'class' => $action['buttonClass'],
                    'title' => $this->__(/** @Ignore */$action['description'])
                ]
            ]);
        }
        $builder->add('reset', 'Symfony\Component\Form\Extension\Core\Type\ResetType', [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rksponsoringmodule_sponsor';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'RK\SponsoringModule\Entity\SponsorEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createSponsor();
                },
                'error_mapping' => [
                    'isSponsoringUserUserValid' => 'sponsoringUser',
                    'logo' => 'logo.logo',
                    'isStartDateBeforeEndDate' => 'startDate',
                ],
                'mode' => 'create',
                'actions' => [],
                'inlineUsage' => false
            ])
            ->setRequired(['entity', 'mode', 'actions'])
            ->setAllowedTypes([
                'mode' => 'string',
                'actions' => 'array',
                'inlineUsage' => 'bool'
            ])
            ->setAllowedValues([
                'mode' => ['create', 'edit']
            ])
        ;
    }
}
