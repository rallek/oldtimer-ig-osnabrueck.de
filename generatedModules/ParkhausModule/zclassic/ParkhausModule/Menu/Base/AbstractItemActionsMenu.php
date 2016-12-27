<?php
/**
 * Parkhaus.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\ParkhausModule\Menu\Base;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Zikula\Common\Translator\TranslatorTrait;
use RK\ParkhausModule\Entity\VehicleEntity;
use RK\ParkhausModule\Entity\VehicleImageEntity;

/**
 * This is the item actions menu implementation class.
 */
class AbstractItemActionsMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use TranslatorTrait;

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds the menu.
     *
     * @param FactoryInterface $factory Menu factory
     * @param array            $options Additional options
     *
     * @return \Knp\Menu\MenuItem The assembled menu
     */
    public function menu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('itemActions');
        if (!isset($options['entity']) || !isset($options['area']) || !isset($options['context'])) {
            return $menu;
        }

        $this->setTranslator($this->container->get('translator'));

        $entity = $options['entity'];
        $area = $options['area'];
        $context = $options['context'];

        $permissionApi = $this->container->get('zikula_permissions_module.api.permission');
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $menu->setChildrenAttribute('class', 'list-inline');

        
        $currentLegacyControllerType = $area != '' ? $area : 'user';
        $currentFunc = $context;
        
        if ($entity instanceof VehicleEntity) {
            $component = 'RKParkhausModule:Vehicle:';
            $instance = $entity['id'] . '::';
        
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, ['index', 'view'])) {
                $menu->addChild($this->__('Preview'), [
                    'route' => 'rkparkhausmodule_vehicle_display',
                    'routeParameters' => ['id' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-search-plus');
                $menu[$this->__('Preview')]->setLinkAttribute('target', '_blank');
                $menu[$this->__('Preview')]->setLinkAttribute('title', $this->__('Open preview page'));
                $menu->addChild($this->__('Details'), [
                    'route' => 'rkparkhausmodule_vehicle_admindisplay',
                    'routeParameters' => ['id' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-eye');
                $menu[$this->__('Details')]->setLinkAttribute('title', str_replace('"', '', $entity->getTitleFromDisplayPattern()));
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $uid = $currentUserApi->get('uid');
                    // only allow editing for the owner or people with higher permissions
                    if ($entity->getCreatedUserId() == $uid || $permissionApi->hasPermission($component, $instance, ACCESS_ADD)) {
                        $menu->addChild($this->__('Edit'), [
                            'route' => 'rkparkhausmodule_vehicle_adminedit',
                            'routeParameters' => ['id' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-pencil-square-o');
                        $menu[$this->__('Edit')]->setLinkAttribute('title', $this->__('Edit this vehicle'));
                        $menu->addChild($this->__('Reuse'), [
                            'route' => 'rkparkhausmodule_vehicle_adminedit',
                            'routeParameters' => ['astemplate' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-files-o');
                        $menu[$this->__('Reuse')]->setLinkAttribute('title', $this->__('Reuse for new vehicle'));
                    }
                }
            }
            if ($currentFunc == 'display') {
                $title = $this->__('Back to overview');
                $menu->addChild($title, [
                    'route' => 'rkparkhausmodule_vehicle_adminview'
                ])->setAttribute('icon', 'fa fa-reply');
                $menu[$title]->setLinkAttribute('title', $title);
            }
            
            // more actions for adding new related items
            $authAdmin = $permissionApi->hasPermission($component, $instance, ACCESS_ADMIN);
            
            $uid = $currentUserApi->get('uid');
            if ($authAdmin || (isset($uid) && $entity->getCreatedUserId() != '' && $entity->getCreatedUserId() == $uid)) {
            
                $title = $this->__('Create vehicle image');
                $menu->addChild($title, [
                    'route' => 'rkparkhausmodule_vehicleimage_adminedit',
                    'routeParameters' => ['vehicle' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-plus');
                $menu[$title]->setLinkAttribute('title', $title);
            }
        }
        if ($currentLegacyControllerType == 'user') {
            if (in_array($currentFunc, ['index', 'view'])) {
                $menu->addChild($this->__('Details'), [
                    'route' => 'rkparkhausmodule_vehicle_display',
                    'routeParameters' => ['id' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-eye');
                $menu[$this->__('Details')]->setLinkAttribute('title', str_replace('"', '', $entity->getTitleFromDisplayPattern()));
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $uid = $currentUserApi->get('uid');
                    // only allow editing for the owner or people with higher permissions
                    if ($entity->getCreatedUserId() == $uid || $permissionApi->hasPermission($component, $instance, ACCESS_ADD)) {
                        $menu->addChild($this->__('Edit'), [
                            'route' => 'rkparkhausmodule_vehicle_edit',
                            'routeParameters' => ['id' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-pencil-square-o');
                        $menu[$this->__('Edit')]->setLinkAttribute('title', $this->__('Edit this vehicle'));
                        $menu->addChild($this->__('Reuse'), [
                            'route' => 'rkparkhausmodule_vehicle_edit',
                            'routeParameters' => ['astemplate' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-files-o');
                        $menu[$this->__('Reuse')]->setLinkAttribute('title', $this->__('Reuse for new vehicle'));
                    }
                }
            }
            if ($currentFunc == 'display') {
                $title = $this->__('Back to overview');
                $menu->addChild($title, [
                    'route' => 'rkparkhausmodule_vehicle_view'
                ])->setAttribute('icon', 'fa fa-reply');
                $menu[$title]->setLinkAttribute('title', $title);
            }
            
            // more actions for adding new related items
            $authAdmin = $permissionApi->hasPermission($component, $instance, ACCESS_ADMIN);
            
            $uid = $currentUserApi->get('uid');
            if ($authAdmin || (isset($uid) && $entity->getCreatedUserId() != '' && $entity->getCreatedUserId() == $uid)) {
            
                $title = $this->__('Create vehicle image');
                $menu->addChild($title, [
                    'route' => 'rkparkhausmodule_vehicleimage_edit',
                    'routeParameters' => ['vehicle' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-plus');
                $menu[$title]->setLinkAttribute('title', $title);
            }
        }
        }
        if ($entity instanceof VehicleImageEntity) {
            $component = 'RKParkhausModule:VehicleImage:';
            $instance = $entity['id'] . '::';
        
        if ($currentLegacyControllerType == 'admin') {
            if (in_array($currentFunc, ['index', 'view'])) {
                $menu->addChild($this->__('Preview'), [
                    'route' => 'rkparkhausmodule_vehicleimage_display',
                    'routeParameters' => ['id' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-search-plus');
                $menu[$this->__('Preview')]->setLinkAttribute('target', '_blank');
                $menu[$this->__('Preview')]->setLinkAttribute('title', $this->__('Open preview page'));
                $menu->addChild($this->__('Details'), [
                    'route' => 'rkparkhausmodule_vehicleimage_admindisplay',
                    'routeParameters' => ['id' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-eye');
                $menu[$this->__('Details')]->setLinkAttribute('title', str_replace('"', '', $entity->getTitleFromDisplayPattern()));
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $uid = $currentUserApi->get('uid');
                    // only allow editing for the owner or people with higher permissions
                    if ($entity->getCreatedUserId() == $uid || $permissionApi->hasPermission($component, $instance, ACCESS_ADD)) {
                        $menu->addChild($this->__('Edit'), [
                            'route' => 'rkparkhausmodule_vehicleimage_adminedit',
                            'routeParameters' => ['id' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-pencil-square-o');
                        $menu[$this->__('Edit')]->setLinkAttribute('title', $this->__('Edit this vehicle image'));
                        $menu->addChild($this->__('Reuse'), [
                            'route' => 'rkparkhausmodule_vehicleimage_adminedit',
                            'routeParameters' => ['astemplate' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-files-o');
                        $menu[$this->__('Reuse')]->setLinkAttribute('title', $this->__('Reuse for new vehicle image'));
                    }
                }
                if ($permissionApi->hasPermission($component, $instance, ACCESS_DELETE)) {
                    $menu->addChild($this->__('Delete'), [
                        'route' => 'rkparkhausmodule_vehicleimage_admindelete',
                        'routeParameters' => ['id' => $entity['id']]
                    ])->setAttribute('icon', 'fa fa-trash-o');
                    $menu[$this->__('Delete')]->setLinkAttribute('title', $this->__('Delete this vehicle image'));
                }
            }
            if ($currentFunc == 'display') {
                $title = $this->__('Back to overview');
                $menu->addChild($title, [
                    'route' => 'rkparkhausmodule_vehicleimage_adminview'
                ])->setAttribute('icon', 'fa fa-reply');
                $menu[$title]->setLinkAttribute('title', $title);
            }
        }
        if ($currentLegacyControllerType == 'user') {
            if (in_array($currentFunc, ['index', 'view'])) {
                $menu->addChild($this->__('Details'), [
                    'route' => 'rkparkhausmodule_vehicleimage_display',
                    'routeParameters' => ['id' => $entity['id']]
                ])->setAttribute('icon', 'fa fa-eye');
                $menu[$this->__('Details')]->setLinkAttribute('title', str_replace('"', '', $entity->getTitleFromDisplayPattern()));
            }
            if (in_array($currentFunc, ['index', 'view', 'display'])) {
                if ($permissionApi->hasPermission($component, $instance, ACCESS_EDIT)) {
                    $uid = $currentUserApi->get('uid');
                    // only allow editing for the owner or people with higher permissions
                    if ($entity->getCreatedUserId() == $uid || $permissionApi->hasPermission($component, $instance, ACCESS_ADD)) {
                        $menu->addChild($this->__('Edit'), [
                            'route' => 'rkparkhausmodule_vehicleimage_edit',
                            'routeParameters' => ['id' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-pencil-square-o');
                        $menu[$this->__('Edit')]->setLinkAttribute('title', $this->__('Edit this vehicle image'));
                        $menu->addChild($this->__('Reuse'), [
                            'route' => 'rkparkhausmodule_vehicleimage_edit',
                            'routeParameters' => ['astemplate' => $entity['id']]
                        ])->setAttribute('icon', 'fa fa-files-o');
                        $menu[$this->__('Reuse')]->setLinkAttribute('title', $this->__('Reuse for new vehicle image'));
                    }
                }
                if ($permissionApi->hasPermission($component, $instance, ACCESS_DELETE)) {
                    $menu->addChild($this->__('Delete'), [
                        'route' => 'rkparkhausmodule_vehicleimage_delete',
                        'routeParameters' => ['id' => $entity['id']]
                    ])->setAttribute('icon', 'fa fa-trash-o');
                    $menu[$this->__('Delete')]->setLinkAttribute('title', $this->__('Delete this vehicle image'));
                }
            }
            if ($currentFunc == 'display') {
                $title = $this->__('Back to overview');
                $menu->addChild($title, [
                    'route' => 'rkparkhausmodule_vehicleimage_view'
                ])->setAttribute('icon', 'fa fa-reply');
                $menu[$title]->setLinkAttribute('title', $title);
            }
        }
        }

        return $menu;
    }
}
