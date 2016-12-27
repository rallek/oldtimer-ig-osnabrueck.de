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

namespace RK\ParkhausModule\Form\Type\Finder\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * Vehicle finder form type base class.
 */
abstract class AbstractVehicleFinderType extends AbstractType
{
    use TranslatorTrait;

    /**
     * VehicleFinderType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
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
        $builder
            ->setMethod('GET')
            ->add('objectType', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
                'data' => $options['objectType']
            ])
            ->add('editor', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
                'data' => $options['editorName']
            ])
        ;

        $this->addPasteAsField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addSearchField($builder, $options);

        $builder
            ->add('update', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $this->__('Change selection'),
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $this->__('Cancel'),
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default',
                    'formnovalidate' => 'formnovalidate'
                ]
            ])
        ;
    }

    /**
     * Adds a "paste as" field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addPasteAsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pasteas', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Paste as') . ':',
            'empty_data' => 1,
            'choices' => [
                $this->__('Link to the vehicle') => 1,
                $this->__('ID of vehicle') => 2
            ],
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds sorting fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort by') . ':',
                'empty_data' => '',
                'choices' => [
                    $this->__('Id') => 'id',
                    $this->__('Vehicle type') => 'vehicleType',
                    $this->__('Title image') => 'titleImage',
                    $this->__('Copyright title image') => 'copyrightTitleImage',
                    $this->__('Vehicle image') => 'vehicleImage',
                    $this->__('Copyright vehicle image') => 'copyrightVehicleImage',
                    $this->__('Vehicle description teaser') => 'vehicleDescriptionTeaser',
                    $this->__('Vehicle description') => 'vehicleDescription',
                    $this->__('Manufacturer') => 'manufacturer',
                    $this->__('Model') => 'model',
                    $this->__('Built') => 'built',
                    $this->__('Engine') => 'engine',
                    $this->__('Displacement') => 'displacement',
                    $this->__('Cylinders') => 'cylinders',
                    $this->__('Compression') => 'compression',
                    $this->__('Fuel management') => 'fuelManagement',
                    $this->__('Fuel') => 'fuel',
                    $this->__('Horse power') => 'horsePower',
                    $this->__('Max speed') => 'maxSpeed',
                    $this->__('Weight') => 'weight',
                    $this->__('Brakes') => 'brakes',
                    $this->__('Gearbox') => 'gearbox',
                    $this->__('Rim') => 'rim',
                    $this->__('Tire') => 'tire',
                    $this->__('Interior') => 'interior',
                    $this->__('Info field 1') => 'infoField1',
                    $this->__('Info field 2') => 'infoField2',
                    $this->__('Info field 3') => 'infoField3',
                    $this->__('Owner') => 'owner',
                    $this->__('Show vehicle owner') => 'showVehicleOwner',
                    $this->__('Title text color') => 'titleTextColor',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdUserId',
                    $this->__('Update date') => 'updatedDate'
                ],
                'choices_as_values' => true,
                'multiple' => false,
                'expanded' => false
            ])
            ->add('sortdir', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort direction') . ':',
                'empty_data' => 'asc',
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'choices_as_values' => true,
                'multiple' => false,
                'expanded' => false
            ])
        ;
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Page size') . ':',
            'empty_data' => 20,
            'attr' => [
                'class' => 'text-right'
            ],
            'choices' => [
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20,
                30 => 30,
                50 => 50,
                100 => 100
            ],
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a search field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSearchField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', 'Symfony\Component\Form\Extension\Core\Type\SearchType', [
            'label' => $this->__('Search for') . ':',
            'required' => false,
            'max_length' => 255
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rkparkhausmodule_vehiclefinder';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'objectType' => 'vehicle',
                'editorName' => 'ckeditor'
            ])
            ->setRequired(['objectType', 'editorName'])
            ->setAllowedTypes([
                'objectType' => 'string',
                'editorName' => 'string'
            ])
            ->setAllowedValues([
                'editorName' => ['tinymce', 'ckeditor']
            ])
        ;
    }
}
