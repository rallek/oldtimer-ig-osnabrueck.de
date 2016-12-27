<?php
/**
 * Sponsoring.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace RK\SponsoringModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Core\Event\GenericEvent;

/**
 * Event handler implementation class for group-related events.
 */
abstract class AbstractGroupListener implements EventSubscriberInterface
{
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            'group.create'     => ['create', 5],
            'group.update'     => ['update', 5],
            'group.delete'     => ['delete', 5],
            'group.adduser'    => ['addUser', 5],
            'group.removeuser' => ['removeUser', 5]
        ];
    }
    
    /**
     * Listener for the `group.create` event.
     *
     * Occurs after a group is created. All handlers are notified.
     * The full group record created is available as the subject.
     *
     * @param GenericEvent $event The event instance
     */
    public function create(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `group.update` event.
     *
     * Occurs after a group is updated. All handlers are notified.
     * The full updated group record is available as the subject.
     *
     * @param GenericEvent $event The event instance
     */
    public function update(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `group.delete` event.
     *
     * Occurs after a group is deleted from the system. All handlers are notified.
     * The full group record deleted is available as the subject.
     *
     * @param GenericEvent $event The event instance
     */
    public function delete(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `group.adduser` event.
     *
     * Occurs after a user is added to a group. All handlers are notified.
     * It does not apply to pending membership requests.
     * The uid and gid are available as the subject.
     *
     * @param GenericEvent $event The event instance
     */
    public function addUser(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `group.removeuser` event.
     *
     * Occurs after a user is removed from a group. All handlers are notified.
     * The uid and gid are available as the subject.
     *
     * @param GenericEvent $event The event instance
     */
    public function removeUser(GenericEvent $event)
    {
    }
}
