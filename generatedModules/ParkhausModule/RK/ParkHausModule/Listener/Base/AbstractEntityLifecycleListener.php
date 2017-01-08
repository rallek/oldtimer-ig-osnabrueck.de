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

namespace RK\ParkHausModule\Listener\Base;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use ServiceUtil;
use Symfony\Component\HttpFoundation\File\File;
use Zikula\Core\Doctrine\EntityAccess;
use RK\ParkHausModule\ParkHausEvents;
use RK\ParkHausModule\Event\FilterVehicleEvent;
use RK\ParkHausModule\Event\FilterVehicleImageEvent;

/**
 * Event subscriber base class for entity lifecycle events.
 */
abstract class AbstractEntityLifecycleListener implements EventSubscriber
{
    /**
     * Returns list of events to subscribe.
     *
     * @return array list of events
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::postRemove,
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::postLoad
        ];
    }

    /**
     * The preRemove event occurs for a given entity before the respective EntityManager
     * remove operation for that entity is executed. It is not called for a DQL DELETE statement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        $serviceManager = ServiceUtil::getManager();
        $dispatcher = $serviceManager->get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($entity->get_objectType()) . '_PRE_REMOVE'), $event);
        if ($event->isPropagationStopped()) {
            return false;
        }
        
        // delete workflow for this entity
        $workflowHelper = $serviceManager->get('rk_parkhaus_module.workflow_helper');
        $workflowHelper->normaliseWorkflowData($entity);
        $workflow = $entity['__WORKFLOW__'];
        if ($workflow['id'] > 0) {
            $entityManager = $serviceManager->get('doctrine.orm.default_entity_manager');
            $result = true;
            try {
                $workflow = $entityManager->find('Zikula\Core\Doctrine\Entity\WorkflowEntity', $workflow['id']);
                $entityManager->remove($workflow);
                $entityManager->flush();
            } catch (\Exception $e) {
                $result = false;
            }
            if (false === $result) {
                $flashBag = $serviceManager->get('session')->getFlashBag();
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Could not remove stored workflow. Deletion has been aborted.'));
        
                return false;
            }
        }
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be
     * invoked after the database delete operations. It is not called for a DQL DELETE statement.
     *
     * Note that the postRemove event or any events triggered after an entity removal can receive
     * an uninitializable proxy in case you have configured an entity to cascade remove relations.
     * In this case, you should load yourself the proxy in the associated pre event.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        $serviceManager = ServiceUtil::getManager();
        
        $objectType = $entity->get_objectType();
        $objectId = $entity->createCompositeIdentifier();
        
        $uploadHelper = $serviceManager->get('rk_parkhaus_module.upload_helper');
        $uploadFields = $this->getUploadFields($objectType);
        
        foreach ($uploadFields as $uploadField) {
            if (empty($entity[$uploadField])) {
                continue;
            }
        
            // remove upload file
            $uploadHelper->deleteUploadFile($entity, $uploadField);
        }
        
        $logger = $serviceManager->get('logger');
        $logArgs = ['app' => 'RKParkHausModule', 'user' => $serviceManager->get('zikula_users_module.current_user')->get('uname'), 'entity' => $objectType, 'id' => $objectId];
        $logger->debug('{app}: User {user} removed the {entity} with id {id}.', $logArgs);
        
        $dispatcher = $serviceManager->get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($objectType) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($objectType) . '_POST_REMOVE'), $event);
    }

    /**
     * The prePersist event occurs for a given entity before the respective EntityManager
     * persist operation for that entity is executed. It should be noted that this event
     * is only triggered on initial persist of an entity (i.e. it does not trigger on future updates).
     *
     * Doctrine will not recognize changes made to relations in a prePersist event.
     * This includes modifications to collections such as additions, removals or replacement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        $objectType = $entity->get_objectType();
        $uploadFields = $this->getUploadFields($objectType);
        
        foreach ($uploadFields as $uploadField) {
            if (empty($entity[$uploadField])) {
                continue;
            }
        
            if (!($entity[$uploadField] instanceof File)) {
                $entity[$uploadField] = new File($entity[$uploadField]);
            }
            $entity[$uploadField] = $entity[$uploadField]->getFilename();
        }
        
        $dispatcher = ServiceUtil::get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($entity->get_objectType()) . '_PRE_PERSIST'), $event);
        if ($event->isPropagationStopped()) {
            return false;
        }
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations. Generated primary key values
     * are available in the postPersist event.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        $serviceManager = ServiceUtil::getManager();
        $objectId = $entity->createCompositeIdentifier();
        $logger = $serviceManager->get('logger');
        $logArgs = ['app' => 'RKParkHausModule', 'user' => $serviceManager->get('zikula_users_module.current_user')->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $objectId];
        $logger->debug('{app}: User {user} created the {entity} with id {id}.', $logArgs);
        
        $dispatcher = $serviceManager->get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($entity->get_objectType()) . '_POST_PERSIST'), $event);
    }

    /**
     * The preUpdate event occurs before the database update operations to entity data.
     * It is not called for a DQL UPDATE statement nor when the computed changeset is empty.
     *
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preupdate
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        $objectType = $entity->get_objectType();
        $uploadFields = $this->getUploadFields($objectType);
        
        foreach ($uploadFields as $uploadField) {
            if (empty($entity[$uploadField])) {
                continue;
            }
        
            if (!($entity[$uploadField] instanceof File)) {
                $entity[$uploadField] = new File($entity[$uploadField]);
            }
            $entity[$uploadField] = $entity[$uploadField]->getFilename();
        }
        
        $serviceManager = ServiceUtil::getManager();
        $dispatcher = $serviceManager->get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($entity->get_objectType()) . '_PRE_UPDATE'), $event);
        if ($event->isPropagationStopped()) {
            return false;
        }
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        $serviceManager = ServiceUtil::getManager();
        $objectId = $entity->createCompositeIdentifier();
        $logger = $serviceManager->get('logger');
        $logArgs = ['app' => 'RKParkHausModule', 'user' => $serviceManager->get('zikula_users_module.current_user')->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $objectId];
        $logger->debug('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        
        $dispatcher = $serviceManager->get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($entity->get_objectType()) . '_POST_UPDATE'), $event);
    }

    /**
     * The postLoad event occurs for an entity after the entity has been loaded into the current
     * EntityManager from the database or after the refresh operation has been applied to it.
     *
     * Note that, when using Doctrine\ORM\AbstractQuery#iterate(), postLoad events will be executed
     * immediately after objects are being hydrated, and therefore associations are not guaranteed
     * to be initialized. It is not safe to combine usage of Doctrine\ORM\AbstractQuery#iterate()
     * and postLoad event handlers.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }

        // prepare helper fields for uploaded files
        $objectType = $entity->get_objectType();
        $uploadFields = $this->getUploadFields($objectType);

        if (count($uploadFields) > 0) {
            $controllerHelper = ServiceUtil::get('rk_parkhaus_module.controller_helper');
            $request = ServiceUtil::get('request_stack')->getCurrentRequest();
            $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();
            $uploadHelper = ServiceUtil::get('rk_parkhaus_module.upload_helper');
            foreach ($uploadFields as $fieldName) {
                if (empty($entity[$fieldName])) {
                    continue;
                }
                $basePath = $controllerHelper->getFileBaseFolder($objectType, $fieldName);
                $filePath = $basePath . $entity[$fieldName];
                if (file_exists($filePath)) {
                    $fileName = $entity[$fieldName];
                    $entity[$fieldName] = new File($filePath);
                    $entity[$fieldName . 'Url'] = $baseUrl . '/' . $filePath;

                    // determine meta data if it does not exist
                    if (!is_array($entity[$fieldName . 'Meta']) || !count($entity[$fieldName . 'Meta'])) {
                        $entity[$fieldName . 'Meta'] = $uploadHelper->readMetaDataForFile($fileName, $filePath);
                    }
                } else {
                    $entity[$fieldName] = null;
                    $entity[$fieldName . 'Url'] = '';
                    $entity[$fieldName . 'Meta'] = [];
                }
            }
        }

        
        $serviceManager = ServiceUtil::getManager();
        $dispatcher = ServiceUtil::get('event_dispatcher');
        
        // create the filter event and dispatch it
        $filterEventClass = '\\RK\\ParkHausModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);
        $dispatcher->dispatch(constant('\\RK\\ParkHausModule\\ParkHausEvents::' . strtoupper($entity->get_objectType()) . '_POST_LOAD'), $event);
    }

    /**
     * Checks whether this listener is responsible for the given entity or not.
     *
     * @param EntityAccess $entity The given entity
     *
     * @return boolean True if entity is managed by this listener, false otherwise
     */
    protected function isEntityManagedByThisBundle($entity)
    {
        if (!($entity instanceof EntityAccess)) {
            return false;
        }

        $entityClassParts = explode('\\', get_class($entity));

        return ($entityClassParts[0] == 'RK' && $entityClassParts[1] == 'ParkHausModule');
    }

    /**
     * Returns list of upload fields for the given object type.
     *
     * @param string $objectType The object type
     *
     * @return array List of upload fields
     */
    protected function getUploadFields($objectType)
    {
        $uploadFields = [];
        switch ($objectType) {
            case 'vehicle':
                $uploadFields = ['titleImage', 'vehicleImage', 'manufacturerImage'];
                break;
            case 'vehicleImage':
                $uploadFields = ['vehicleImage'];
                break;
        }

        return $uploadFields;
    }
}
