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

namespace RK\ParkHausModule\Entity\Factory\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

/**
 * Factory class used to retrieve entity and repository instances.
 *
 * This is the base factory class for vehicle entities.
 */
abstract class AbstractVehicleFactory
{
    /**
     * @var String Full qualified class name to be used for vehicles.
     */
    protected $className;

    /**
     * @var ObjectManager The object manager to be used for determining the repository
     */
    protected $objectManager;

    /**
     * @var EntityRepository The currently used repository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectManager $om        The object manager to be used for determining the repository
     * @param String        $className Full qualified class name to be used for vehicles
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->className = $className;
        $this->objectManager = $om;
        $this->repository = $this->objectManager->getRepository($className);
    }

    public function createVehicle()
    {
        $entityClass = $this->className;

        return new $entityClass();
    }

    /**
     * Returns the class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Sets the class name.
     *
     * @param string $className
     *
     * @return void
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }
    
    /**
     * Returns the object manager.
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
    
    /**
     * Sets the object manager.
     *
     * @param ObjectManager $objectManager
     *
     * @return void
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;
    }
    
    /**
     * Returns the repository.
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * Sets the repository.
     *
     * @param EntityRepository $repository
     *
     * @return void
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
    
}
