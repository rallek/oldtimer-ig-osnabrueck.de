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

namespace RK\ParkHausModule\Helper\Base;

use InvalidArgumentException;
use Zikula\Common\Translator\TranslatorInterface;
use RK\ParkHausModule\Entity\Factory\ParkHausFactory;
use RK\ParkHausModule\Helper\ControllerHelper;

/**
 * Selection helper base class.
 */
abstract class AbstractSelectionHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ParkHausFactory
     */
    private $entityFactory;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * SelectionHelper constructor.
     *
     * @param TranslatorInterface $translator       Translator service instance
     * @param ParkHausFactory $entityFactory ParkHausFactory service instance
     * @param ControllerHelper    $controllerHelper ControllerHelper service instance
     */
    public function __construct(TranslatorInterface $translator, ParkHausFactory $entityFactory, ControllerHelper $controllerHelper)
    {
        $this->translator = $translator;
        $this->entityFactory = $entityFactory;
        $this->controllerHelper = $controllerHelper;
    }

    /**
     * Gets the list of identifier fields for a given object type.
     *
     * @param string $objectType The object type to be treated (optional)
     *
     * @return array List of identifier field names
     */
    public function getIdFields($objectType = '')
    {
        $objectType = $this->determineObjectType($objectType, 'getIdFields');
        $entityClass = 'RKParkHausModule:' . ucfirst($objectType) . 'Entity';
    
        $meta = $this->entityFactory->getObjectManager()->getClassMetadata($entityClass);
    
        if ($this->hasCompositeKeys($objectType)) {
            $idFields = $meta->getIdentifierFieldNames();
        } else {
            $idFields = [$meta->getSingleIdentifierFieldName()];
        }
    
        return $idFields;
    }
    
    /**
     * Checks whether a certain entity type uses composite keys or not.
     *
     * @param string $objectType The object type to retrieve
     *
     * @return boolean Whether composite keys are used or not
     */
    protected function hasCompositeKeys($objectType)
    {
        return $this->controllerHelper->hasCompositeKeys($objectType);
    }
    
    /**
     * Selects a single entity.
     *
     * @param string $objectType The object type to be treated (optional)
     * @param mixed  $id         The id (or array of ids) to use to retrieve the object (default=null)
     * @param boolean $useJoins  Whether to include joining related objects (optional) (default=true)
     * @param boolean $slimMode  If activated only some basic fields are selected without using any joins (optional) (default=false)
     *
     * @return mixed Desired entity object or null
     */
    public function getEntity($objectType = '', $id = '', $useJoins = true, $slimMode = false)
    {
        if (empty($id)) {
            throw new InvalidArgumentException($this->translator->__('Invalid identifier received.'));
        }
    
        $objectType = $this->determineObjectType($objectType, 'getEntity');
        $repository = $this->getRepository($objectType);
    
        $useJoins = (bool) $useJoins;
        $slimMode = (bool) $slimMode; 
    
        $entity = $repository->selectById($id, $useJoins, $slimMode);
    
        return $entity;
    }
    
    /**
     * Selects a list of entities by different criteria.
     *
     * @param string  $objectType The object type to retrieve (optional)
     * @param string  $idList     A list of ids to select (optional) (default=[])
     * @param string  $where      The where clause to use when retrieving the collection (optional) (default='')
     * @param string  $orderBy    The order-by clause to use when retrieving the collection (optional) (default='')
     * @param boolean $useJoins   Whether to include joining related objects (optional) (default=true)
     * @param boolean $slimMode   If activated only some basic fields are selected without using any joins (optional) (default=false)
     *
     * @return array with retrieved collection
     */
    public function getEntities($objectType = '', array $idList = [], $where = '', $orderBy = '', $useJoins = true, $slimMode = false)
    {
        $objectType = $this->determineObjectType($objectType, 'getEntities');
        $repository = $this->getRepository($objectType);
    
        $useJoins = (bool) $useJoins;
        $slimMode = (bool) $slimMode; 
    
        if (!empty($idList)) {
           return $repository->selectByIdList($idList, $useJoins, $slimMode);
        }
    
        return $repository->selectWhere($where, $orderBy, $useJoins, $slimMode);
    }
    
    /**
     * Selects a list of entities by different criteria.
     *
     * @param string  $objectType     The object type to retrieve (optional)
     * @param string  $where          The where clause to use when retrieving the collection (optional) (default='')
     * @param string  $orderBy        The order-by clause to use when retrieving the collection (optional) (default='')
     * @param integer $currentPage    Where to start selection
     * @param integer $resultsPerPage Amount of items to select
     * @param boolean $useJoins       Whether to include joining related objects (optional) (default=true)
     * @param boolean $slimMode       If activated only some basic fields are selected without using any joins (optional) (default=false)
     *
     * @return array with retrieved collection and amount of total records affected by this query
     */
    public function getEntitiesPaginated($objectType = '', $where = '', $orderBy = '', $currentPage = 1, $resultsPerPage = 25, $useJoins = true, $slimMode = false)
    {
        $objectType = $this->determineObjectType($objectType, 'getEntitiesPaginated');
        $repository = $this->getRepository($objectType);
    
        $useJoins = (bool) $useJoins;
        $slimMode = (bool) $slimMode; 
    
        return $repository->selectWherePaginated($where, $orderBy, $currentPage, $resultsPerPage, $useJoins, $slimMode);
    }
    
    /**
     * Determines object type using controller util methods.
     *
     * @param string $objectType The object type to be treated (optional)
     * @param string $methodName Name of calling method
     *
     * @return string the object type
     */
    protected function determineObjectType($objectType = '', $methodName = '')
    {
        $contextArgs = ['helper' => 'selection', 'action' => $methodName];
        if (!in_array($objectType, $this->controllerHelper->getObjectTypes('helper', $contextArgs))) {
            $objectType = $this->controllerHelper->getDefaultObjectType('helper', $contextArgs);
        }
    
        return $objectType;
    }
    
    /**
     * Returns repository instance for a certain object type.
     *
     * @param string $objectType The desired object type
     *
     * @return mixed Repository class instance or null
     */
    protected function getRepository($objectType = '')
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException($this->translator->__('Invalid object type received.'));
        }
    
        return $this->entityFactory->getRepository($objectType);
    }
}
