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

namespace RK\ParkHausModule\Form\Type\QuickNavigation\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use RK\ParkHausModule\Helper\ListEntriesHelper;

/**
 * Vehicle image quick navigation form type base class.
 */
abstract class AbstractVehicleImageQuickNavType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * VehicleImageQuickNavType constructor.
     *
     * @param TranslatorInterface $translator   Translator service instance
     * @param RequestStack        $requestStack RequestStack service instance
     * @param ListEntriesHelper   $listHelper   ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
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
        $builder
            ->setMethod('GET')
            ->add('all', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
            ->add('own', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
            ->add('tpl', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
        ;

        $this->addIncomingRelationshipFields($builder, $options);
        $this->addListFields($builder, $options);
        $this->addSearchField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addBooleanFields($builder, $options);
        $builder->add('updateview', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $this->__('OK'),
            'attr' => [
                'class' => 'btn btn-default btn-sm'
            ]
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
        $mainSearchTerm = '';
        $request = $this->requestStack->getCurrentRequest();
        if ($request->query->has('q')) {
            // remove current search argument from request to avoid filtering related items
            $mainSearchTerm = $request->query->get('q');
            $request->query->remove('q');
        }
    
        $builder->add('vehicle', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'RKParkHausModule:VehicleEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'placeholder' => $this->__('All'),
            'required' => false,
            'label' => $this->__('Vehicle'),
            'attr' => [
                'class' => 'input-sm'
            ]
        ]);
    
        if ($mainSearchTerm != '') {
            // readd current search argument
            $request->query->set('q', $mainSearchTerm);
        }
    }

    /**
     * Adds list fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListFields(FormBuilderInterface $builder, array $options)
    {
        $listEntries = $this->listHelper->getEntries('vehicleImage', 'workflowState');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('workflowState', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('State'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => $choices,
            'choices_as_values' => true,
            'choice_attr' => $choiceAttributes,
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
            'label' => $this->__('Search'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'max_length' => 255
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
                'label' => $this->__('Sort by'),
                'attr' => [
                    'class' => 'input-sm'
                ],
                'choices' =>             [
                    $this->__('Titel') => 'titel',
                    $this->__('Vehicle image') => 'vehicleImage',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdUserId',
                    $this->__('Update date') => 'updatedDate'
                ],
                'choices_as_values' => true,
                'required' => false,
                'expanded' => false
            ])
            ->add('sortdir', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort direction'),
                'empty_data' => 'asc',
                'attr' => [
                    'class' => 'input-sm'
                ],
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'choices_as_values' => true,
                'required' => false,
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
            'label' => $this->__('Page size'),
            'empty_data' => 20,
            'attr' => [
                'class' => 'input-sm text-right'
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
            'required' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds boolean fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addBooleanFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('viewImage', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('View image'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => [
                $this->__('No') => 'no',
                $this->__('Yes') => 'yes'
            ],
            'choices_as_values' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rkparkhausmodule_vehicleimagequicknav';
    }
}
