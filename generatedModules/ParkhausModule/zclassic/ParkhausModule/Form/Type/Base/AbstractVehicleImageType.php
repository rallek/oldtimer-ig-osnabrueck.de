<?php
/**
 * Parkhaus.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\ParkhausModule\Form\Type\Base;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use RK\ParkhausModule\Entity\Factory\VehicleImageFactory;
use RK\ParkhausModule\Helper\ListEntriesHelper;

/**
 * Vehicle image editing form type base class.
 */
abstract class AbstractVehicleImageType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var VehicleImageFactory
     */
    protected $entityFactory;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * VehicleImageType constructor.
     *
     * @param TranslatorInterface $translator    Translator service instance
     * @param VehicleImageFactory        $entityFactory Entity factory service instance
     * @param ListEntriesHelper   $listHelper    ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, VehicleImageFactory $entityFactory, ListEntriesHelper $listHelper)
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
        $this->addIncomingRelationshipFields($builder, $options);
        $this->addReturnControlField($builder, $options);
        $this->addSubmitButtons($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['vehicleImage'] as $uploadFieldName) {
                if ($entity[$uploadFieldName] instanceof File) {
                    $entity[$uploadFieldName] = [$uploadFieldName => $entity[$uploadFieldName]->getPathname()];
                }
            }
        });
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['vehicleImage'] as $uploadFieldName) {
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
        $builder->add('titel', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Titel') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('e.g. the location or the event')
            ],
            'help' => $this->__('e.g. the location or the event'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the titel of the vehicle image')
            ],'required' => true,
            'max_length' => 255,
        ]);
        $builder->add('vehicleImage', 'RK\ParkhausModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Vehicle image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Please upload an image. It does not matter if it is portrait, landscape or square. The hight will be harmonized always.')
            ],
            'help' => $this->__('Please upload an image. It does not matter if it is portrait, landscape or square. The hight will be harmonized always.'),
            'attr' => [
                'class' => ' validate-nospace validate-upload',
                'title' => $this->__('Enter the vehicle image of the vehicle image')
            ],'required' => true && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => 0
        ]);
        $builder->add('copyright', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Copyright') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Please fill the copyright. If you got the image from someone else please be fair and name him here.')
            ],
            'help' => $this->__('Please fill the copyright. If you got the image from someone else please be fair and name him here.'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the copyright of the vehicle image')
            ],'required' => false,
            'max_length' => 255,
        ]);
        $builder->add('imageDate', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Image date') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('the image date will be added to the title')
            ],
            'help' => $this->__('the image date will be added to the title'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the image date of the vehicle image')
            ],'required' => false,
            'max_length' => 255,
        ]);
        $builder->add('description', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
            'label' => $this->__('Description') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the description of the vehicle image')
            ],'required' => false,
            'max_length' => 2000,
        ]);
        $builder->add('viewImage', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label' => $this->__('View image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If not checked the image will not been shown to the public. Only IG members are able to see.')
            ],
            'help' => $this->__('If not checked the image will not been shown to the public. Only IG members are able to see.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('view image ?')
            ],'required' => false,
        ]);
    }

    /**
     * Adds fields for incoming relationships.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIncomingRelationshipFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vehicle', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'RKParkhausModule:VehicleEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function(EntityRepository $er) {
                return $er->getListQueryBuilder();
            },
            'label' => $this->__('Vehicle'),
            'attr' => [
                'title' => $this->__('Choose the vehicle')
            ]
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
        return 'rkparkhausmodule_vehicleimage';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'RK\ParkhausModule\Entity\VehicleImageEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createVehicleImage();
                },
                'error_mapping' => [
                    'vehicleImage' => 'vehicleImage.vehicleImage',
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
