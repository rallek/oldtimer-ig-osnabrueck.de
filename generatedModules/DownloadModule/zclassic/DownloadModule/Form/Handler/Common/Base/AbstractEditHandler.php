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

namespace RK\DownLoadModule\Form\Handler\Common\Base;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\RouteUrl;
use ModUtil;
use RuntimeException;
use UserUtil;
use RK\DownLoadModule\Helper\FeatureActivationHelper;

/**
 * This handler class handles the page events of editing forms.
 * It collects common functionality required by different object types.
 */
abstract class AbstractEditHandler
{
    use TranslatorTrait;

    /**
     * Name of treated object type.
     *
     * @var string
     */
    protected $objectType;

    /**
     * Name of treated object type starting with upper case.
     *
     * @var string
     */
    protected $objectTypeCapital;

    /**
     * Lower case version.
     *
     * @var string
     */
    protected $objectTypeLower;

    /**
     * Permission component based on object type.
     *
     * @var string
     */
    protected $permissionComponent;

    /**
     * Reference to treated entity instance.
     *
     * @var EntityAccess
     */
    protected $entityRef = null;

    /**
     * List of identifier names.
     *
     * @var array
     */
    protected $idFields = [];

    /**
     * List of identifiers of treated entity.
     *
     * @var array
     */
    protected $idValues = [];

    /**
     * Code defining the redirect goal after command handling.
     *
     * @var string
     */
    protected $returnTo = null;

    /**
     * Whether a create action is going to be repeated or not.
     *
     * @var boolean
     */
    protected $repeatCreateAction = false;

    /**
     * Url of current form with all parameters for multiple creations.
     *
     * @var string
     */
    protected $repeatReturnUrl = null;

    /**
     * Whether an existing item is used as template for a new one.
     *
     * @var boolean
     */
    protected $hasTemplateId = false;

    /**
     * Whether the PageLock extension is used for this entity type or not.
     *
     * @var boolean
     */
    protected $hasPageLockSupport = false;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * The current request.
     *
     * @var Request
     */
    protected $request = null;

    /**
     * The router.
     *
     * @var RouterInterface
     */
    protected $router = null;

    /**
     * The handled form type.
     *
     * @var AbstractType
     */
    protected $form = null;

    /**
     * Template parameters.
     *
     * @var array
     */
    protected $templateParameters = [];

