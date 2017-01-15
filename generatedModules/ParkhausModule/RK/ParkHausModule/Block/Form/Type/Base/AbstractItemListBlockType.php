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

namespace RK\ParkHausModule\Block\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;

/**
 * List block form type base class.
 */
abstract class AbstractItemListBlockType extends AbstractType
{
    use TranslatorTrait;

    /**
     * ItemListBlockType constructor.
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
        $this->addObjectTypeField($builder, $options);
        $this->addSortingField($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addTemplateFields($builder, $options);
        $this->addFilterField($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['isCategorisable'] = $options['isCategorisable'];
    }

    /**
     * Adds an object type field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('objectType', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Object type') . ':',
            'empty_data' => 'vehicle',
            'attr' => [
                'title' => $this->__('If you change this please save the block once to reload the parameters below.')
            ],
            'help' => $this->__('If you change this please save the block once to reload the parameters below.'),
            'choices' => [
                $this->__('Vehicles') => 'vehicle',
                $this->__('Vehicle images') => 'vehicleImage'
            ],
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a sorting field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sorting', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Sorting') . ':',
            'empty_data' => 'default',
            'choices' => [
                $this->__('Random') => 'random',
                $this->__('Newest') => 'newest',
                $this->__('Default') => 'default'
            ],
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('amount', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', [
            'label' => $this->__('Amount') . ':',
            'attr' => [
                'maxlength' => 2,
                'title' => $this->__('The maximum amount of items to be shown. Only digits are allowed.')
            ],
            'help' => $this->__('The maximum amount of items to be shown. Only digits are allowed.'),
            'empty_data' => 5,
            'scale' => 0
        ]);
    }

    /**
     * Adds template fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addTemplateFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Template') . ':',
                'empty_data' => 'itemlist_display.html.twig',
                'choices' => [
                    $this->__('Only item titles') => 'itemlist_display.html.twig',
                    $this->__('With description') => 'itemlist_display_description.html.twig',
                    $this->__('Custom template') => 'custom'
                ],
                'choices_as_values' => true,
                'multiple' => false,
                'expanded' => false
            ])
            ->add('customTemplate', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => $this->__('Custom template') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 80,
                    'title' => $this->__('Example') . ': itemlist_[objectType]_display.html.twig'
                ],
                'help' => $this->__('Example') . ': <em>itemlist_[objectType]_display.html.twig</em>'
            ])
        ;
    }

    /**
     * Adds a filter field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addFilterField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('filter', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Filter (expert option)') . ':',
            'required' => false,
            'attr' => [
                'maxlength' => 255
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rkparkhausmodule_listblock';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'objectType' => 'vehicle',
                'isCategorisable' => false,
                'categoryHelper' => null
            ])
            ->setRequired(['objectType'])
            ->setOptional(['isCategorisable', 'categoryHelper'])
            ->setAllowedTypes([
                'objectType' => 'string',
                'isCategorisable' => 'bool'
            ])
        ;
    }
}
