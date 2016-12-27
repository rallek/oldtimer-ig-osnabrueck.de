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

namespace RK\WebsiteHelperModule\Container\Base;

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
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..ui_hooks.linkers', 'ui_hooks', $this->__('rkwebsitehelpermodule. Linkers Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'rkwebsitehelpermodule.ui_hooks.linkers.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'rkwebsitehelpermodule.ui_hooks.linkers.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'rkwebsitehelpermodule.ui_hooks.linkers.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'rkwebsitehelpermodule.ui_hooks.linkers.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'rkwebsitehelpermodule.ui_hooks.linkers.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'rkwebsitehelpermodule.ui_hooks.linkers.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'rkwebsitehelpermodule.ui_hooks.linkers.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..filter_hooks.linkers', 'filter_hooks', $this->__('rkwebsitehelpermodule. Linkers Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'rkwebsitehelpermodule.filter_hooks.linkers.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..ui_hooks.carouselitems', 'ui_hooks', $this->__('rkwebsitehelpermodule. Carousel items Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'rkwebsitehelpermodule.ui_hooks.carouselitems.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'rkwebsitehelpermodule.ui_hooks.carouselitems.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'rkwebsitehelpermodule.ui_hooks.carouselitems.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'rkwebsitehelpermodule.ui_hooks.carouselitems.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'rkwebsitehelpermodule.ui_hooks.carouselitems.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'rkwebsitehelpermodule.ui_hooks.carouselitems.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'rkwebsitehelpermodule.ui_hooks.carouselitems.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..filter_hooks.carouselitems', 'filter_hooks', $this->__('rkwebsitehelpermodule. Carousel items Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'rkwebsitehelpermodule.filter_hooks.carouselitems.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..ui_hooks.carousells', 'ui_hooks', $this->__('rkwebsitehelpermodule. Carousells Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'rkwebsitehelpermodule.ui_hooks.carousells.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'rkwebsitehelpermodule.ui_hooks.carousells.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'rkwebsitehelpermodule.ui_hooks.carousells.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'rkwebsitehelpermodule.ui_hooks.carousells.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'rkwebsitehelpermodule.ui_hooks.carousells.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'rkwebsitehelpermodule.ui_hooks.carousells.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'rkwebsitehelpermodule.ui_hooks.carousells.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..filter_hooks.carousells', 'filter_hooks', $this->__('rkwebsitehelpermodule. Carousells Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'rkwebsitehelpermodule.filter_hooks.carousells.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..ui_hooks.websiteimages', 'ui_hooks', $this->__('rkwebsitehelpermodule. Website images Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'rkwebsitehelpermodule.ui_hooks.websiteimages.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'rkwebsitehelpermodule.ui_hooks.websiteimages.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'rkwebsitehelpermodule.ui_hooks.websiteimages.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'rkwebsitehelpermodule.ui_hooks.websiteimages.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'rkwebsitehelpermodule.ui_hooks.websiteimages.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'rkwebsitehelpermodule.ui_hooks.websiteimages.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'rkwebsitehelpermodule.ui_hooks.websiteimages.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('RKWebsiteHelperModule', 'subscriber.rkwebsitehelpermodule..filter_hooks.websiteimages', 'filter_hooks', $this->__('rkwebsitehelpermodule. Website images Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'rkwebsitehelpermodule.filter_hooks.websiteimages.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        
        
    }
}
