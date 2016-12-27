<?php
/**
 * WebsiteHelper.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace RK\WebsiteHelperModule\Base;

use Doctrine\DBAL\Connection;
use EventUtil;
use RuntimeException;
use UserUtil;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula_Workflow_Util;

/**
 * Installer base class.
 */
abstract class AbstractWebsiteHelperModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * Install the RKWebsiteHelperModule application.
     *
     * @return boolean True on success, or false
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        $logger = $this->container->get('logger');
    
        // Check if upload directories exist and if needed create them
        try {
            $container = $this->container;
            $controllerHelper = new \RK\WebsiteHelperModule\Helper\ControllerHelper($container, $container->get('translator.default'), $container->get('session'), $container->get('logger'));
            $controllerHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            $logger->error('{app}: User {user} could not create upload folders during installation. Error details: {errorMessage}.', ['app' => 'RKWebsiteHelperModule', 'user' => $userName, 'errorMessage' => $e->getMessage()]);
        
            return false;
        }
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->listEntityClasses());
        } catch (\Exception $e) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'RKWebsiteHelperModule', 'errorMessage' => $e->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('enableShrinkingForLinkerLinkerImage', false);
        $this->setVar('shrinkWidthLinkerLinkerImage', 800);
        $this->setVar('shrinkHeightLinkerLinkerImage', 600);
        $this->setVar('enableShrinkingForCarouselItemItemImage', false);
        $this->setVar('shrinkWidthCarouselItemItemImage', 800);
        $this->setVar('shrinkHeightCarouselItemItemImage', 600);
        $this->setVar('enableShrinkingForWebsiteImageMyImage', false);
        $this->setVar('shrinkWidthWebsiteImageMyImage', 800);
        $this->setVar('shrinkHeightWebsiteImageMyImage', 600);
        $this->setVar('thumbnailModeLinker',  'inset' );
        $this->setVar('thumbnailWidthLinkerLinkerImageView', 32);
        $this->setVar('thumbnailHeightLinkerLinkerImageView', 24);
        $this->setVar('thumbnailWidthLinkerLinkerImageDisplay', 240);
        $this->setVar('thumbnailHeightLinkerLinkerImageDisplay', 180);
        $this->setVar('thumbnailWidthLinkerLinkerImageEdit', 240);
        $this->setVar('thumbnailHeightLinkerLinkerImageEdit', 180);
        $this->setVar('thumbnailModeCarouselItem',  'inset' );
        $this->setVar('thumbnailWidthCarouselItemItemImageView', 32);
        $this->setVar('thumbnailHeightCarouselItemItemImageView', 24);
        $this->setVar('thumbnailWidthCarouselItemItemImageEdit', 240);
        $this->setVar('thumbnailHeightCarouselItemItemImageEdit', 180);
        $this->setVar('thumbnailModeWebsiteImage',  'inset' );
        $this->setVar('thumbnailWidthWebsiteImageMyImageView', 32);
        $this->setVar('thumbnailHeightWebsiteImageMyImageView', 24);
        $this->setVar('thumbnailWidthWebsiteImageMyImageDisplay', 240);
        $this->setVar('thumbnailHeightWebsiteImageMyImageDisplay', 180);
        $this->setVar('thumbnailWidthWebsiteImageMyImageEdit', 240);
        $this->setVar('thumbnailHeightWebsiteImageMyImageEdit', 180);
    
        // create the default data
        $this->createDefaultData();
    
        // install subscriber hooks
        $this->hookApi->installSubscriberHooks($this->bundle->getMetaData());
        
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the RKWebsiteHelperModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->listEntityClasses());
                } catch (\Exception $e) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'RKWebsiteHelperModule', 'errorMessage' => $e->getMessage()]);
    
                    return false;
                }
        }
    
        // Note there are several helpers available for making migration of your extension easier.
        // The following convenience methods are each responsible for a single aspect of upgrading to Zikula 1.4.0.
    
        // here is a possible usage example
        // of course 1.2.3 should match the number you used for the last stable 1.3.x module version.
        /* if ($oldVersion = '1.2.3') {
            // rename module for all modvars
            $this->updateModVarsTo14();
            
            // update extension information about this app
            $this->updateExtensionInfoFor14();
            
            // rename existing permission rules
            $this->renamePermissionsFor14();
            
            // rename all tables
            $this->renameTablesFor14();
            
            // remove event handler definitions from database
            $this->dropEventHandlersFromDatabase();
            
            // update module name in the hook tables
            $this->updateHookNamesFor14();
            
            // update module name in the workflows table
            $this->updateWorkflowsFor14();
        } * /
    */
    
        // update successful
        return true;
    }
    
    /**
     * Renames the module name for variables in the module_vars table.
     */
    protected function updateModVarsTo14()
    {
        $dbName = $this->getDbName();
        $conn = $this->getConnection();
    
        $conn->executeQuery("
            UPDATE $dbName.module_vars
            SET modname = 'RKWebsiteHelperModule'
            WHERE modname = 'WebsiteHelper';
        ");
    }
    
    /**
     * Renames this application in the core's extensions table.
     */
    protected function updateExtensionInfoFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("
            UPDATE $dbName.modules
            SET name = 'RKWebsiteHelperModule',
                directory = 'RK/WebsiteHelperModule'
            WHERE name = 'WebsiteHelper';
        ");
    }
    
    /**
     * Renames all permission rules stored for this app.
     */
    protected function renamePermissionsFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $componentLength = strlen('WebsiteHelper') + 1;
    
        $conn->executeQuery("
            UPDATE $dbName.group_perms
            SET component = CONCAT('RKWebsiteHelperModule', SUBSTRING(component, $componentLength))
            WHERE component LIKE 'WebsiteHelper%';
        ");
    }
    
    /**
     * Renames all (existing) tables of this app.
     */
    protected function renameTablesFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $oldPrefix = 'websit_';
        $oldPrefixLength = strlen($oldPrefix);
        $newPrefix = 'rk_websit_';
    
        $sm = $conn->getSchemaManager();
        $tables = $sm->listTables();
        foreach ($tables as $table) {
            $tableName = $table->getName();
            if (substr($tableName, 0, $oldPrefixLength) != $oldPrefix) {
                continue;
            }
    
            $newTableName = str_replace($oldPrefix, $newPrefix, $tableName);
    
            $conn->executeQuery("
                RENAME TABLE $dbName.$tableName
                TO $dbName.$newTableName;
            ");
        }
    }
    
    /**
     * Removes event handlers from database as they are now described by service definitions and managed by dependency injection.
     */
    protected function dropEventHandlersFromDatabase()
    {
        EventUtil::unregisterPersistentModuleHandlers('WebsiteHelper');
    }
    
    /**
     * Updates the module name in the hook tables.
     */
    protected function updateHookNamesFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("
            UPDATE $dbName.hook_area
            SET owner = 'RKWebsiteHelperModule'
            WHERE owner = 'WebsiteHelper';
        ");
    
        $componentLength = strlen('subscriber.websitehelper') + 1;
        $conn->executeQuery("
            UPDATE $dbName.hook_area
            SET areaname = CONCAT('subscriber.rkwebsitehelpermodule', SUBSTRING(areaname, $componentLength))
            WHERE areaname LIKE 'subscriber.websitehelper%';
        ");
    
        $conn->executeQuery("
            UPDATE $dbName.hook_binding
            SET sowner = 'RKWebsiteHelperModule'
            WHERE sowner = 'WebsiteHelper';
        ");
    
        $conn->executeQuery("
            UPDATE $dbName.hook_runtime
            SET sowner = 'RKWebsiteHelperModule'
            WHERE sowner = 'WebsiteHelper';
        ");
    
        $componentLength = strlen('websitehelper') + 1;
        $conn->executeQuery("
            UPDATE $dbName.hook_runtime
            SET eventname = CONCAT('rkwebsitehelpermodule', SUBSTRING(eventname, $componentLength))
            WHERE eventname LIKE 'websitehelper%';
        ");
    
        $conn->executeQuery("
            UPDATE $dbName.hook_subscriber
            SET owner = 'RKWebsiteHelperModule'
            WHERE owner = 'WebsiteHelper';
        ");
    
        $componentLength = strlen('websitehelper') + 1;
        $conn->executeQuery("
            UPDATE $dbName.hook_subscriber
            SET eventname = CONCAT('rkwebsitehelpermodule', SUBSTRING(eventname, $componentLength))
            WHERE eventname LIKE 'websitehelper%';
        ");
    }
    
    /**
     * Updates the module name in the workflows table.
     */
    protected function updateWorkflowsFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("
            UPDATE $dbName.workflows
            SET module = 'RKWebsiteHelperModule'
            WHERE module = 'WebsiteHelper';
        ");
    }
    
    /**
     * Returns connection to the database.
     *
     * @return Connection the current connection
     */
    protected function getConnection()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $connection = $entityManager->getConnection();
    
        return $connection;
    }
    
    /**
     * Returns the name of the default system database.
     *
     * @return string the database name
     */
    protected function getDbName()
    {
        return $this->container->getParameter('database_name');
    }
    
    /**
     * Uninstall RKWebsiteHelperModule.
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        // delete stored object workflows
        $result = Zikula_Workflow_Util::deleteWorkflowsForModule('RKWebsiteHelperModule');
        if (false === $result) {
            $this->addFlash('error', $this->__f('An error was encountered while removing stored object workflows for the %s extension.', ['%s' => 'RKWebsiteHelperModule']));
            $logger->error('{app}: Could not remove stored object workflows during uninstallation.', ['app' => 'RKWebsiteHelperModule']);
    
            return false;
        }
    
        try {
            $this->schemaTool->drop($this->listEntityClasses());
        } catch (\Exception $e) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'RKWebsiteHelperModule', 'errorMessage' => $e->getMessage()]);
    
            return false;
        }
    
        // uninstall subscriber hooks
        $this->hookApi->uninstallSubscriberHooks($this->bundle->getMetaData());
        
    
        // remove all module vars
        $this->delVars();
    
        // remove all thumbnails
        $manager = $this->container->get('systemplugin.imagine.manager');
        $manager->setModule('RKWebsiteHelperModule');
        $manager->cleanupModuleThumbs();
    
        // remind user about upload folders not being deleted
        $uploadPath = $this->container->getParameter('datadir') . '/RKWebsiteHelperModule/';
        $this->addFlash('status', $this->__f('The upload directories at [%s] can be removed manually.', ['%s' => $uploadPath]));
    
        // uninstallation successful
        return true;
    }
    
    /**
     * Build array with all entity classes for RKWebsiteHelperModule.
     *
     * @return array list of class names
     */
    protected function listEntityClasses()
    {
        $classNames = [];
        $classNames[] = 'RK\WebsiteHelperModule\Entity\LinkerEntity';
        $classNames[] = 'RK\WebsiteHelperModule\Entity\CarouselItemEntity';
        $classNames[] = 'RK\WebsiteHelperModule\Entity\CarouselEntity';
        $classNames[] = 'RK\WebsiteHelperModule\Entity\WebsiteImageEntity';
    
        return $classNames;
    }
    
    /**
     * Create the default data for RKWebsiteHelperModule.
     *
     * @return void
     */
    protected function createDefaultData()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $logger = $this->container->get('logger');
        $request = $this->container->get('request_stack')->getMasterRequest();
        
        $entityClass = 'RK\WebsiteHelperModule\Entity\LinkerEntity';
        $entityManager->getRepository($entityClass)->truncateTable($logger);
        $entityClass = 'RK\WebsiteHelperModule\Entity\CarouselItemEntity';
        $entityManager->getRepository($entityClass)->truncateTable($logger);
        $entityClass = 'RK\WebsiteHelperModule\Entity\CarouselEntity';
        $entityManager->getRepository($entityClass)->truncateTable($logger);
        $entityClass = 'RK\WebsiteHelperModule\Entity\WebsiteImageEntity';
        $entityManager->getRepository($entityClass)->truncateTable($logger);
    }
}
