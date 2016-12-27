<?php
/**
 * Websitehelper.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace RK\WebsitehelperModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use RK\WebsitehelperModule\Entity\Factory\CarouselFactory;
use RK\WebsitehelperModule\Helper\ListEntriesHelper;

/**
 * Carousel editing form type base class.
 */
abstract class AbstractCarouselType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var CarouselFactory
     */
    protected $entityFactory;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * CarouselType constructor.
     *
     * @param TranslatorInterface $translator    Translator service instance
     * @param CarouselFactory        $entityFactory Entity factory service instance
     * @param ListEntriesHelper   $listHelper    ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, CarouselFactory $entityFactory, ListEntriesHelper $listHelper)
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
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('carouselName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Carousel name') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the carousel name of the carousel')
            ],'required' => true,
            'max_length' => 255,
        ]);
        $builder->add('remarks', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Remarks') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('make a note for which usecase you create this carousel')
            ],
            'help' => $this->__('make a note for which usecase you create this carousel'),
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the remarks of the carousel')
            ],'required' => false,
            'max_length' => 255,
        ]);
        $builder->add('slidingTime', 'Symfony\Component\Form\Extension\Core\Type\IntegerType', [
            'label' => $this->__('Sliding time') . ':',
            'empty_data' => '5000',
            'attr' => [
                'class' => ' validate-digits',
                'title' => $this->__('Enter the sliding time of the carousel. Only digits are allowed.')
            ],'required' => true,
            'max_length' => 11,
            'scale' => 0
        ]);
        $builder->add('controls', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'label' => $this->__('Controls') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('check if controlls should be shown')
            ],
            'help' => $this->__('check if controlls should be shown'),
            'attr' => [
                'class' => '',
                'title' => $this->__('controls ?')
            ],'required' => false,
        ]);
        $builder->add('carouselGroup', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'label' => $this->__('Carousel group') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This field is for filtering in the block settings. So it makes it possible to have more than one carousel managed.')
            ],
            'help' => $this->__('This field is for filtering in the block settings. So it makes it possible to have more than one carousel managed.'),
            'empty_data' => '',
            'attr' => [
                'class' => ' validate-nospace',
                'title' => $this->__('Enter the carousel group of the carousel')
            ],'required' => false,
            'max_length' => 255,
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
        return 'rkwebsitehelpermodule_carousel';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'RK\WebsitehelperModule\Entity\CarouselEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createCarousel();
                },
                'error_mapping' => [
                ],
                'mode' => 'create',
                'actions' => [],
                'inlineUsage' => false
            ])
            ->setRequired(['mode', 'actions'])
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
