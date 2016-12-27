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

use ServiceUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use UserUtil;
use Zikula\Core\Event\GenericEvent;
use Zikula\UsersModule\UserEvents;

/**
 * Event handler base class for user-related events.
 */
abstract class AbstractUserListener implements EventSubscriberInterface
{
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            'user.gettheme'            => ['getTheme', 5],
            UserEvents::CREATE_ACCOUNT => ['create', 5],
            UserEvents::UPDATE_ACCOUNT => ['update', 5],
            UserEvents::DELETE_ACCOUNT => ['delete', 5]
        ];
    }
    
    /**
     * Listener for the `user.gettheme` event.
     *
     * Called during UserUtil::getTheme() and is used to filter the results.
     * Receives arg['type'] with the type of result to be filtered
     * and the $themeName in the $event->data which can be modified.
     * Must $event->stopPropagation() if handler performs filter.
     *
     * @param GenericEvent $event The event instance
     */
    public function getTheme(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `user.account.create` event.
     *
     * Occurs after a user account is created. All handlers are notified.
     * It does not apply to creation of a pending registration.
     * The full user record created is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record that was created.
     *
     * @param GenericEvent $event The event instance
     */
    public function create(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `user.account.update` event.
     *
     * Occurs after a user is updated. All handlers are notified.
     * The full updated user record is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record, with the updated values.
     *
     * @param GenericEvent $event The event instance
     */
    public function update(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `user.account.delete` event.
     *
     * Occurs after the deletion of a user account. Subject is $uid.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     *
     * @param GenericEvent $event The event instance
     */
    public function delete(GenericEvent $event)
    {
        $uid = $event->getSubject();
    
        $serviceManager = ServiceUtil::getManager();
        $entityManager = $serviceManager->get('doctrine.orm.default_entity_manager');
        $translator = $serviceManager->get('translator.default');
        $logger = $serviceManager->get('logger');
        $currentUserApi = $serviceManager->get('zikula_users_module.current_user');
        
        $repo = $entityManager->getRepository('RK\ParkHausModule\Entity\VehicleEntity');
        // set creator to guest (1) for all vehicles created by this user
        $repo->updateCreator($uid, 1, $translator, $logger, $currentUserApi);
        
        // set last editor to guest (1) for all vehicles updated by this user
        $repo->updateLastEditor($uid, 1, $translator, $logger, $currentUserApi);
        // set last editor to guest (1) for all vehicles affected by this user
        $repo->updateUserField('owner', $uid, 1, $translator, $logger, $currentUserApi);
        
        $logArgs = ['app' => 'RKParkHausModule', 'user' => $serviceManager->get('zikula_users_module.current_user')->get('uname'), 'entities' => 'vehicles'];
        $logger->notice('{app}: User {user} has been deleted, so we deleted/updated corresponding {entities}, too.', $logArgs);
        
        $repo = $entityManager->getRepository('RK\ParkHausModule\Entity\VehicleImageEntity');
        // set creator to guest (1) for all vehicle images created by this user
        $repo->updateCreator($uid, 1, $translator, $logger, $currentUserApi);
        
        // set last editor to guest (1) for all vehicle images updated by this user
        $repo->updateLastEditor($uid, 1, $translator, $logger, $currentUserApi);
        
        $logArgs = ['app' => 'RKParkHausModule', 'user' => $serviceManager->get('zikula_users_module.current_user')->get('uname'), 'entities' => 'vehicle images'];
        $logger->notice('{app}: User {user} has been deleted, so we deleted/updated corresponding {entities}, too.', $logArgs);
    }
}
