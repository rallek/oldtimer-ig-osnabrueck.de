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

namespace RK\ParkHausModule\Controller\Base;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use PageUtil;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Response\PlainResponse;

/**
 * Controller for external calls base class.
 */
abstract class AbstractExternalController extends AbstractController
{
    /**
     * Displays one item of a certain object type using a separate template for external usages.
     *
     * @param string $objectType  The currently treated object type
     * @param int    $id          Identifier of the entity to be shown
     * @param string $source      Source of this call (contentType or scribite)
     * @param string $displayMode Display mode (link or embed)
     *
     * @return string Desired data output
     */
    public function displayAction($objectType, $id, $source, $displayMode)
    {
        $controllerHelper = $this->get('rk_parkhaus_module.controller_helper');
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controller', $contextArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerType', $contextArgs);
        }
        
        $component = $this->name . ':' . ucfirst($objectType) . ':';
        if (!$this->hasPermission($component, $id . '::', ACCESS_READ)) {
            return '';
        }
        
        $repository = $this->get('rk_parkhaus_module.entity_factory')->getRepository($objectType);
        $repository->setRequest($this->get('request_stack')->getCurrentRequest());
        $selectionHelper = $this->get('rk_parkhaus_module.selection_helper');
        $idFields = $selectionHelper->getIdFields($objectType);
        $idValues = ['id' => $id];
        
        $hasIdentifier = $controllerHelper->isValidIdentifier($idValues);
        if (!$hasIdentifier) {
            return $this->__('Error! Invalid identifier received.');
        }
        
        // assign object data fetched from the database
        $entity = $repository->selectById($idValues);
        if ((!is_array($entity) && !is_object($entity)) || !isset($entity[$idFields[0]])) {
            return $this->__('No such item.');
        }
        
        $entity->initWorkflow();
        
        $instance = $entity->createCompositeIdentifier() . '::';
        
        $templateParameters = [
            'objectType' => $objectType,
            'source' => $source,
            $objectType => $entity,
            'displayMode' => $displayMode
        ];
        
        return $this->render('@RKParkHausModule/External/' . ucfirst($objectType) . '/display.html.twig', $templateParameters);
    }
    
    /**
     * Popup selector for Scribite plugins.
     * Finds items of a certain object type.
     *
     * @param Request $request    The current request
     * @param string  $objectType The object type
     * @param string  $editor     Name of used Scribite editor
     * @param string  $sort       Sorting field
     * @param string  $sortdir    Sorting direction
     * @param int     $pos        Current pager position
     * @param int     $num        Amount of entries to display
     *
     * @return output The external item finder page
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function finderAction(Request $request, $objectType, $editor, $sort, $sortdir, $pos = 1, $num = 0)
    {
        $assetHelper = $this->get('zikula_core.common.theme.asset_helper');
        $cssAssetBag = $this->get('zikula_core.common.theme.assets_css');
        $cssAssetBag->add($assetHelper->resolve('@RKParkHausModule:css/style.css'));
        
        $controllerHelper = $this->get('rk_parkhaus_module.controller_helper');
        $contextArgs = ['controller' => 'external', 'action' => 'finder'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controller', $contextArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerType', $contextArgs);
        }
        
        if (!$this->hasPermission('RKParkHausModule:' . ucfirst($objectType) . ':', '::', ACCESS_COMMENT)) {
            throw new AccessDeniedException();
        }
        
        if (empty($editor) || !in_array($editor, ['ckeditor', 'tinymce'])) {
            return $this->__('Error: Invalid editor context given for external controller action.');
        }
        
        $repository = $this->get('rk_parkhaus_module.entity_factory')->getRepository($objectType);
        $repository->setRequest($request);
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }
        
        $sdir = strtolower($sortdir);
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }
        
        // the current offset which is used to calculate the pagination
        $currentPage = (int) $pos;
        
        // the number of items displayed on a page for pagination
        $resultsPerPage = (int) $num;
        if ($resultsPerPage == 0) {
            $resultsPerPage = $this->getVar('pageSize', 20);
        }
        
        $templateParameters = [
            'editorName' => $editor,
            'objectType' => $objectType,
            'sort' => $sort,
            'sortdir' => $sdir,
            'currentPage' => $currentPage
        ];
        $searchTerm = '';
        
        $formOptions = [
            'objectType' => $objectType,
            'editorName' => $editor
        ];
        $form = $this->createForm('RK\ParkHausModule\Form\Type\Finder\\' . ucfirst($objectType) . 'FinderType', $templateParameters, $formOptions);
        
        if ($form->handleRequest($request)->isValid() && $form->get('update')->isClicked()) {
            $formData = $form->getData();
            $templateParameters = array_merge($templateParameters, $formData);
            $currentPage = $formData['currentPage'];
            $resultsPerPage = $formData['num'];
            $sort = $formData['sort'];
            $sdir = $formData['sortdir'];
            $searchTerm = $formData['q'];
        }
        
        $where = '';
        $sortParam = $sort . ' ' . $sdir;
        if ($searchTerm != '') {
            list($entities, $objectCount) = $repository->selectSearch($searchTerm, [], $sortParam, $currentPage, $resultsPerPage);
        } else {
            list($entities, $objectCount) = $repository->selectWherePaginated($where, $sortParam, $currentPage, $resultsPerPage);
        }
        
        foreach ($entities as $k => $entity) {
            $entity->initWorkflow();
        }
        
        $templateParameters['items'] = $entities;
        $templateParameters['finderForm'] = $form->createView();
        
        $templateParameters['pager'] = [
            'numitems' => $objectCount,
            'itemsperpage' => $resultsPerPage
        ];
        
        $output = $this->renderView('@RKParkHausModule/External/' . ucfirst($objectType) . '/find.html.twig', $templateParameters);
        
        return new PlainResponse($output);
    }
}
