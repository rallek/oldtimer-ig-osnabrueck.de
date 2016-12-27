<?php
/**
 * Download.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace RK\DownloadModule\Container\Base;

use Zikula\Bundle\HookBundle\AbstractHookContainer as ZikulaHookContainer;
use Zikula\Bundle\HookBundle\Bundle\SubscriberBundle;

/**
 * Base class for hook container methods.
 */
abstract class AbstractHookContainer extends ZikulaHookContainer
{
    /**
     * Define the hook bundles supported by this module.
     *
     * @return void
     */
    protected function setupHookBundles()
    {
        $bundle = new SubscriberBundle('RKDownloadModule', 'subscriber.rkdownloadmodule..ui_hooks.files', 'ui_hooks', $this->__('rkdownloadmodule. Files Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'rkdownloadmodule.ui_hooks.files.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'rkdownloadmodule.ui_hooks.files.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'rkdownloadmodule.ui_hooks.files.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'rkdownloadmodule.ui_hooks.files.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'rkdownloadmodule.ui_hooks.files.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'rkdownloadmodule.ui_hooks.files.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'rkdownloadmodule.ui_hooks.files.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKDownloadModule', 'subscriber.rkdownloadmodule..filter_hooks.files', 'filter_hooks', $this->__('rkdownloadmodule. Files Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'rkdownloadmodule.filter_hooks.files.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        
        
    }
}
