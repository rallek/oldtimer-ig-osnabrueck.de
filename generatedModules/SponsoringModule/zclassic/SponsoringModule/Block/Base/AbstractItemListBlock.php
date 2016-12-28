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

namespace RK\SponsoringModule\Block\Base;

use Zikula\BlocksModule\AbstractBlockHandler;
use Zikula\Core\AbstractBundle;

/**
 * Generic item list block base class.
 */
abstract class AbstractItemListBlock extends AbstractBlockHandler
{
    /**
     * Display the block content.
     *
     * @param array $properties The block properties array
     *
     * @return array|string
     */
    public function display(array $properties)
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('RKSponsoringModule:ItemListBlock:', "$properties[title]::", ACCESS_OVERVIEW)) {
            return false;
        }
    
        // set default values for all params which are not properly set
        $defaults = $this->getDefaults();
        $properties = array_merge($defaults, $properties);
    
        $controllerHelper = $this->get('rk_sponsoring_module.controller_helper');
        $utilArgs = ['name' => 'list'];
        if (!isset($properties['objectType']) || !in_array($properties['objectType'], $controllerHelper->getObjectTypes('block', $utilArgs))) {
            $properties['objectType'] = $controllerHelper->getDefaultObjectType('block', $utilArgs);
        }
    
        $objectType = $properties['objectType'];
    
        $repository = $this->get('rk_sponsoring_module.' . $objectType . '_factory')->getRepository();
    
    
        // create query
        $where = $properties['filter'];
        $orderBy = $this->getSortParam($properties, $repository);
        $qb = $repository->genericBaseQuery($where, $orderBy);
    
        // get objects from database
        $currentPage = 1;
        $resultsPerPage = $properties['amount'];
        list($query, $count) = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        list($entities, $objectCount) = $repository->retrieveCollectionResult($query, $orderBy, true);
    
        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->__('RKSponsoringModule items');
        }
    
        $template = $this->getDisplayTemplate($properties);
    
        $templateParameters = [
            'vars' => $properties,
            'objectType' => $objectType,
            'items' => $entities
        ];
        $imageHelper = $this->get('rk_sponsoring_module.image_helper');
        $templateParameters = array_merge($templateParameters, $repository->getAdditionalTemplateParameters($imageHelper, 'block'));
    
        return $this->renderView($template, $templateParameters);
    }
    
    /**
     * Returns the template used for output.
     *
     * @param array $properties The block properties array
     *
     * @return string the template path
     */
    protected function getDisplayTemplate(array $properties)
    {
        $templateFile = $properties['template'];
        if ($templateFile == 'custom') {
            $templateFile = $properties['customTemplate'];
        }
    
        $templateForObjectType = str_replace('itemlist_', 'itemlist_' . $properties['objectType'] . '_', $templateFile);
        
        $templateDirectory = str_replace('Block/Base/AbstractItemListBlock.php', 'Resources/views/', __FILE__);
    
        $template = '';
        if (file_exists($templateDirectory . 'ContentType/' . $templateForObjectType)) {
            $template = 'ContentType/' . $templateForObjectType;
        } elseif (file_exists($templateDirectory . 'Block/' . $templateForObjectType)) {
            $template = 'Block/' . $templateForObjectType;
        } elseif (file_exists($templateDirectory . 'ContentType/' . $templateFile)) {
            $template = 'ContentType/' . $templateFile;
        } elseif (file_exists($templateDirectory . 'Block/' . $templateFile)) {
            $template = 'Block/' . $templateFile;
        } else {
            $template = 'Block/itemlist.html.twig';
        }
        $template = '@RKSponsoringModule/' . $template;
    
        return $template;
    }
    
    /**
     * Determines the order by parameter for item selection.
     *
     * @param array               $properties The block properties array
     * @param Doctrine_Repository $repository The repository used for data fetching
     *
     * @return string the sorting clause
     */
    protected function getSortParam(array $properties, $repository)
    {
        if ($properties['sorting'] == 'random') {
            return 'RAND()';
        }
    
        $sortParam = '';
        if ($properties['sorting'] == 'newest') {
            $selectionHelper = $this->get('rk_sponsoring_module.selection_helper');
            $idFields = $selectionHelper->getIdFields($properties['objectType']);
            if (count($idFields) == 1) {
                $sortParam = $idFields[0] . ' DESC';
            } else {
                foreach ($idFields as $idField) {
                    if (!empty($sortParam)) {
                        $sortParam .= ', ';
                    }
                    $sortParam .= $idField . ' DESC';
                }
            }
        } elseif ($properties['sorting'] == 'default') {
            $sortParam = $repository->getDefaultSortingField() . ' ASC';
        }
    
        return $sortParam;
    }
    
    /**
     * Returns the fully qualified class name of the block's form class.
     *
     * @return string Template path
     */
    public function getFormClassName()
    {
        return 'RK\SponsoringModule\Block\Form\Type\ItemListBlockType';
    }
    
    /**
     * Returns any array of form options.
     *
     * @return array Options array
     */
    public function getFormOptions()
    {
        $objectType = 'sponsor';
    
        $request = $this->get('request_stack')->getCurrentRequest();
        if ($request->attributes->has('blockEntity')) {
            $blockEntity = $request->attributes->get('blockEntity');
            if (is_object($blockEntity) && method_exists($blockEntity, 'getContent')) {
                $blockProperties = $blockEntity->getContent();
                if (isset($blockProperties['objectType'])) {
                    $objectType = $blockProperties['objectType'];
                }
            }
        }
    
        return [
            'objectType' => $objectType
        ];
    }
    
    /**
     * Returns the template used for rendering the editing form.
     *
     * @return string Template path
     */
    public function getFormTemplate()
    {
        return '@RKSponsoringModule/Block/itemlist_modify.html.twig';
    }
    
    /**
     * Returns default settings for this block.
     *
     * @return array The default settings
     */
    protected function getDefaults()
    {
        $defaults = [
            'objectType' => 'sponsor',
            'sorting' => 'default',
            'amount' => 5,
            'template' => 'itemlist_display.html.twig',
            'customTemplate' => '',
            'filter' => ''
        ];
    
        return $defaults;
    }
    
}
