<?php
/**
 * Team.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\TeamModule\Helper\Base;

use DataUtil;
use FileUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Common\Translator\TranslatorInterface;

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
     * Constructor.
     * Initialises member vars.
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
     * Returns an array of all allowed object types in RKTeamModule.
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
        $allowedObjectTypes[] = 'person';
    
        return $allowedObjectTypes;
    }

    /**
     * Returns the default object type in RKTeamModule.
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
    
        $defaultObjectType = 'person';
    
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
            case 'person':
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
    
        $basePath = $this->container->getParameter('datadir') . '/RKTeamModule/';
    
        switch ($objectType) {
            case 'person':
                $basePath .= 'persons/teammemberimage/';
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
    
        $result &= $this->checkAndCreateUploadFolder('person', 'teamMemberImage', 'gif, jpeg, jpg, png');
    
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
                $this->logger->error('{app}: The upload directory {directory} does not exist and could not be created.', ['app' => 'RKTeamModule', 'directory' => $uploadPath]);
    
                return false;
            }
        }
    
        // Check if directory is writable and change permissions if needed
        if (!is_writable($uploadPath)) {
            try {
                $fs->chmod($uploadPath, 0777);
            } catch (IOExceptionInterface $e) {
                $flashBag->add('warning', $this->translator->__f('Warning! The upload directory at "%s" exists but is not writable by the webserver.', ['%s' => $e->getPath()]));
                $this->logger->error('{app}: The upload directory {directory} exists but is not writable by the webserver.', ['app' => 'RKTeamModule', 'directory' => $uploadPath]);
    
                return false;
            }
        }
    
        // Write a htaccess file into the upload directory
        $htaccessFilePath = $uploadPath . '/.htaccess';
        $htaccessFileTemplate = 'modules/RKTeamModule/Resources/docs/htaccessTemplate';
        if (!$fs->exists($htaccessFilePath) && $fs->exists($htaccessFileTemplate)) {
            try {
                $extensions = str_replace(',', '|', str_replace(' ', '', $allowedExtensions));
                $htaccessContent = str_replace('__EXTENSIONS__', $extensions, file_get_contents($htaccessFileTemplate, false));
                $fs->dumpFile($htaccessFilePath, $htaccessContent);
            } catch (IOExceptionInterface $e) {
                $flashBag->add('error', $this->translator->__f('An error occured during creation of the .htaccess file in directory "%s".', ['%s' => $e->getPath()]));
                $this->logger->error('{app}: An error occured during creation of the .htaccess file in directory {directory}.', ['app' => 'RKTeamModule', 'directory' => $uploadPath]);
            }
        }
    
        return true;
    }
}
