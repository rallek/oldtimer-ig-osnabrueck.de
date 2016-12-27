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

namespace RK\WebsitehelperModule\ContentType\Base;

use ModUtil;
use ServiceUtil;

/**
 * Generic single item display content plugin base class.
 */
abstract class AbstractItem extends \Content_AbstractContentType
{
    protected $objectType;
    protected $id;
    protected $displayMode;
    
    /**
     * Returns the module providing this content type.
     *
     * @return string The module name
     */
    public function getModule()
    {
        return 'RKWebsitehelperModule';
    }
    
    /**
     * Returns the name of this content type.
     *
     * @return string The content type name
     */
    public function getName()
    {
        return 'Item';
    }
    
    /**
     * Returns the title of this content type.
     *
     * @return string The content type title
     */
    public function getTitle()
    {
        $serviceManager = ServiceUtil::getManager();
    
        return $serviceManager->get('translator.default')->__('RKWebsitehelperModule detail view');
    }
    
    /**
     * Returns the description of this content type.
     *
     * @return string The content type description
     */
    public function getDescription()
    {
        $serviceManager = ServiceUtil::getManager();
    
        return $serviceManager->get('translator.default')->__('Display or link a single RKWebsitehelperModule object.');
    }
    
    /**
     * Loads the data.
     *
     * @param array $data Data array with parameters
     */
    public function loadData(&$data)
    {
        $serviceManager = ServiceUtil::getManager();
        $controllerHelper = $serviceManager->get('rk_websitehelper_module.controller_helper');
    
        $utilArgs = ['name' => 'detail'];
        if (!isset($data['objectType']) || !in_array($data['objectType'], $controllerHelper->getObjectTypes('contentType', $utilArgs))) {
            $data['objectType'] = $controllerHelper->getDefaultObjectType('contentType', $utilArgs);
        }
    
        $this->objectType = $data['objectType'];
    
        if (!isset($data['id'])) {
            $data['id'] = null;
        }
        if (!isset($data['displayMode'])) {
            $data['displayMode'] = 'embed';
        }
    
        $this->id = $data['id'];
        $this->displayMode = $data['displayMode'];
    }
    
    /**
     * Displays the data.
     *
     * @return string The returned output
     */
    public function display()
    {
        if (null !== $this->id && !empty($this->displayMode)) {
            return ModUtil::func('RKWebsitehelperModule', 'external', 'display', $this->getDisplayArguments());
        }
    
        return '';
    }
    
    /**
     * Displays the data for editing.
     */
    public function displayEditing()
    {
        if (null !== $this->id && !empty($this->displayMode)) {
            return ModUtil::func('RKWebsitehelperModule', 'external', 'display', $this->getDisplayArguments());
        }
    
        $serviceManager = ServiceUtil::getManager();
        
        return $serviceManager->get('translator.default')->__('No item selected.');
    }
    
    /**
     * Returns common arguments for display data selection with the external api.
     *
     * @return array Display arguments
     */
    protected function getDisplayArguments()
    {
        return [
            'objectType' => $this->objectType,
            'source' => 'contentType',
            'displayMode' => $this->displayMode,
            'id' => $this->id
        ];
    }
    
    /**
     * Returns the default data.
     *
     * @return array Default data and parameters
     */
    public function getDefaultData()
    {
        return [
            'objectType' => 'linker',
             'id' => null,
             'displayMode' => 'embed'
         ];
    }
    
    /**
     * Executes additional actions for the editing mode.
     */
    public function startEditing()
    {
        // ensure our custom plugins are loaded
        array_push($this->view->plugins_dir, 'modules/RKWebsitehelperModule/Resources/views//plugins');
    
        // required as parameter for the item selector plugin
        $this->view->assign('objectType', $this->objectType);
    }
    
    /**
     * Returns the edit template path.
     *
     * @return string
     */
    public function getEditTemplate()
    {
        $absoluteTemplatePath = str_replace('ContentType/Base/AbstractItem.php', 'Resources/views/ContentType/item_edit.tpl', __FILE__);
    
        return 'file:' . $absoluteTemplatePath;
    }
}
