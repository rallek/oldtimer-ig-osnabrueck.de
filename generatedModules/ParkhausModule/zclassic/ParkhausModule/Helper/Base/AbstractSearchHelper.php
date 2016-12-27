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

namespace RK\ParkhausModule\Helper\Base;

use ServiceUtil;
use Zikula\Core\RouteUrl;
use Zikula\SearchModule\AbstractSearchable;

/**
 * Search helper base class.
 */
abstract class AbstractSearchHelper extends AbstractSearchable
{
    /**
     * Display the search form.
     *
     * @param boolean    $active  if the module should be checked as active
     * @param array|null $modVars module form vars as previously set
     *
     * @return string Template output
     */
    public function getOptions($active, $modVars = null)
    {
        $serviceManager = ServiceUtil::getManager();
        $permissionApi = $serviceManager->get('zikula_permissions_module.api.permission');
    
        if (!$permissionApi->hasPermission($this->name . '::', '::', ACCESS_READ)) {
            return '';
        }
    
        $templateParameters = [];
    
        $searchTypes = array('vehicle', 'vehicleImage');
        foreach ($searchTypes as $searchType) {
            $templateParameters['active_' . $searchType] = (!isset($args['rKParkhausModuleSearchTypes']) || in_array($searchType, $args['rKParkhausModuleSearchTypes']));
        }
    
        return $this->getContainer()->get('twig')->render('@RKParkhausModule/Search/options.html.twig', $templateParameters);
    }
    
    /**
     * Returns the search results.
     *
     * @param array      $words      Array of words to search for
     * @param string     $searchType AND|OR|EXACT (defaults to AND)
     * @param array|null $modVars    Module form vars passed though
     *
     * @return array List of fetched results
     */
    public function getResults(array $words, $searchType = 'AND', $modVars = null)
    {
        $serviceManager = ServiceUtil::getManager();
        $permissionApi = $serviceManager->get('zikula_permissions_module.api.permission');
        $request = $serviceManager->get('request_stack')->getMasterRequest();
    
        if (!$permissionApi->hasPermission($this->name . '::', '::', ACCESS_READ)) {
            return [];
        }
    
        // save session id as it is used when inserting search results below
        $session = $serviceManager->get('session');
        $sessionId = $session->getId();
    
        // initialise array for results
        $records = [];
    
        // retrieve list of activated object types
        $searchTypes = isset($modVars['objectTypes']) ? (array)$modVars['objectTypes'] : [];
        if (!is_array($searchTypes) || !count($searchTypes)) {
            if ($request->isMethod('GET')) {
                $searchTypes = $request->query->get('rKParkhausModuleSearchTypes', []);
            } elseif ($request->isMethod('POST')) {
                $searchTypes = $request->request->get('rKParkhausModuleSearchTypes', []);
            }
        }
    
        $controllerHelper = $serviceManager->get('rk_parkhaus_module.controller_helper');
        $utilArgs = ['helper' => 'search', 'action' => 'getResults'];
        $allowedTypes = $controllerHelper->getObjectTypes('helper', $utilArgs);
    
        foreach ($searchTypes as $objectType) {
            if (!in_array($objectType, $allowedTypes)) {
                continue;
            }
    
            $whereArray = [];
            $languageField = null;
            switch ($objectType) {
                case 'vehicle':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.vehicleType';
                    $whereArray[] = 'tbl.titleImage';
                    $whereArray[] = 'tbl.copyrightTitleImage';
                    $whereArray[] = 'tbl.vehicleImage';
                    $whereArray[] = 'tbl.copyrightVehicleImage';
                    $whereArray[] = 'tbl.vehicleDescriptionTeaser';
                    $whereArray[] = 'tbl.vehicleDescription';
                    $whereArray[] = 'tbl.manufacturer';
                    $whereArray[] = 'tbl.model';
                    $whereArray[] = 'tbl.built';
                    $whereArray[] = 'tbl.engine';
                    $whereArray[] = 'tbl.displacement';
                    $whereArray[] = 'tbl.cylinders';
                    $whereArray[] = 'tbl.compression';
                    $whereArray[] = 'tbl.fuelManagement';
                    $whereArray[] = 'tbl.fuel';
                    $whereArray[] = 'tbl.horsePower';
                    $whereArray[] = 'tbl.maxSpeed';
                    $whereArray[] = 'tbl.weight';
                    $whereArray[] = 'tbl.brakes';
                    $whereArray[] = 'tbl.gearbox';
                    $whereArray[] = 'tbl.rim';
                    $whereArray[] = 'tbl.tire';
                    $whereArray[] = 'tbl.interior';
                    $whereArray[] = 'tbl.infoField1';
                    $whereArray[] = 'tbl.infoField2';
                    $whereArray[] = 'tbl.infoField3';
                    $whereArray[] = 'tbl.titleTextColor';
                    break;
                case 'vehicleImage':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.titel';
                    $whereArray[] = 'tbl.vehicleImage';
                    $whereArray[] = 'tbl.copyright';
                    $whereArray[] = 'tbl.imageDate';
                    $whereArray[] = 'tbl.description';
                    break;
            }
    
            $repository = $serviceManager->get('rk_parkhaus_module.' . $objectType . '_factory')->getRepository();
    
            // build the search query without any joins
            $qb = $repository->genericBaseQuery('', '', false);
    
            // build where expression for given search type
            $whereExpr = $this->formatWhere($qb, $words, $whereArray, $searchType);
            $qb->andWhere($whereExpr);
    
            $query = $qb->getQuery();
    
            // set a sensitive limit
            $query->setFirstResult(0)
                  ->setMaxResults(250);
    
            // fetch the results
            $entities = $query->getResult();
    
            if (count($entities) == 0) {
                continue;
            }
    
            $descriptionField = $repository->getDescriptionFieldName();
    
            $entitiesWithDisplayAction = ['vehicle', 'vehicleImage'];
    
            foreach ($entities as $entity) {
                $urlArgs = $entity->createUrlArgs();
                $hasDisplayAction = in_array($objectType, $entitiesWithDisplayAction);
    
                $instanceId = $entity->createCompositeIdentifier();
                // perform permission check
                if (!$permissionApi->hasPermission($this->name . ':' . ucfirst($objectType) . ':', $instanceId . '::', ACCESS_OVERVIEW)) {
                    continue;
                }
    
                $description = !empty($descriptionField) ? $entity[$descriptionField] : '';
                $created = isset($entity['createdDate']) ? $entity['createdDate'] : null;
    
                $urlArgs['_locale'] = (null !== $languageField && !empty($entity[$languageField])) ? $entity[$languageField] : $request->getLocale();
    
                $displayUrl = $hasDisplayAction ? new RouteUrl('rkparkhausmodule_' . $objectType . '_display', $urlArgs) : '';
    
                $records[] = [
                    'title' => $entity->getTitleFromDisplayPattern(),
                    'text' => $description,
                    'module' => $this->name,
                    'sesid' => $sessionId,
                    'created' => $created,
                    'url' => $displayUrl
                ];
            }
        }
    
        return $records;
    }
}
