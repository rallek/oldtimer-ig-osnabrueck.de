<?php
/**
 * WebsiteHelper.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\WebsiteHelperModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DoctrineExtensions\StandardFields\Mapping\Annotation as ZK;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

use DataUtil;
use FormUtil;
use RuntimeException;
use ServiceUtil;
use UserUtil;
use Zikula_Workflow_Util;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for linker entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 *
 * @abstract
 */
abstract class AbstractLinkerEntity extends EntityAccess
{
    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'linker';
    
    /**
     * @Assert\Type(type="bool")
     * @var boolean Option to bypass validation if needed
     */
    protected $_bypassValidation = false;
    
    /**
     * @var array The current workflow data of this object
     */
    protected $__WORKFLOW__ = [];
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=1000000000, message="Length of field value must not be higher than 9.")) {
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getWorkflowStateAllowedValues", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * Linker image meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $linkerImageMeta
     */
    protected $linkerImageMeta = [];
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
        mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
        allowSquare = false,
        allowPortrait = false
     * )
     * @var string $linkerImage
     */
    protected $linkerImage = null;
    
    /**
     * Full linker image path as url.
     *
     * @Assert\Type(type="string")
     * @Assert\Url()
     * @var string $linkerImageUrl
     */
    protected $linkerImageUrl = '';
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $linkerHeadline
     */
    protected $linkerHeadline = '';
    
    /**
     * @ORM\Column(type="text", length=2000)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="2000")
     * @var text $linkerText
     */
    protected $linkerText = '';
    
    /**
     * You must be carefull with the link settings. It is not validated!
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $theLink
     */
    protected $theLink = '';
    
    /**
     * see the definitions at the bootstrap documentation
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $boostrapSetting
     */
    protected $boostrapSetting = 'col-xs-12 col-sm-6 col-md-3';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Regex(pattern="/\s/", match=false, message="This value must not contain space chars.")
     * @Assert\Length(min="0", max="255")
     * @Assert\Locale()
     * @var string $linkerLanguage
     */
    protected $linkerLanguage = '';
    
    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=2147483647, message="Length of field value must not be higher than 11.")) {
     * @var integer $sorting
     */
    protected $sorting = 0;
    
    /**
     * a field to be used for block filtering. We may want to filter the same string here. Please do not use spaces an scpecial characters.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Regex(pattern="/\s/", match=false, message="This value must not contain space chars.")
     * @Assert\Length(min="0", max="255")
     * @var string $linkerGroup
     */
    protected $linkerGroup = '';
    
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="create")
     * @var integer $createdUserId
     */
    protected $createdUserId;
    
    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="update")
     * @var integer $updatedUserId
     */
    protected $updatedUserId;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Assert\DateTime()
     * @var \DateTime $createdDate
     */
    protected $createdDate;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Assert\DateTime()
     * @var \DateTime $updatedDate
     */
    protected $updatedDate;
    
    
    /**
     * Constructor.
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     *
     * @param TODO
     */
    public function __construct()
    {
        $this->sorting = 1;
        $this->initWorkflow();
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        $this->_objectType = $_objectType;
    }
    
    /**
     * Returns the _bypass validation.
     *
     * @return boolean
     */
    public function get_bypassValidation()
    {
        return $this->_bypassValidation;
    }
    
    /**
     * Sets the _bypass validation.
     *
     * @param boolean $_bypassValidation
     *
     * @return void
     */
    public function set_bypassValidation($_bypassValidation)
    {
        $this->_bypassValidation = $_bypassValidation;
    }
    
    /**
     * Returns the __ w o r k f l o w__.
     *
     * @return array
     */
    public function get__WORKFLOW__()
    {
        return $this->__WORKFLOW__;
    }
    
    /**
     * Sets the __ w o r k f l o w__.
     *
     * @param array $__WORKFLOW__
     *
     * @return void
     */
    public function set__WORKFLOW__($__WORKFLOW__ = [])
    {
        $this->__WORKFLOW__ = $__WORKFLOW__;
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        $this->workflowState = isset($workflowState) ? $workflowState : '';
    }
    
    /**
     * Returns the linker image.
     *
     * @return string
     */
    public function getLinkerImage()
    {
        return $this->linkerImage;
    }
    
    /**
     * Sets the linker image.
     *
     * @param string $linkerImage
     *
     * @return void
     */
    public function setLinkerImage($linkerImage)
    {
        $this->linkerImage = isset($linkerImage) ? $linkerImage : '';
    }
    
    /**
     * Returns the linker image url.
     *
     * @return string
     */
    public function getLinkerImageUrl()
    {
        return $this->linkerImageUrl;
    }
    
    /**
     * Sets the linker image url.
     *
     * @param string $linkerImageUrl
     *
     * @return void
     */
    public function setLinkerImageUrl($linkerImageUrl)
    {
        $this->linkerImageUrl = isset($linkerImageUrl) ? $linkerImageUrl : '';
    }
    
    /**
     * Returns the linker image meta.
     *
     * @return array
     */
    public function getLinkerImageMeta()
    {
        return $this->linkerImageMeta;
    }
    
    /**
     * Sets the linker image meta.
     *
     * @param array $linkerImageMeta
     *
     * @return void
     */
    public function setLinkerImageMeta($linkerImageMeta = [])
    {
        $this->linkerImageMeta = isset($linkerImageMeta) ? $linkerImageMeta : '';
    }
    
    /**
     * Returns the linker headline.
     *
     * @return string
     */
    public function getLinkerHeadline()
    {
        return $this->linkerHeadline;
    }
    
    /**
     * Sets the linker headline.
     *
     * @param string $linkerHeadline
     *
     * @return void
     */
    public function setLinkerHeadline($linkerHeadline)
    {
        $this->linkerHeadline = isset($linkerHeadline) ? $linkerHeadline : '';
    }
    
    /**
     * Returns the linker text.
     *
     * @return text
     */
    public function getLinkerText()
    {
        return $this->linkerText;
    }
    
    /**
     * Sets the linker text.
     *
     * @param text $linkerText
     *
     * @return void
     */
    public function setLinkerText($linkerText)
    {
        $this->linkerText = isset($linkerText) ? $linkerText : '';
    }
    
    /**
     * Returns the the link.
     *
     * @return string
     */
    public function getTheLink()
    {
        return $this->theLink;
    }
    
    /**
     * Sets the the link.
     *
     * @param string $theLink
     *
     * @return void
     */
    public function setTheLink($theLink)
    {
        $this->theLink = isset($theLink) ? $theLink : '';
    }
    
    /**
     * Returns the boostrap setting.
     *
     * @return string
     */
    public function getBoostrapSetting()
    {
        return $this->boostrapSetting;
    }
    
    /**
     * Sets the boostrap setting.
     *
     * @param string $boostrapSetting
     *
     * @return void
     */
    public function setBoostrapSetting($boostrapSetting)
    {
        $this->boostrapSetting = isset($boostrapSetting) ? $boostrapSetting : '';
    }
    
    /**
     * Returns the linker language.
     *
     * @return string
     */
    public function getLinkerLanguage()
    {
        return $this->linkerLanguage;
    }
    
    /**
     * Sets the linker language.
     *
     * @param string $linkerLanguage
     *
     * @return void
     */
    public function setLinkerLanguage($linkerLanguage)
    {
        $this->linkerLanguage = isset($linkerLanguage) ? $linkerLanguage : '';
    }
    
    /**
     * Returns the sorting.
     *
     * @return integer
     */
    public function getSorting()
    {
        return $this->sorting;
    }
    
    /**
     * Sets the sorting.
     *
     * @param integer $sorting
     *
     * @return void
     */
    public function setSorting($sorting)
    {
        $this->sorting = intval($sorting);
    }
    
    /**
     * Returns the linker group.
     *
     * @return string
     */
    public function getLinkerGroup()
    {
        return $this->linkerGroup;
    }
    
    /**
     * Sets the linker group.
     *
     * @param string $linkerGroup
     *
     * @return void
     */
    public function setLinkerGroup($linkerGroup)
    {
        $this->linkerGroup = $linkerGroup;
    }
    
    /**
     * Returns the created user id.
     *
     * @return integer
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }
    
    /**
     * Sets the created user id.
     *
     * @param integer $createdUserId
     *
     * @return void
     */
    public function setCreatedUserId($createdUserId)
    {
        $this->createdUserId = $createdUserId;
    }
    
    /**
     * Returns the updated user id.
     *
     * @return integer
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }
    
    /**
     * Sets the updated user id.
     *
     * @param integer $updatedUserId
     *
     * @return void
     */
    public function setUpdatedUserId($updatedUserId)
    {
        $this->updatedUserId = $updatedUserId;
    }
    
    /**
     * Returns the created date.
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    
    /**
     * Sets the created date.
     *
     * @param \DateTime $createdDate
     *
     * @return void
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }
    
    /**
     * Returns the updated date.
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
    
    /**
     * Sets the updated date.
     *
     * @param \DateTime $updatedDate
     *
     * @return void
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }
    
    
    
    
    /**
     * Returns the formatted title conforming to the display pattern
     * specified for this entity.
     *
     * @return string The display title
     */
    public function getTitleFromDisplayPattern()
    {
        $serviceManager = ServiceUtil::getManager();
        $listHelper = $serviceManager->get('rk_websitehelper_module.listentries_helper');
    
        $formattedTitle = ''
                . $this->getLinkerHeadline();
    
        return $formattedTitle;
    }
    
    
    /**
     * Returns a list of possible choices for the workflowState list field.
     * This method is used for validation.
     *
     * @return array List of allowed choices
     */
    public static function getWorkflowStateAllowedValues()
    {
        $serviceManager = ServiceUtil::getManager();
        $helper = $serviceManager->get('rk_websitehelper_module.listentries_helper');
        $listEntries = $helper->getWorkflowStateEntriesForLinker();
    
        $allowedValues = ['initial'];
        foreach ($listEntries as $entry) {
            $allowedValues[] = $entry['value'];
        }
    
        return $allowedValues;
    }
    
    /**
     * Sets/retrieves the workflow details.
     *
     * @param boolean $forceLoading load the workflow record
     *
     * @throws RuntimeException Thrown if retrieving the workflow object fails
     */
    public function initWorkflow($forceLoading = false)
    {
        $currentFunc = FormUtil::getPassedValue('func', 'index', 'GETPOST', FILTER_SANITIZE_STRING);
        $isReuse = FormUtil::getPassedValue('astemplate', '', 'GETPOST', FILTER_SANITIZE_STRING);
    
        // apply workflow with most important information
        $idColumn = 'id';
        
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = $serviceManager->get('rk_websitehelper_module.workflow_helper');
        
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = [
            'module' => 'RKWebsiteHelperModule',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => $idColumn,
            'obj_id' => $this[$idColumn],
            'schemaname' => $schemaName
        ];
        
        // load the real workflow only when required (e. g. when func is edit or delete)
        if ((!in_array($currentFunc, ['index', 'view', 'display']) && empty($isReuse)) || $forceLoading) {
            $result = Zikula_Workflow_Util::getWorkflowForObject($this, $this['_objectType'], $idColumn, 'RKWebsiteHelperModule');
            if (!$result) {
                $flashBag = $serviceManager->get('session')->getFlashBag();
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Could not load the associated workflow.'));
            }
        }
        
        if (!is_object($this['__WORKFLOW__']) && !isset($this['__WORKFLOW__']['schemaname'])) {
            $workflow = $this['__WORKFLOW__'];
            $workflow['schemaname'] = $schemaName;
            $this['__WORKFLOW__'] = $workflow;
        }
    }
    
    /**
     * Resets workflow data back to initial state.
     * To be used after cloning an entity object.
     */
    public function resetWorkflow()
    {
        $this->setWorkflowState('initial');
    
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = $serviceManager->get('rk_websitehelper_module.workflow_helper');
    
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = [
            'module' => 'RKWebsiteHelperModule',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => 'id',
            'obj_id' => 0,
            'schemaname' => $schemaName
        ];
    }
    
    /**
     * Start validation and raise exception if invalid data is found.
     *
     * @return boolean Whether everything is valid or not
     */
    public function validate()
    {
        if (true === $this->_bypassValidation) {
            return true;
        }
    
        $serviceManager = ServiceUtil::getManager();
    
        $validator = $serviceManager->get('validator');
        $errors = $validator->validate($this);
    
        if (count($errors) > 0) {
            $flashBag = $serviceManager->get('session')->getFlashBag();
            foreach ($errors as $error) {
                $flashBag->add('error', $error->getMessage());
            }
    
            return false;
        }
    
        return true;
    }
    
    /**
     * Return entity data in JSON format.
     *
     * @return string JSON-encoded data
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array The resulting arguments list
     */
    public function createUrlArgs()
    {
        $args = [];
    
        $args['id'] = $this['id'];
    
        if (property_exists($this, 'slug')) {
            $args['slug'] = $this['slug'];
        }
    
        return $args;
    }
    
    /**
     * Create concatenated identifier string (for composite keys).
     *
     * @return String concatenated identifiers
     */
    public function createCompositeIdentifier()
    {
        $itemId = $this['id'];
    
        return $itemId;
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'rkwebsitehelpermodule.ui_hooks.linkers';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects The objects are added to this array. Default: []
     * 
     * @return array of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = []) 
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Linker ' . $this->createCompositeIdentifier();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // If the entity has an identity, proceed as normal.
        if ($this->id) {
            // unset identifiers
            $this->setId(0);
    
            // reset Workflow
            $this->resetWorkflow();
    
            // reset upload fields
            $this->setLinkerImage('');
            $this->setLinkerImageMeta([]);
    
            $this->setCreatedDate(null);
            $this->setCreatedUserId(null);
            $this->setUpdatedDate(null);
            $this->setUpdatedUserId(null);
    
        }
        // otherwise do nothing, do NOT throw an exception!
    }
}
