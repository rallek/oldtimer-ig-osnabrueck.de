<?php
/**
 * DownLoad.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\DownLoadModule\Base;

/**
 * Events definition base class.
 */
abstract class AbstractDownLoadEvents
{
    /**
     * The rkdownloadmodule.file_post_load event is thrown when files
     * are loaded from the database.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const FILE_POST_LOAD = 'rkdownloadmodule.file_post_load';
    
    /**
     * The rkdownloadmodule.file_pre_persist event is thrown before a new file
     * is created in the system.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const FILE_PRE_PERSIST = 'rkdownloadmodule.file_pre_persist';
    
    /**
     * The rkdownloadmodule.file_post_persist event is thrown after a new file
     * has been created in the system.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const FILE_POST_PERSIST = 'rkdownloadmodule.file_post_persist';
    
    /**
     * The rkdownloadmodule.file_pre_remove event is thrown before an existing file
     * is removed from the system.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const FILE_PRE_REMOVE = 'rkdownloadmodule.file_pre_remove';
    
    /**
     * The rkdownloadmodule.file_post_remove event is thrown after an existing file
     * has been removed from the system.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const FILE_POST_REMOVE = 'rkdownloadmodule.file_post_remove';
    
    /**
     * The rkdownloadmodule.file_pre_update event is thrown before an existing file
     * is updated in the system.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const FILE_PRE_UPDATE = 'rkdownloadmodule.file_pre_update';
    
    /**
     * The rkdownloadmodule.file_post_update event is thrown after an existing new file
     * has been updated in the system.
     *
     * The event listener receives an
     * RK\DownLoadModule\Event\FilterFileEvent instance.
     *
     * @see RK\DownLoadModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const FILE_POST_UPDATE = 'rkdownloadmodule.file_post_update';
    
}
