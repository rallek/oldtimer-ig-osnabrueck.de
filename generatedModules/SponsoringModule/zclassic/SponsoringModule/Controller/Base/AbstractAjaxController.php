<?php
/**
 * Sponsoring.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\SponsoringModule\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\AbstractQuery;
use DataUtil;
use ModUtil;
use RuntimeException;
use System;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\Ajax\AjaxResponse;
use Zikula\Core\Response\Ajax\BadDataResponse;
use Zikula\Core\Response\Ajax\FatalResponse;
use Zikula\Core\Response\Ajax\NotFoundResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Zikula\Core\Response\PlainResponse;

/**
 * Ajax controller class.
 */
abstract class AbstractAjaxController extends AbstractController
{


    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'sponsor');
        
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . '::', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
    }

    
    public function getSponsorSponsoringUserUsersAction(Request $request)
    {
        return $this->getCommonUsersListAction($request);
    }
    
    /**
     * Retrieve a general purpose list of users.
     *
     * @param string $fragment The search fragment
     *
     * @return PlainResponse
     */ 
    public function getCommonUsersListAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::Ajax', '::', ACCESS_EDIT)) {
            return true;
        }
        
        $fragment = '';
        if ($request->isMethod('POST') && $request->request->has('fragment')) {
            $fragment = $request->request->get('fragment', '');
        } elseif ($request->isMethod('GET') && $request->query->has('fragment')) {
            $fragment = $request->query->get('fragment', '');
        }
        
        
        $dql = 'SELECT u FROM Zikula\Module\UsersModule\Entity\UserEntity u WHERE u.uname LIKE :fragment';
        $entityManager = $this->get('doctrine')->getManager();
        $query = $entityManager->createQuery($dql);
        $query->setParameter('fragment', '%' . $fragment . '%');
        $results = $query->getArrayResult();
        
        // load avatar plugin
        include_once 'lib/legacy/viewplugins/function.useravatar.php';
        $view = \Zikula_View::getInstance('RKSponsoringModule', false);
        
        $resultItems = [];
        if (is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $resultItems[] = [
                    'uid' => $result['uid'],
                    'uname' => DataUtil::formatForDisplay($result['uname']),
                    'avatar' => smarty_function_useravatar(['uid' => $result['uid'], 'rating' => 'g'], $view)
                ];
            }
        }
        
        return new JsonResponse($resultItems);
    }
    
    /**
     * Retrieve item list for finder selections in Forms, Content type plugin and Scribite.
     *
     * @param string $ot      Name of currently used object type
     * @param string $sort    Sorting field
     * @param string $sortdir Sorting direction
     *
     * @return AjaxResponse
     */
    public function getItemListFinderAction(Request $request)
    {
        if (!$this->hasPermission($this->name . '::Ajax', '::', ACCESS_EDIT)) {
            return true;
        }
        
        $objectType = 'sponsor';
        if ($request->isMethod('POST') && $request->request->has('ot')) {
            $objectType = $request->request->getAlnum('ot', 'sponsor');
        } elseif ($request->isMethod('GET') && $request->query->has('ot')) {
            $objectType = $request->query->getAlnum('ot', 'sponsor');
        }
        $controllerHelper = $this->get('rk_sponsoring_module.controller_helper');
        $utilArgs = ['controller' => 'ajax', 'action' => 'getItemListFinder'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        
        $repository = $this->get('rk_sponsoring_module.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);
        $selectionHelper = $this->get('rk_sponsoring_module.selection_helper');
        $idFields = $selectionHelper->getIdFields($objectType);
        
        $descriptionField = $repository->getDescriptionFieldName();
        
        $sort = $request->request->getAlnum('sort', '');
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }
        
        $sdir = $request->request->getAlpha('sortdir', '');
        $sdir = strtolower($sdir);
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }
        
        $where = ''; // filters are processed inside the repository class
        $sortParam = $sort . ' ' . $sdir;
        
        $entities = $repository->selectWhere($where, $sortParam);
        
        $slimItems = [];
        $component = $this->name . ':' . ucfirst($objectType) . ':';
        foreach ($entities as $item) {
            $itemId = '';
            foreach ($idFields as $idField) {
                $itemId .= ((!empty($itemId)) ? '_' : '') . $item[$idField];
            }
            if (!$this->hasPermission($component, $itemId . '::', ACCESS_READ)) {
                continue;
            }
            $slimItems[] = $this->prepareSlimItem($objectType, $item, $itemId, $descriptionField);
        }
        
        return new AjaxResponse($slimItems);
    }
    
    /**
     * Builds and returns a slim data array from a given entity.
     *
     * @param string $objectType       The currently treated object type
     * @param object $item             The currently treated entity
     * @param string $itemid           Data item identifier(s)
     * @param string $descriptionField Name of item description field
     *
     * @return array The slim data representation
     */
    protected function prepareSlimItem($objectType, $item, $itemId, $descriptionField)
    {
        $view = Zikula_View::getInstance('RKSponsoringModule', false);
        $view->assign($objectType, $item);
        $previewInfo = base64_encode($view->fetch('External/' . ucfirst($objectType) . '/info.tpl'));
    
        $title = $item->getTitleFromDisplayPattern();
        $description = ($descriptionField != '') ? $item[$descriptionField] : '';
    
        return [
            'id'          => $itemId,
            'title'       => str_replace('&amp;', '&', $title),
            'description' => $description,
            'previewInfo' => $previewInfo
        ];
    }
}
