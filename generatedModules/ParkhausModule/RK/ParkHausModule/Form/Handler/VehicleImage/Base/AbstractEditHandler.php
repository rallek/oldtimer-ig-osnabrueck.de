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

namespace RK\ParkHausModule\Form\Handler\VehicleImage\Base;

use RK\ParkHausModule\Form\Handler\Common\EditHandler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the vehicle image object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    /**
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables
     *
     * @return boolean False in case of initialisation errors, otherwise true
     */
    public function processForm(array $templateParameters)
    {
        $this->objectType = 'vehicleImage';
        $this->objectTypeCapital = 'VehicleImage';
        $this->objectTypeLower = 'vehicleimage';
        
        $this->hasPageLockSupport = true;
    
        $result = parent::processForm($templateParameters);
    
        if ($this->templateParameters['mode'] == 'create') {
            $modelHelper = $this->container->get('rk_parkhaus_module.model_helper');
            if (!$modelHelper->canBeCreated($this->objectType)) {
                $this->request->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the vehicle image yet as other items are required which must be created before!'));
                $logger = $this->container->get('logger');
                $logArgs = ['app' => 'RKParkHausModule', 'user' => $this->container->get('zikula_users_module.current_user')->get('uname'), 'entity' => $this->objectType];
                $logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (makes translatable support easier)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
    
        return $result;
    }
    
    /**
     * Initialises relationship presets.
     */
    protected function initRelationPresets()
    {
        $entity = $this->entityRef;
    
        $selectionHelper = $this->container->get('rk_parkhaus_module.selection_helper');
        
        // assign identifiers of predefined incoming relationships
        // editable relation, we store the id and assign it now to show it in UI
        $this->relationPresets['vehicle'] = $this->request->get('vehicle', '');
        if (!empty($this->relationPresets['vehicle'])) {
            $relObj = $selectionHelper->getEntity('vehicle', $this->relationPresets['vehicle']);
            if (null !== $relObj) {
                $relObj->addVehicleImages($entity);
            }
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        $options = [
            'entity' => $this->entityRef,
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'inlineUsage' => $this->templateParameters['inlineUsage']
        ];
    
        return $this->container->get('form.factory')->create('RK\ParkHausModule\Form\Type\VehicleImageType', $this->entityRef, $options);
    }


    /**
     * Initialise existing entity for editing.
     *
     * @return EntityAccess desired entity instance or null
     */
    protected function initEntityForEditing()
    {
        $entity = parent::initEntityForEditing();
    
        // only allow editing for the owner or people with higher permissions
        $uid = $this->container->get('zikula_users_module.current_user')->get('uid');
        if (!method_exists($entity, 'getCreatedBy') || $entity->getCreatedBy()->getUid() != $uid) {
            $permissionApi = $this->container->get('zikula_permissions_module.api.permission');
            if (!$permissionApi->hasPermission($this->permissionComponent, $this->createCompositeIdentifier() . '::', ACCESS_ADD)) {
                throw new AccessDeniedException();
            }
        }
    
        return $entity;
    }

    /**
     * Get list of allowed redirect codes.
     *
     * @return array list of possible redirect codes
     */
    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
        // user index page of vehicle image area
        $codes[] = 'userIndex';
        // admin index page of vehicle image area
        $codes[] = 'adminIndex';
        // user list of vehicle images
        $codes[] = 'userView';
        // admin list of vehicle images
        $codes[] = 'adminView';
        // user detail page of treated vehicle image
        $codes[] = 'userDisplay';
        // admin detail page of treated vehicle image
        $codes[] = 'adminDisplay';
    
        // user list of vehicles
        $codes[] = 'userViewVehicle';
        // admin list of vehicles
        $codes[] = 'adminViewVehicle';
        // user detail page of related vehicle
        $codes[] = 'userDisplayVehicle';
        // admin detail page of related vehicle
        $codes[] = 'adminDisplayVehicle';
    
        return $codes;
    }

    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     *
     * @param array $args List of arguments
     *
     * @return string The default redirect url
     */
    protected function getDefaultReturnUrl($args)
    {
        $objectIsPersisted = $args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel');
    
        if (null !== $this->returnTo) {
            
            $isDisplayOrEditPage = substr($this->returnTo, -7) == 'display' || substr($this->returnTo, -4) == 'edit';
            if (!$isDisplayOrEditPage || $objectIsPersisted) {
                // return to referer
                return $this->returnTo;
            }
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
        $routePrefix = 'rkparkhausmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of vehicleImages
        $viewArgs = [];
        $url = $this->router->generate($routePrefix . 'view', $viewArgs);
    
        return $url;
    }

    /**
     * Command event handler.
     *
     * This event handler is called when a command is issued by the user.
     *
     * @param array $args List of arguments
     *
     * @return mixed Redirect or false on errors
     */
    public function handleCommand($args = [])
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ($this->form->get('cancel')->isClicked()) {
            $args['commandName'] = 'cancel';
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    Arguments from handleCommand method
     * @param Boolean $success Becomes true if this is a success, false for default error
     *
     * @return String desired status or error message
     */
    protected function getDefaultMessage($args, $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        $message = '';
        switch ($args['commandName']) {
            case 'defer':
            case 'submit':
                if ($this->templateParameters['mode'] == 'create') {
                    $message = $this->__('Done! Vehicle image created.');
                } else {
                    $message = $this->__('Done! Vehicle image updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Vehicle image deleted.');
                break;
            default:
                $message = $this->__('Done! Vehicle image updated.');
                break;
        }
    
        return $message;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args Arguments from handleCommand method
     *
     * @return bool Whether everything worked well or not
     *
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
    
        $success = false;
        $flashBag = $this->request->getSession()->getFlashBag();
        $logger = $this->container->get('logger');
        try {
            // execute the workflow action
            $workflowHelper = $this->container->get('rk_parkhaus_module.workflow_helper');
            $success = $workflowHelper->executeAction($entity, $action);
        } catch(\Exception $e) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $e->getMessage());
            $logArgs = ['app' => 'RKParkHausModule', 'user' => $this->container->get('zikula_users_module.current_user')->get('uname'), 'entity' => 'vehicle image', 'id' => $entity->createCompositeIdentifier(), 'errorMessage' => $e->getMessage()];
            $logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && $this->templateParameters['mode'] == 'create') {
            // store new identifier
            foreach ($this->idFields as $idField) {
                $this->idValues[$idField] = $entity[$idField];
            }
        }
    
        return $success;
    }

    /**
     * Get url to redirect to.
     *
     * @param array $args List of arguments
     *
     * @return string The redirect url
     */
    protected function getRedirectUrl($args)
    {
        if (true === $this->templateParameters['inlineUsage']) {
            $urlArgs = [
                'idPrefix' => $this->idPrefix,
                'commandName' => $args['commandName']
            ];
            foreach ($this->idFields as $idField) {
                $urlArgs[$idField] = $this->idValues[$idField];
            }
    
            // inline usage, return to special function for closing the modal window instance
            return $this->router->generate('rkparkhausmodule_' . $this->objectTypeLower . '_handleinlineredirect', $urlArgs);
        }
    
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        if ($this->request->getSession()->has('rkparkhausmoduleReferer')) {
            $this->request->getSession()->del('rkparkhausmoduleReferer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = substr($this->returnTo, 0, 5) == 'admin' ? 'admin' : '';
        $routePrefix = 'rkparkhausmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userIndex':
            case 'adminIndex':
                return $this->router->generate($routePrefix . 'index');
            case 'userView':
            case 'adminView':
                return $this->router->generate($routePrefix . 'view');
            case 'userDisplay':
            case 'adminDisplay':
                if ($args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel')) {
                    foreach ($this->idFields as $idField) {
                        $urlArgs[$idField] = $this->idValues[$idField];
                    }
    
                    return $this->router->generate($routePrefix . 'display', $urlArgs);
                }
    
                return $this->getDefaultReturnUrl($args);
            case 'userViewVehicle':
            case 'adminViewVehicle':
                return $this->router->generate('rkparkhausmodule_vehicle_' . $routeArea . 'view');
            case 'userDisplayVehicle':
            case 'adminDisplayVehicle':
                if (!empty($this->relationPresets['vehicle'])) {
                    return $this->router->generate('rkparkhausmodule_vehicle_' . $routeArea . 'display',  ['id' => $this->relationPresets['vehicle']]);
                }
    
                return $this->getDefaultReturnUrl($args);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
