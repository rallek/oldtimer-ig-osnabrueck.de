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

namespace RK\ParkHausModule\Helper\Base;

use DataUtil;
use FileUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\RouteUrl;

/**
 * Helper base class for controller layer methods.
 */
abstract class AbstractControllerHelper
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ControllerHelper constructor.
     *
     * @param ContainerBuilder    $container  ContainerBuilder service instance
     * @param TranslatorInterface $translator Translator service instance
     * @param SessionInterface    $session    Session service instance
     * @param LoggerInterface     $logger     Logger service instance
     */
    public function __construct(ContainerBuilder $container, TranslatorInterface $translator, SessionInterface $session, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->translator = $translator;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * Returns an array of all allowed object types in RKParkHausModule.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, helper, actionHandler, block, contentType, util)
     * @param array  $args    Additional arguments
     *
     * @return array List of allowed object types
     */
    public function getObjectTypes($context = '', $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'util'])) {
            $context = 'controllerAction';
        }
    
        $allowedObjectTypes = [];
        $allowedObjectTypes[] = 'vehicle';
        $allowedObjectTypes[] = 'vehicleImage';
    
        return $allowedObjectTypes;
    }

    /**
     * Returns the default object type in RKParkHausModule.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, helper, actionHandler, block, contentType, util)
     * @param array  $args    Additional arguments
     *
     * @return string The name of the default object type
     */
    public function getDefaultObjectType($context = '', $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'util'])) {
            $context = 'controllerAction';
        }
    
        $defaultObjectType = 'vehicle';
    
        return $defaultObjectType;
    }

    /**
     * Checks whether a certain entity type uses composite keys or not.
     *
     * @param string $objectType The object type to retrieve
     *
     * @return Boolean Whether composite keys are used or not
     */
    public function hasCompositeKeys($objectType)
    {
        switch ($objectType) {
            case 'vehicle':
                return false;
            case 'vehicleImage':
                return false;
                default:
                    return false;
        }
    }

    /**
     * Retrieve identifier parameters for a given object type.
     *
     * @param Request $request    The current request
     * @param array   $args       List of arguments used as fallback if request does not contain a field
     * @param string  $objectType Name of treated entity type
     * @param array   $idFields   List of identifier field names
     *
     * @return array List of fetched identifiers
     */
    public function retrieveIdentifier(Request $request, array $args, $objectType = '', array $idFields)
    {
        $idValues = [];
        $routeParams = $request->get('_route_params', []);
        foreach ($idFields as $idField) {
            $defaultValue = isset($args[$idField]) && is_numeric($args[$idField]) ? $args[$idField] : 0;
            if ($this->hasCompositeKeys($objectType)) {
                // composite key may be alphanumeric
                if (array_key_exists($idField, $routeParams)) {
                    $id = !empty($routeParams[$idField]) ? $routeParams[$idField] : $defaultValue;
                } elseif ($request->query->has($idField)) {
                    $id = $request->query->getAlnum($idField, $defaultValue);
                } else {
                    $id = $defaultValue;
                }
            } else {
                // single identifier
                if (array_key_exists($idField, $routeParams)) {
                    $id = (int) !empty($routeParams[$idField]) ? $routeParams[$idField] : $defaultValue;
                } elseif ($request->query->has($idField)) {
                    $id = $request->query->getInt($idField, $defaultValue);
                } else {
                    $id = $defaultValue;
                }
            }
    
            // fallback if id has not been found yet
            if (!$id && $idField != 'id' && count($idFields) == 1) {
                $defaultValue = isset($args['id']) && is_numeric($args['id']) ? $args['id'] : 0;
                if (array_key_exists('id', $routeParams)) {
                    $id = (int) !empty($routeParams['id']) ? $routeParams['id'] : $defaultValue;
                } elseif ($request->query->has('id')) {
                    $id = (int) $request->query->getInt('id', $defaultValue);
                } else {
                    $id = $defaultValue;
                }
            }
            $idValues[$idField] = $id;
        }
    
        return $idValues;
    }

    /**
     * Checks if all identifiers are set properly.
     *
     * @param array  $idValues List of identifier field values
     *
     * @return boolean Whether all identifiers are set or not
     */
    public function isValidIdentifier(array $idValues)
    {
        if (!count($idValues)) {
            return false;
        }
    
        foreach ($idValues as $idField => $idValue) {
            if (!$idValue) {
                return false;
            }
        }
    
        return true;
    }

    /**
     * Create nice permalinks.
     *
     * @param string $name The given object title
     *
     * @return string processed permalink
     * @deprecated made obsolete by Doctrine extensions
     */
    public function formatPermalink($name)
    {
        $name = str_replace(
            ['�', '�', '�', '�', '�', '�', '�', '.', '?', '"', '/', ':', '�', '�', '�'],
            ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss', '', '', '', '-', '-', 'e', 'e', 'a'],
            $name
        );
        $name = DataUtil::formatPermalink($name);
    
        return strtolower($name);
    }

    /**
     * Processes the parameters for a view action.
     * This includes handling pagination, quick navigation forms and other aspects.
     *
     * @param string          $objectType         Name of treated entity type
     * @param SortableColumns $sortableColumns    Used SortableColumns instance
     * @param array           $templateParameters Template data
     * @param boolean         $supportsHooks      Whether hooks are supported or not
     *
     * @return array Enriched template parameters used for creating the response
     */
    public function processViewActionParameters($objectType, SortableColumns $sortableColumns, array $templateParameters = [], $supportsHooks = false)
    {
        if (!in_array($objectType, $this->getObjectTypes())) {
            throw new Exception('Error! Invalid object type received.');
        }
    
        $request = $this->container->get('request_stack')->getMasterRequest();
        $repository = $this->get('rk_parkhaus_module.' . $objectType . '_factory')->getRepository();
        $repository->setRequest($request);
    
        $showOwnEntries = $request->query->getInt('own', $this->getVar('showOnlyOwnEntries', 0));
        $showAllEntries = $request->query->getInt('all', 0);
    
    
        if (true === $supportsHooks) {
            $currentUrlArgs = [];
            if ($showAllEntries == 1) {
                $currentUrlArgs['all'] = 1;
            }
            if ($showOwnEntries == 1) {
                $currentUrlArgs['own'] = 1;
            }
        }
    
        $resultsPerPage = 0;
        if ($showAllEntries != 1) {
            // the number of items displayed on a page for pagination
            $resultsPerPage = $request->query->getInt('num', 0);
            if (in_array($resultsPerPage, [0, 10])) {
                $resultsPerPage = $this->container->get('zikula_extensions_module.api.variable')->get('RKParkHausModule', $objectType . 'EntriesPerPage', 10);
            }
        }
    
        $imageHelper = $this->container->get('rk_parkhaus_module.image_helper');
        $additionalParameters = $repository->getAdditionalTemplateParameters($imageHelper, 'controllerAction', $utilArgs);
    
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
    
        $templateParameters['own'] = $showAllEntries;
        $templateParameters['all'] = $showOwnEntries;
        $templateParameters['num'] = $resultsPerPage;
        $templateParameters['tpl'] = $request->query->getAlnum('tpl', '');
    
        $quickNavForm = $this->container->get('form.factory')->create('RK\ParkHausModule\Form\Type\QuickNavigation\\' . ucfirst($objectType) . 'QuickNavType', $templateParameters);
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
        $sort = $request->query->get('sort');
        $sortdir = $request->query->get('sortdir');
        $sortableColumns->setOrderBy($sortableColumns->getColumn($sort), strtoupper($sortdir));
        $sortableColumns->setAdditionalUrlParameters($additionalUrlParameters);
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
    
        $selectionHelper = $this->get('rk_parkhaus_module.selection_helper');
    
        $where = '';
        if ($showAllEntries == 1) {
            // retrieve item list without pagination
            $entities = $selectionHelper->getEntities($objectType, [], $where, $sort . ' ' . $sortdir);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = $request->query->getInt('pos', 1);
    
            // retrieve item list with pagination
            list($entities, $objectCount) = $selectionHelper->getEntitiesPaginated($objectType, $where, $sort . ' ' . $sortdir, $currentPage, $resultsPerPage);
    
            $templateParameters['currentPage'] = $currentPage;
            $templateParameters['pager'] = [
                'amountOfItems' => $objectCount,
                'itemsPerPage' => $resultsPerPage
            ];
        }
    
        if (true === $supportsHooks) {
            // build RouteUrl instance for display hooks
            $currentUrlArgs['_locale'] = $request->getLocale();
            $currentUrlObject = new RouteUrl('rkparkhausmodule_parkHaus_' . /*($isAdmin ? 'admin' : '') . */'view', $currentUrlArgs);
        }
    
        $templateParameters['items'] = $entities;
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['num'] = $resultsPerPage;
        if (true === $supportsHooks) {
            $templateParameters['currentUrlObject'] = $currentUrlObject;
        }
        $templateParameters = array_merge($templateParameters, $additionalParameters);
    
        $templateParameters['sort'] = $sortableColumns->generateSortableColumns();
        $templateParameters['quickNavForm'] = $quickNavForm->createView();
    
        $templateParameters['showAllEntries'] = $templateParameters['all'];
        $templateParameters['showOwnEntries'] = $templateParameters['own'];
    
        $templateParameters['canBeCreated'] = $this->container->get('rk_parkhaus_module.model_helper')->canBeCreated($objectType);
    
        return $templateParameters;
    }

    /**
     * Retrieve the base path for given object type and upload field combination.
     *
     * @param string  $objectType   Name of treated entity type
     * @param string  $fieldName    Name of upload field
     * @param boolean $ignoreCreate Whether to ignore the creation of upload folders on demand or not
     *
     * @return mixed Output
     *
     * @throws Exception If an invalid object type is used
     */
    public function getFileBaseFolder($objectType, $fieldName, $ignoreCreate = false)
    {
        if (!in_array($objectType, $this->getObjectTypes())) {
            throw new Exception('Error! Invalid object type received.');
        }
    
        $basePath = $this->container->getParameter('datadir') . '/RKParkHausModule/';
    
        switch ($objectType) {
            case 'vehicle':
                $basePath .= 'vehicles/';
                switch ($fieldName) {
                    case 'titleImage':
                        $basePath .= 'titleimage/';
                        break;
                    case 'vehicleImage':
                        $basePath .= 'vehicleimage/';
                        break;
                    case 'manufacturerImage':
                        $basePath .= 'manufacturerimage/';
                        break;
                }
            break;
            case 'vehicleImage':
                $basePath .= 'vehicleimages/vehicleimage/';
            break;
        }
    
        $result = $basePath;
        if (substr($result, -1, 1) != '/') {
            // reappend the removed slash
            $result .= '/';
        }
    
        if (!is_dir($result) && !$ignoreCreate) {
            $this->checkAndCreateAllUploadFolders();
        }
    
        return $result;
    }

    /**
     * Creates all required upload folders for this application.
     *
     * @return Boolean Whether everything went okay or not
     */
    public function checkAndCreateAllUploadFolders()
    {
        $result = true;
    
        $result &= $this->checkAndCreateUploadFolder('vehicle', 'titleImage', 'gif, jpeg, jpg, png');
        $result &= $this->checkAndCreateUploadFolder('vehicle', 'vehicleImage', 'gif, jpeg, jpg, png');
        $result &= $this->checkAndCreateUploadFolder('vehicle', 'manufacturerImage', 'gif, jpeg, jpg, png');
    
        $result &= $this->checkAndCreateUploadFolder('vehicleImage', 'vehicleImage', 'gif, jpeg, jpg, png');
    
        return $result;
    }

    /**
     * Creates upload folder including a subfolder for thumbnail and an .htaccess file within it.
     *
     * @param string $objectType        Name of treated entity type
     * @param string $fieldName         Name of upload field
     * @param string $allowedExtensions String with list of allowed file extensions (separated by ", ")
     *
     * @return Boolean Whether everything went okay or not
     */
    protected function checkAndCreateUploadFolder($objectType, $fieldName, $allowedExtensions = '')
    {
        $uploadPath = $this->getFileBaseFolder($objectType, $fieldName, true);
    
        $fs = new Filesystem();
        $flashBag = $this->session->getFlashBag();
    
        // Check if directory exist and try to create it if needed
        if (!$fs->exists($uploadPath)) {
            try {
                $fs->mkdir($uploadPath, 0777);
            } catch (IOExceptionInterface $e) {
                $flashBag->add('error', $this->translator->__f('The upload directory "%s" does not exist and could not be created. Try to create it yourself and make sure that this folder is accessible via the web and writable by the webserver.', ['%s' => $e->getPath()]));
                $this->logger->error('{app}: The upload directory {directory} does not exist and could not be created.', ['app' => 'RKParkHausModule', 'directory' => $uploadPath]);
    
                return false;
            }
        }
    
        // Check if directory is writable and change permissions if needed
        if (!is_writable($uploadPath)) {
            try {
                $fs->chmod($uploadPath, 0777);
            } catch (IOExceptionInterface $e) {
                $flashBag->add('warning', $this->translator->__f('Warning! The upload directory at "%s" exists but is not writable by the webserver.', ['%s' => $e->getPath()]));
                $this->logger->error('{app}: The upload directory {directory} exists but is not writable by the webserver.', ['app' => 'RKParkHausModule', 'directory' => $uploadPath]);
    
                return false;
            }
        }
    
        // Write a htaccess file into the upload directory
        $htaccessFilePath = $uploadPath . '/.htaccess';
        $htaccessFileTemplate = 'modules/RK/ParkHausModule/Resources/docs/htaccessTemplate';
        if (!$fs->exists($htaccessFilePath) && $fs->exists($htaccessFileTemplate)) {
            try {
                $extensions = str_replace(',', '|', str_replace(' ', '', $allowedExtensions));
                $htaccessContent = str_replace('__EXTENSIONS__', $extensions, file_get_contents($htaccessFileTemplate, false));
                $fs->dumpFile($htaccessFilePath, $htaccessContent);
            } catch (IOExceptionInterface $e) {
                $flashBag->add('error', $this->translator->__f('An error occured during creation of the .htaccess file in directory "%s".', ['%s' => $e->getPath()]));
                $this->logger->error('{app}: An error occured during creation of the .htaccess file in directory {directory}.', ['app' => 'RKParkHausModule', 'directory' => $uploadPath]);
            }
        }
    
        return true;
    }
}
