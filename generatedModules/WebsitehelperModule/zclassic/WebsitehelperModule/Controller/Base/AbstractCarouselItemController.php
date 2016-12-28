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

namespace RK\WebsiteHelperModule\Controller\Base;

use RK\WebsiteHelperModule\Entity\CarouselItemEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ModUtil;
use RuntimeException;
use System;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;

/**
 * Carousel item controller base class.
 */
abstract class AbstractCarouselItemController extends AbstractController
{
    /**
     * This is the default action handling the index admin area called without defining arguments.
     * @Cache(expires="+7 days", public=true)
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminIndexAction(Request $request)
    {
        return $this->indexInternal($request, true);
    }
    
    /**
     * This is the default action handling the index area called without defining arguments.
     * @Cache(expires="+7 days", public=true)
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        return $this->indexInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminIndex() and index().
     */
    protected function indexInternal(Request $request, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'carouselItem';
        $utilArgs = ['controller' => 'carouselItem', 'action' => 'index'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        
        if ($isAdmin) {
            
            return $this->redirectToRoute('rkwebsitehelpermodule_carouselitem_' . ($isAdmin ? 'admin' : '') . 'view');
        }
        
        if (!$isAdmin) {
            
            return $this->redirectToRoute('rkwebsitehelpermodule_carouselitem_' . ($isAdmin ? 'admin' : '') . 'view');
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // return index template
        return $this->render('@RKWebsiteHelperModule/CarouselItem/index.html.twig', $templateParameters);
    }
    /**
     * This action provides a handling of edit requests in the admin area.
     * @Cache(lastModified="carouselItem.getUpdatedDate()", ETag="'CarouselItem' ~ carouselItem.getid() ~ carouselItem.getUpdatedDate().format('U')")
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminEditAction(Request $request)
    {
        return $this->editInternal($request, true);
    }
    
    /**
     * This action provides a handling of edit requests.
     * @Cache(lastModified="carouselItem.getUpdatedDate()", ETag="'CarouselItem' ~ carouselItem.getid() ~ carouselItem.getUpdatedDate().format('U')")
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if item to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function editAction(Request $request)
    {
        return $this->editInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminEdit() and edit().
     */
    protected function editInternal(Request $request, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'carouselItem';
        $utilArgs = ['controller' => 'carouselItem', 'action' => 'edit'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $repository = $this->get('rk_websitehelper_module.' . $objectType . '_factory')->getRepository();
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $imageHelper = $this->get('rk_websitehelper_module.image_helper');
        $templateParameters = array_merge($templateParameters, $repository->getAdditionalTemplateParameters($imageHelper, 'controllerAction', $utilArgs));
        
        // delegate form processing to the form handler
        $formHandler = $this->get('rk_websitehelper_module.form.handler.carouselitem');
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $viewHelper = $this->get('rk_websitehelper_module.view_helper');
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'edit', $request, $templateParameters);
    }
    /**
     * This action provides an item list overview in the admin area.
     * @Cache(expires="+2 hours", public=false)
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field
     * @param string  $sortdir      Sorting direction
     * @param int     $pos          Current pager position
     * @param int     $num          Amount of entries to display
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * This action provides an item list overview.
     * @Cache(expires="+2 hours", public=false)
     *
     * @param Request  $request      Current request instance
     * @param string  $sort         Sorting field
     * @param string  $sortdir      Sorting direction
     * @param int     $pos          Current pager position
     * @param int     $num          Amount of entries to display
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, false);
    }
    
    /**
     * This method includes the common implementation code for adminView() and view().
     */
    protected function viewInternal(Request $request, $sort, $sortdir, $pos, $num, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'carouselItem';
        $utilArgs = ['controller' => 'carouselItem', 'action' => 'view'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $repository = $this->get('rk_websitehelper_module.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);
        $viewHelper = $this->get('rk_websitehelper_module.view_helper');
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $imageHelper = $this->get('rk_websitehelper_module.image_helper');
        $selectionHelper = $this->get('rk_websitehelper_module.selection_helper');
        
        // convenience vars to make code clearer
        $currentUrlArgs = [];
        $where = '';
        
        $showOwnEntries = $request->query->getInt('own', $this->getVar('showOnlyOwnEntries', 0));
        $showAllEntries = $request->query->getInt('all', 0);
        
        $templateParameters['own'] = $showAllEntries;
        $templateParameters['all'] = $showOwnEntries;
        if ($showAllEntries == 1) {
            $currentUrlArgs['all'] = 1;
        }
        if ($showOwnEntries == 1) {
            $currentUrlArgs['own'] = 1;
        }
        
        $additionalParameters = $repository->getAdditionalTemplateParameters($imageHelper, 'controllerAction', $utilArgs);
        
        $resultsPerPage = 0;
        if ($showAllEntries != 1) {
            // the number of items displayed on a page for pagination
            $resultsPerPage = $num;
            if ($resultsPerPage == 0) {
                $resultsPerPage = $this->getVar($objectType . 'EntriesPerPage', 10);
            }
        }
        
        // parameter for used sorting field
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
            System::queryStringSetVar('sort', $sort);
            $request->query->set('sort', $sort);
            // set default sorting in route parameters (e.g. for the pager)
            $routeParams = $request->attributes->get('_route_params');
            $routeParams['sort'] = $sort;
            $request->attributes->set('_route_params', $routeParams);
        }
        
        // parameter for used sort order
        $sortdir = strtolower($sortdir);
        
        $sortableColumns = new SortableColumns($this->get('router'), 'rkwebsitehelpermodule_carouselitem_' . ($isAdmin ? 'admin' : '') . 'view', 'sort', 'sortdir');
        $sortableColumns->addColumns([
            new Column('itemName'),
            new Column('title'),
            new Column('subtitle'),
            new Column('link'),
            new Column('itemImage'),
            new Column('titleColor'),
            new Column('itemStartDate'),
            new Column('intemEndDate'),
            new Column('singleItemIdentifier'),
            new Column('carousel'),
            new Column('createdUserId'),
            new Column('createdDate'),
            new Column('updatedUserId'),
            new Column('updatedDate'),
        ]);
        
        $additionalUrlParameters = [
            'all' => $showAllEntries,
            'own' => $showOwnEntries,
            'num' => $resultsPerPage
        ];
        foreach ($additionalParameters as $parameterName => $parameterValue) {
            if (false !== stripos($parameterName, 'thumbRuntimeOptions')) {
                continue;
            }
            $additionalUrlParameters[$parameterName] = $parameterValue;
        }
        
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['num'] = $resultsPerPage;
        
        $tpl = '';
        if ($request->isMethod('POST')) {
            $tpl = $request->request->getAlnum('tpl', '');
        } elseif ($request->isMethod('GET')) {
            $tpl = $request->query->getAlnum('tpl', '');
        }
        $templateParameters['tpl'] = $tpl;
        
        $quickNavForm = $this->createForm('RK\WebsiteHelperModule\Form\Type\QuickNavigation\\' . ucfirst($objectType) . 'QuickNavType', $templateParameters);
        if ($quickNavForm->handleRequest($request) && $quickNavForm->isSubmitted()) {
            $quickNavData = $quickNavForm->getData();
            foreach ($quickNavData as $fieldName => $fieldValue) {
                if ($fieldName == 'routeArea') {
                    continue;
                }
                if ($fieldName == 'all') {
                    $showAllEntries = $additionalUrlParameters['all'] = $templateParameters['all'] = $fieldValue;
                } elseif ($fieldName == 'own') {
                    $showOwnEntries = $additionalUrlParameters['own'] = $templateParameters['own'] = $fieldValue;
                } elseif ($fieldName == 'num') {
                    $resultsPerPage = $additionalUrlParameters['num'] = $fieldValue;
                } else {
                    // set filter as query argument, fetched inside repository
                    $request->query->set($fieldName, $fieldValue);
                }
            }
        }
        $sortableColumns->setOrderBy($sortableColumns->getColumn($sort), strtoupper($sortdir));
        $sortableColumns->setAdditionalUrlParameters($additionalUrlParameters);
        
        if ($showAllEntries == 1) {
            // retrieve item list without pagination
            $entities = $selectionHelper->getEntities($objectType, [], $where, $sort . ' ' . $sortdir);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = $pos;
        
            // retrieve item list with pagination
            list($entities, $objectCount) = $selectionHelper->getEntitiesPaginated($objectType, $where, $sort . ' ' . $sortdir, $currentPage, $resultsPerPage);
        
            $templateParameters['currentPage'] = $currentPage;
            $templateParameters['pager'] = ['numitems' => $objectCount, 'itemsperpage' => $resultsPerPage];
        }
        
        foreach ($entities as $k => $entity) {
            $entity->initWorkflow();
        }
        
        // build RouteUrl instance for display hooks
        $currentUrlArgs['_locale'] = $request->getLocale();
        $currentUrlObject = new RouteUrl('rkwebsitehelpermodule_carouselItem_' . /*($isAdmin ? 'admin' : '') . */'view', $currentUrlArgs);
        
        $templateParameters['items'] = $entities;
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['num'] = $resultsPerPage;
        $templateParameters['currentUrlObject'] = $currentUrlObject;
        $templateParameters = array_merge($templateParameters, $additionalParameters);
        
        $templateParameters['sort'] = $sortableColumns->generateSortableColumns();
        $templateParameters['quickNavForm'] = $quickNavForm->createView();
        
        $templateParameters['showAllEntries'] = $templateParameters['all'];
        $templateParameters['showOwnEntries'] = $templateParameters['own'];
        
        $modelHelper = $this->get('rk_websitehelper_module.model_helper');
        $templateParameters['canBeCreated'] = $modelHelper->canBeCreated($objectType);
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'view', $request, $templateParameters);
    }
    /**
     * This action provides a handling of simple delete requests in the admin area.
     * @ParamConverter("carouselItem", class="RKWebsiteHelperModule:CarouselItemEntity", options={"id" = "id", "repository_method" = "selectById"})
     * @Cache(lastModified="carouselItem.getUpdatedDate()", ETag="'CarouselItem' ~ carouselItem.getid() ~ carouselItem.getUpdatedDate().format('U')")
     *
     * @param Request  $request      Current request instance
     * @param CarouselItemEntity $carouselItem      Treated carousel item instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if item to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminDeleteAction(Request $request, CarouselItemEntity $carouselItem)
    {
        return $this->deleteInternal($request, $carouselItem, true);
    }
    
    /**
     * This action provides a handling of simple delete requests.
     * @ParamConverter("carouselItem", class="RKWebsiteHelperModule:CarouselItemEntity", options={"id" = "id", "repository_method" = "selectById"})
     * @Cache(lastModified="carouselItem.getUpdatedDate()", ETag="'CarouselItem' ~ carouselItem.getid() ~ carouselItem.getUpdatedDate().format('U')")
     *
     * @param Request  $request      Current request instance
     * @param CarouselItemEntity $carouselItem      Treated carousel item instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if item to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function deleteAction(Request $request, CarouselItemEntity $carouselItem)
    {
        return $this->deleteInternal($request, $carouselItem, false);
    }
    
    /**
     * This method includes the common implementation code for adminDelete() and delete().
     */
    protected function deleteInternal(Request $request, CarouselItemEntity $carouselItem, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'carouselItem';
        $utilArgs = ['controller' => 'carouselItem', 'action' => 'delete'];
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_DELETE;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $entity = $carouselItem;
        
        $logger = $this->get('logger');
        $logArgs = ['app' => 'RKWebsiteHelperModule', 'user' => $this->get('zikula_users_module.current_user')->get('uname'), 'entity' => 'carousel item', 'id' => $entity->createCompositeIdentifier()];
        
        $entity->initWorkflow();
        
        // determine available workflow actions
        $workflowHelper = $this->get('rk_websitehelper_module.workflow_helper');
        $actions = $workflowHelper->getActionsForObject($entity);
        if (false === $actions || !is_array($actions)) {
            $this->addFlash('error', $this->__('Error! Could not determine workflow actions.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but failed to determine available workflow actions.', $logArgs);
            throw new \RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
        
        if ($isAdmin) {
            // redirect to the list of carousel items
            $redirectRoute = 'rkwebsitehelpermodule_carouselitem_' . ($isAdmin ? 'admin' : '') . 'view';
        } else {
            // redirect to the list of carousel items
            $redirectRoute = 'rkwebsitehelpermodule_carouselitem_' . ($isAdmin ? 'admin' : '') . 'view';
        }
        
        // check whether deletion is allowed
        $deleteActionId = 'delete';
        $deleteAllowed = false;
        foreach ($actions as $actionId => $action) {
            if ($actionId != $deleteActionId) {
                continue;
            }
            $deleteAllowed = true;
            break;
        }
        if (!$deleteAllowed) {
            $this->addFlash('error', $this->__('Error! It is not allowed to delete this carousel item.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but this action was not allowed.', $logArgs);
        
            return $this->redirectToRoute($redirectRoute);
        }
        
        $form = $this->createForm('RK\WebsiteHelperModule\Form\DeleteEntityType', $entity);
        
        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $hookHelper = $this->get('rk_websitehelper_module.hook_helper');
                // Let any hooks perform additional validation actions
                $hookType = 'validate_delete';
                $validationHooksPassed = $hookHelper->callValidationHooks($entity, $hookType);
                if ($validationHooksPassed) {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($entity, $deleteActionId);
                    if ($success) {
                        $this->addFlash('status', $this->__('Done! Item deleted.'));
                        $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', $logArgs);
                    }
                    
                    // Let any hooks know that we have deleted the carousel item
                    $hookType = 'process_delete';
                    $hookHelper->callProcessHooks($entity, $hookType, null);
                    
                    return $this->redirectToRoute($redirectRoute);
                }
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
        
                return $this->redirectToRoute($redirectRoute);
            }
        }
        
        $repository = $this->get('rk_websitehelper_module.' . $objectType . '_factory')->getRepository();
        
        $viewHelper = $this->get('rk_websitehelper_module.view_helper');
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            'deleteForm' => $form->createView()
        ];
        
        $templateParameters[$objectType] = $entity;
        $imageHelper = $this->get('rk_websitehelper_module.image_helper');
        $templateParameters = array_merge($templateParameters, $repository->getAdditionalTemplateParameters($imageHelper, 'controllerAction', $utilArgs));
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($this->get('twig'), $objectType, 'delete', $request, $templateParameters);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return bool true on sucess, false on failure
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, true);
    }
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return bool true on sucess, false on failure
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminHandleSelectedEntriesAction() and handleSelectedEntriesAction().
     */
    protected function handleSelectedEntriesActionInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'carouselItem';
        
        // Get parameters
        $action = $request->request->get('action', null);
        $items = $request->request->get('items', null);
        
        $action = strtolower($action);
        
        $workflowHelper = $this->get('rk_websitehelper_module.workflow_helper');
        $hookHelper = $this->get('rk_websitehelper_module.hook_helper');
        $logger = $this->get('logger');
        $userName = $this->get('zikula_users_module.current_user')->get('uname');
        
        // process each item
        foreach ($items as $itemid) {
            // check if item exists, and get record instance
            $selectionHelper = $this->get('rk_websitehelper_module.selection_helper');
            $entity = $selectionHelper->getEntity($objectType, $itemid, false);
        
            $entity->initWorkflow();
        
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds)) {
                // action not allowed, skip this object
                continue;
            }
        