    /**
     * Constructor.
     *
     * @param ContainerBuilder    $container    ContainerBuilder service instance
     * @param TranslatorInterface $translator   Translator service instance
     * @param RequestStack        $requestStack RequestStack service instance
     * @param RouterInterface     $router       Router service instance
     */
    public function __construct(ContainerBuilder $container, TranslatorInterface $translator, RequestStack $requestStack, RouterInterface $router)
    {
        $this->container = $container;
        $this->setTranslator($translator);
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
    }

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
     * Initialise form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @param array $templateParameters List of preassigned template variables
     *
     * @return boolean False in case of initialisation errors, otherwise true
     *
     * @throws NotFoundHttpException Thrown if item to be edited isn't found
     * @throws RuntimeException      Thrown if the workflow actions can not be determined
     */
    public function processForm(array $templateParameters)
    {
        $this->templateParameters = $templateParameters;
        $this->templateParameters['inlineUsage'] = UserUtil::getTheme() == 'ZikulaPrinterTheme' ? true : false;
    
    
        // initialise redirect goal
        $this->returnTo = $this->request->query->get('returnTo', null);
        if (null === $this->returnTo) {
            // default to referer
            if ($this->request->getSession()->has('referer')) {
                $this->returnTo = $this->request->getSession()->get('referer');
            } elseif ($this->request->headers->has('referer')) {
                $this->returnTo = $this->request->headers->get('referer');
                $this->request->getSession()->set('referer', $this->returnTo);
            } elseif ($this->request->server->has('HTTP_REFERER')) {
                $this->returnTo = $this->request->server->get('HTTP_REFERER');
                $this->request->getSession()->set('referer', $this->returnTo);
            }
        }
        // store current uri for repeated creations
        $this->repeatReturnUrl = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() . $this->request->getPathInfo();
    
        $this->permissionComponent = 'RKDownLoadModule:' . $this->objectTypeCapital . ':';
    
        $selectionHelper = $this->container->get('rk_download_module.selection_helper');
        $this->idFields = $selectionHelper->getIdFields($this->objectType);
    
        // retrieve identifier of the object we wish to view
        $controllerHelper = $this->container->get('rk_download_module.controller_helper');
    
        $this->idValues = $controllerHelper->retrieveIdentifier($this->request, [], $this->objectType, $this->idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($this->idValues);
    
        $entity = null;
        $this->templateParameters['mode'] = $hasIdentifier ? 'edit' : 'create';
    
        $permissionApi = $this->container->get('zikula_permissions_module.api.permission');
    
        if ($this->templateParameters['mode'] == 'edit') {
            if (!$permissionApi->hasPermission($this->permissionComponent, $this->createCompositeIdentifier() . '::', ACCESS_EDIT)) {
                throw new AccessDeniedException();
            }
    
            $entity = $this->initEntityForEditing();
            if (!is_object($entity)) {
                return false;
            }
    
            if (true === $this->hasPageLockSupport && \ModUtil::available('ZikulaPageLockModule')) {
                // try to guarantee that only one person at a time can be editing this entity
                $lockingApi = $this->container->get('zikula_pagelock_module.api.locking');
                $lockName = 'RKDownLoadModule' . $this->objectTypeCapital . $this->createCompositeIdentifier();
                $lockingApi->addLock($lockName, $this->getRedirectUrl(null));
            }
        } else {
            if (!$permissionApi->hasPermission($this->permissionComponent, '::', ACCESS_EDIT)) {
                throw new AccessDeniedException();
            }
    
            $entity = $this->initEntityForCreation();
        }
    
        // save entity reference for later reuse
        $this->entityRef = $entity;
    
    
        $workflowHelper = $this->container->get('rk_download_module.workflow_helper');
        $actions = $workflowHelper->getActionsForObject($entity);
        if (false === $actions || !is_array($actions)) {
            $this->request->getSession()->getFlashBag()->add('error', $this->__('Error! Could not determine workflow actions.'));
            $logger = $this->container->get('logger');
            $logArgs = ['app' => 'RKDownLoadModule', 'user' => $this->container->get('zikula_users_module.current_user')->get('uname'), 'entity' => $this->objectType, 'id' => $entity->createCompositeIdentifier()];
            $logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed to determine available workflow actions.', $logArgs);
            throw new \RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
    
        $this->templateParameters['actions'] = $actions;
    
        $this->form = $this->createForm();
        if (!is_object($this->form)) {
            return false;
        }
    
        // handle form request and check validity constraints of edited entity
        if ($this->form->handleRequest($this->request) && $this->form->isSubmitted()) {
            if ($this->form->isValid()) {
                $result = $this->handleCommand();
                if (false === $result) {
                    $this->templateParameters['form'] = $this->form->createView();
                }
    
                return $result;
            }
            if ($this->form->get('cancel')->isClicked()) {
                return new RedirectResponse($this->getRedirectUrl(['commandName' => 'cancel']), 302);
            }
        }
    
        $this->templateParameters['form'] = $this->form->createView();
    
        // everything okay, no initialisation errors occured
        return true;
    }
    
    /**
     * Creates the form type.
     */
    protected function createForm()
    {
        // to be customised in sub classes
        return null;
    }
    
    /**
     * Returns the template parameters.
     *
     * @return array
     */
    public function getTemplateParameters()
    {
        return $this->templateParameters;
    }
    
    /**
     * Create concatenated identifier string (for composite keys).
     *
     * @return String concatenated identifiers
     */
    protected function createCompositeIdentifier()
    {
        $itemId = '';
        foreach ($this->idFields as $idField) {
            if (!empty($itemId)) {
                $itemId .= '_';
            }
            $itemId .= $this->idValues[$idField];
        }
    
        return $itemId;
    }
    
    /**
     * Initialise existing entity for editing.
     *
     * @return EntityAccess|null Desired entity instance or null
     *
     * @throws NotFoundHttpException Thrown if item to be edited isn't found
     */
    protected function initEntityForEditing()
    {
        $selectionHelper = $this->container->get('rk_download_module.selection_helper');
        $entity = $selectionHelper->getEntity($this->objectType, $this->idValues);
        if (null === $entity) {
            throw new NotFoundHttpException($this->__('No such item.'));
        }
    
        $entity->initWorkflow();
    
        return $entity;
    }
    
    /**
     * Initialise new entity for creation.
     *
     * @return EntityAccess|null Desired entity instance or null
     *
     * @throws NotFoundHttpException Thrown if item to be cloned isn't found
     */
    protected function initEntityForCreation()
    {
        $this->hasTemplateId = false;
        $templateId = $this->request->query->get('astemplate', '');
        $entity = null;
    
        if (!empty($templateId)) {
            $templateIdValueParts = explode('_', $templateId);
            $this->hasTemplateId = count($templateIdValueParts) == count($this->idFields);
    
            if (true === $this->hasTemplateId) {
                $templateIdValues = [];
                $i = 0;
                foreach ($this->idFields as $idField) {
                    $templateIdValues[$idField] = $templateIdValueParts[$i];
                    $i++;
                }
                // reuse existing entity
                $selectionHelper = $this->container->get('rk_download_module.selection_helper');
                $entityT = $selectionHelper->getEntity($this->objectType, $templateIdValues);
                if (null === $entityT) {
                    throw new NotFoundHttpException($this->__('No such item.'));
                }
                $entity = clone $entityT;
            }
        }
    
        if (is_null($entity)) {
            $factory = $this->container->get('rk_download_module.' . $this->objectType . '_factory');
            $createMethod = 'create' . ucfirst($this->objectType);
            $entity = $factory->$createMethod();
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
        $codes = [];
    
        // index page of admin area
        $codes[] = 'admin';
        // admin list of entities
        $codes[] = 'adminView';
        // admin display page of treated entity
        $codes[] = 'adminDisplay';
        // index page of ajax area
        $codes[] = 'ajax';
        // index page of user area
        $codes[] = 'user';
        // user list of entities
        $codes[] = 'userView';
        // user display page of treated entity
        $codes[] = 'userDisplay';
    
        return $codes;
    }

    /**
     * Command event handler.
     *
     * @param array $args List of arguments
     *
     * @return mixed Redirect or false on errors
     */
    public function handleCommand($args = [])
    {
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ($this->form->get('cancel')->isClicked()) {
            $args['commandName'] = 'cancel';
        }
    
        $action = $args['commandName'];
        $isRegularAction = !in_array($action, ['delete', 'cancel']);
    
        if ($isRegularAction || $action == 'delete') {
            $this->fetchInputData($args);
        }
    
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        $hookHelper = null;
        if ($entity->supportsHookSubscribers() && $action != 'cancel') {
            $hookHelper = $this->container->get('rk_download_module.hook_helper');
            // Let any hooks perform additional validation actions
            $hookType = $action == 'delete' ? 'validate_delete' : 'validate_edit';
            $validationHooksPassed = $hookHelper->callValidationHooks($entity, $hookType);
            if (!$validationHooksPassed) {
                return false;
            }
        }
    
        if ($isRegularAction || $action == 'delete') {
            $success = $this->applyAction($args);
            if (!$success) {
                // the workflow operation failed
                return false;
            }
    
            if ($entity->supportsHookSubscribers()) {
                // Let any hooks know that we have created, updated or deleted an item
                $hookType = $action == 'delete' ? 'process_delete' : 'process_edit';
                $url = null;
                if ($action != 'delete') {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = $this->container->get('request_stack')->getMasterRequest()->getLocale();
                    $url = new RouteUrl('rkdownloadmodule_' . $this->objectType . '_display', $urlArgs);
                }
                if (!is_null($hookHelper)) {
                    $hookHelper->callProcessHooks($entity, $hookType, $url);
                }
            }
        }
    
        if (true === $this->hasPageLockSupport && $this->templateParameters['mode'] == 'edit' && \ModUtil::available('ZikulaPageLockModule')) {
            $lockingApi = $this->container->get('zikula_pagelock_module.api.locking');
            $lockName = 'RKDownLoadModule' . $this->objectTypeCapital . $this->createCompositeIdentifier();
            $lockingApi->releaseLock($lockName);
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param array   $args    arguments from handleCommand method
     * @param Boolean $success true if this is a success, false for default error
     *
     * @return String desired status or error message
     */
    protected function getDefaultMessage($args, $success = false)
    {
        $message = '';
        switch ($args['commandName']) {
            case 'create':
                if (true === $success) {
                    $message = $this->__('Done! Item created.');
                } else {
                    $message = $this->__('Error! Creation attempt failed.');
                }
                break;
            case 'update':
                if (true === $success) {
                    $message = $this->__('Done! Item updated.');
                } else {
                    $message = $this->__('Error! Update attempt failed.');
                }
                break;
            case 'delete':
                if (true === $success) {
                    $message = $this->__('Done! Item deleted.');
                } else {
                    $message = $this->__('Error! Deletion attempt failed.');
                }
                break;
        }
    
        return $message;
    }
    
    /**
     * Add success or error message to session.
     *
     * @param array   $args    arguments from handleCommand method
     * @param Boolean $success true if this is a success, false for default error
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    protected function addDefaultMessage($args, $success = false)
    {
        $message = $this->getDefaultMessage($args, $success);
        if (empty($message)) {
            return;
        }
    
        $flashType = true === $success ? 'status' : 'error';
        $this->request->getSession()->getFlashBag()->add($flashType, $message);
        $logger = $this->container->get('logger');
        $logArgs = ['app' => 'RKDownLoadModule', 'user' => $this->container->get('zikula_users_module.current_user')->get('uname'), 'entity' => $this->objectType, 'id' => $this->entityRef->createCompositeIdentifier()];
        if (true === $success) {
            $logger->notice('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        } else {
            $logger->error('{app}: User {user} tried to update the {entity} with id {id}, but failed.', $logArgs);
        }
    }

    /**
     * Input data processing called by handleCommand method.
     *
     * @param array $args Additional arguments
     */
    public function fetchInputData($args)
    {
        // fetch posted data input values as an associative array
        $formData = $this->form->getData();
    
        if ($args['commandName'] != 'cancel') {
        }
    
        if ($this->templateParameters['mode'] == 'create' && isset($this->form['repeatCreation']) && $this->form['repeatCreation']->getData() == 1) {
            $this->repeatCreateAction = true;
        }
    
        if (isset($this->form['additionalNotificationRemarks']) && $this->form['additionalNotificationRemarks']->getData() != '') {
            $this->request->getSession()->set('RKDownLoadModuleAdditionalNotificationRemarks', $this->form['additionalNotificationRemarks']->getData());
        }
    
        // return remaining form data
        return $formData;
    }

    /**
     * This method executes a certain workflow action.
     *
     * @param array $args Arguments from handleCommand method
     *
     * @return bool Whether everything worked well or not
     */
    public function applyAction(array $args = [])
    {
        // stub for subclasses
        return false;
    }
}
