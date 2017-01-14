<?php
/**
 * ParkHaus.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\ParkHausModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use RK\ParkHausModule\Entity\Factory\ParkHausFactory;
use RK\ParkHausModule\Helper\ListEntriesHelper;

/**
 * Vehicle editing form type base class.
 */
abstract class AbstractVehicleType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var ParkHausFactory
     */
    protected $entityFactory;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * VehicleType constructor.
     *
     * @param TranslatorInterface $translator    Translator service instance
     * @param ParkHausFactory        $entityFactory Entity factory service instance
     * @param ListEntriesHelper   $listHelper    ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, ParkHausFactory $entityFactory, ListEntriesHelper $listHelper)
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
        $this->addModerationFields($builder, $options);
        $this->addReturnControlField($builder, $options);
        $this->addSubmitButtons($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['titleImage', 'vehicleImage', 'manufacturerImage'] as $uploadFieldName) {
                if ($entity[$uploadFieldName] instanceof File) {
                    $entity[$uploadFieldName] = [$uploadFieldName => $entity[$uploadFieldName]->getPathname()];
                }
            }
        });
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['titleImage', 'vehicleImage', 'manufacturerImage'] as $uploadFieldName) {
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
        
        $listEntries = $this->listHelper->getEntries('vehicle', 'vehicleType');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('vehicleType', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Vehicle type') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('please select which type fits best')
            ],
            'help' => $this->__('please select which type fits best'),
            'empty_data' => 'Auto',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the vehicle type')
            ],'choices' => $choices,
            'choices_as_values' => true,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $builder->add('titleImage', 'RK\ParkHausModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Title image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This image will be used as the title image. It shoud have a ratio of 3:1 and min 1200px. Bigger than 1800px will be reduced automatically.')
            ],
            'help' => $this->__('This image will be used as the title image. It shoud have a ratio of 3:1 and min 1200px. Bigger than 1800px will be reduced automatically.'),
            'attr' => [
                'class' => ' validate-nospace validate-upload',
                'title' => $this->__('Enter the title image of the vehicle')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => 0
        ]);
        
        $builder->add('copyrightTitleImage', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Copyright title image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Please fill the copyright. If you got the image from someone else please be fair and name him here.')
            ],
            'help' => $this->__('Please fill the copyright. If you got the image from someone else please be fair and name him here.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the copyright title image of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('vehicleImage', 'RK\ParkHausModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Vehicle image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This image should be in landscape format. It will represent your vehicle. In the album you can add multiple additional images.')
            ],
            'help' => $this->__('This image should be in landscape format. It will represent your vehicle. In the album you can add multiple additional images.'),
            'attr' => [
                'class' => ' validate-nospace validate-upload',
                'title' => $this->__('Enter the vehicle image of the vehicle')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => 0
        ]);
        
        $builder->add('copyrightVehicleImage', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Copyright vehicle image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Please fill the copyright. If you got the image from someone else please be fair and name him here.')
            ],
            'help' => $this->__('Please fill the copyright. If you got the image from someone else please be fair and name him here.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the copyright vehicle image of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('vehicleDescriptionTeaser', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
            'label' => $this->__('Vehicle description teaser') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Vehicle description with max 500 char. It will been shown in the list overview and added in front of the rest of the vehicle description.')
            ],
            'help' => $this->__('Vehicle description with max 500 char. It will been shown in the list overview and added in front of the rest of the vehicle description.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 500,
                'class' => '',
                'title' => $this->__('Enter the vehicle description teaser of the vehicle')
            ],'required' => true
        ]);
        
        $builder->add('vehicleDescription', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
            'label' => $this->__('Vehicle description') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This is the text which follows the teaser text at the display page. You can use max 5000 char.')
            ],
            'help' => $this->__('This is the text which follows the teaser text at the display page. You can use max 5000 char.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 5000,
                'class' => '',
                'title' => $this->__('Enter the vehicle description of the vehicle')
            ],'required' => false
        ]);
        
        $builder->add('manufacturer', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Manufacturer') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('if you do not know the manufacturer please type unknown.')
            ],
            'help' => $this->__('if you do not know the manufacturer please type unknown.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the manufacturer of the vehicle')
            ],'required' => true,
        ]);
        
        $builder->add('manufacturerImage', 'RK\ParkHausModule\Form\Type\Field\UploadType', [
            'label' => $this->__('Manufacturer image') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Here you can place an image from the OEM who manufactured the vehicle. It will be used in the print version.')
            ],
            'help' => $this->__('Here you can place an image from the OEM who manufactured the vehicle. It will be used in the print version.'),
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the manufacturer image of the vehicle')
            ],'required' => false,
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => 0
        ]);
        
        $builder->add('model', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Model') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('vehicle version')
            ],
            'help' => $this->__('vehicle version'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the model of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('built', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Built') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('the year or month.year of your vehicle built')
            ],
            'help' => $this->__('the year or month.year of your vehicle built'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the built of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('engine', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Engine') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('e.g. Otto, Diesel, Wankel, or specific type of engine, ...')
            ],
            'help' => $this->__('e.g. Otto, Diesel, Wankel, or specific type of engine, ...'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the engine of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('displacement', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Displacement') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('how much ccm')
            ],
            'help' => $this->__('how much ccm'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the displacement of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('cylinders', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Cylinders') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('how many cylinders')
            ],
            'help' => $this->__('how many cylinders'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the cylinders of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('compression', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Compression') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('compression in bar')
            ],
            'help' => $this->__('compression in bar'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the compression of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('fuelManagement', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Fuel management') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('e.g. injection type, carburetor manufacturor and size ...')
            ],
            'help' => $this->__('e.g. injection type, carburetor manufacturor and size ...'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the fuel management of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('fuel', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Fuel') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('z.B. Benzine, Diesel or 1:25')
            ],
            'help' => $this->__('z.B. Benzine, Diesel or 1:25'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the fuel of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('horsePower', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Horse power') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('you can use hp or kw')
            ],
            'help' => $this->__('you can use hp or kw'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the horse power of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('maxSpeed', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Max speed') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('you can use km/h or mph')
            ],
            'help' => $this->__('you can use km/h or mph'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the max speed of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('weight', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Weight') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('typical in kg')
            ],
            'help' => $this->__('typical in kg'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the weight of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('brakes', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Brakes') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('tell us something about your brakes')
            ],
            'help' => $this->__('tell us something about your brakes'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the brakes of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('gearbox', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Gearbox') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('shifter or automatic? Specific type?')
            ],
            'help' => $this->__('shifter or automatic? Specific type?'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the gearbox of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('rim', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Rim') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('if you have special rim installed')
            ],
            'help' => $this->__('if you have special rim installed'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the rim of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('tire', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Tire') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('sice of tire or something specific about your tires')
            ],
            'help' => $this->__('sice of tire or something specific about your tires'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the tire of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('interior', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Interior') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('do you have a special interieur?')
            ],
            'help' => $this->__('do you have a special interieur?'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the interior of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('infoField1', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Info field 1') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If there is something special what you can not fill into one of the other fields you may want to place it here.')
            ],
            'help' => $this->__('If there is something special what you can not fill into one of the other fields you may want to place it here.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the info field 1 of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('infoField2', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Info field 2') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If there is something special what you can not fill into one of the other fields you may want to place it here.')
            ],
            'help' => $this->__('If there is something special what you can not fill into one of the other fields you may want to place it here.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the info field 2 of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('infoField3', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Info field 3') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If there is something special what you can not fill into one of the other fields you may want to place it here.')
            ],
            'help' => $this->__('If there is something special what you can not fill into one of the other fields you may want to place it here.'),
            'empty_data' => '',
            'attr' => [
                'max_length' => 255,
                'class' => '',
                'title' => $this->__('Enter the info field 3 of the vehicle')
            ],'required' => false,
        ]);
        
        $builder->add('showVehicleOwner', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label' => $this->__('Show vehicle owner') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If not checked the registered user will not been shown to the public. Only IG members are able to see.')
            ],
            'help' => $this->__('If not checked the registered user will not been shown to the public. Only IG members are able to see.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('show vehicle owner ?')
            ],'required' => false,
        ]);
        
        $builder->add('titleTextColor', 'RK\ParkHausModule\Form\Type\Field\ColourType', [
            'label' => $this->__('Title text color') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Please change if white is not fitting due to the color of the title image. Preferred you should use #000000 in case.')
            ],
            'help' => $this->__('Please change if white is not fitting due to the color of the title image. Preferred you should use #000000 in case.'),
            'empty_data' => '#ffffff',
            'attr' => [
                'max_length' => 255,
                'class' => ' validate-nospace validate-htmlcolour rkparkhausmoduleColourPicker',
                'title' => $this->__('Choose the title text color of the vehicle')
            ],'required' => true,
        ]);
        
        $builder->add('stillMyOwn', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label' => $this->__('Still my own') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If you do not own the vehicle anymore you do have two options. You may wont to delete the vehicle or you uncheck this option. If unchecked the vehicle is marked as "not in Parkhaus anymore"')
            ],
            'help' => $this->__('If you do not own the vehicle anymore you do have two options. You may wont to delete the vehicle or you uncheck this option. If unchecked the vehicle is marked as "not in Parkhaus anymore"'),
            'attr' => [
                'class' => '',
                'title' => $this->__('still my own ?')
            ],'required' => false,
        ]);
    }

    /**
     * Adds special fields for moderators.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options)
    {
        if (!$options['hasModeratePermission']) {
            return;
        }
    
        $builder->add('moderationSpecificCreator', 'RK\ParkHausModule\Form\Type\Field\UserType', [
            'label' => $this->__('Creator') . ':',
            'attr' => [
                'max_length' => 11,
                'class' => ' validate-digits',
                'title' => $this->__('Here you can choose a user which will be set as creator')
            ],
            'empty_data' => 0,
            'required' => false,
            'help' => $this->__('Here you can choose a user which will be set as creator')
        ]);
        $builder->add('moderationSpecificCreationDate', 'Symfony\Component\Form\Extension\Core\Type\DateTimeType', [
            'label' => $this->__('Creation date') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('Here you can choose a custom creation date')
            ],
            'empty_data' => '',
            'required' => false,
            'widget' => 'single_text',
            'help' => $this->__('Here you can choose a custom creation date')
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
        return 'rkparkhausmodule_vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'RK\ParkHausModule\Entity\VehicleEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createVehicle();
                },
                'error_mapping' => [
                    'titleImage' => 'titleImage.titleImage',
                    'vehicleImage' => 'vehicleImage.vehicleImage',
                    'manufacturerImage' => 'manufacturerImage.manufacturerImage',
                ],
                'mode' => 'create',
                'actions' => [],
                'hasModeratePermission' => false,
                'filterByOwnership' => true,
                'currentUserId' => 0,
                'inlineUsage' => false
            ])
            ->setRequired(['entity', 'mode', 'actions'])
            ->setAllowedTypes([
                'mode' => 'string',
                'actions' => 'array',
                'hasModeratePermissions' => 'bool',
                'filterByOwnership' => 'bool',
                'currentUserId' => 'int',
                'inlineUsage' => 'bool'
            ])
            ->setAllowedValues([
                'mode' => ['create', 'edit']
            ])
        ;
    }
}