            // Let any hooks perform additional validation actions
            $hookType = $action == 'delete' ? 'validate_delete' : 'validate_edit';
            $validationHooksPassed = $hookHelper->callValidationHooks($entity, $hookType);
            if (!$validationHooksPassed) {
                continue;
            }
        
            $success = false;
            try {
                if ($action != 'delete' && !$entity->validate()) {
                    continue;
                }
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch(\Exception $e) {
                $this->addFlash('error', $this->__f('Sorry, but an error occured during the %s action.', ['%s' => $action]) . '  ' . $e->getMessage());
                $logger->error('{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'RKWebsiteHelperModule', 'user' => $userName, 'action' => $action, 'entity' => 'carousel item', 'id' => $itemid, 'errorMessage' => $e->getMessage()]);
            }
        
            if (!$success) {
                continue;
            }
        
            if ($action == 'delete') {
                $this->addFlash('status', $this->__('Done! Item deleted.'));
                $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', ['app' => 'RKWebsiteHelperModule', 'user' => $userName, 'entity' => 'carousel item', 'id' => $itemid]);
            } else {
                $this->addFlash('status', $this->__('Done! Item updated.'));
                $logger->notice('{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.', ['app' => 'RKWebsiteHelperModule', 'user' => $userName, 'action' => $action, 'entity' => 'carousel item', 'id' => $itemid]);
            }
        
            // Let any hooks know that we have updated or deleted an item
            $hookType = $action == 'delete' ? 'process_delete' : 'process_edit';
            $url = null;
            if ($action != 'delete') {
                $urlArgs = $entity->createUrlArgs();
                $urlArgs['_locale'] = $request->getLocale();
                $url = new RouteUrl('rkwebsitehelpermodule_carouselItem_' . /*($isAdmin ? 'admin' : '') . */'display', $urlArgs);
            }
            $hookHelper->callProcessHooks($entity, $hookType, $url);
        }
        
        return $this->redirectToRoute('rkwebsitehelpermodule_carouselitem_' . ($isAdmin ? 'admin' : '') . 'index');
    }
}
