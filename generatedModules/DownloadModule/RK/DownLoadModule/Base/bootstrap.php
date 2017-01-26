<?php
/**
 * DownLoad.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.2 (http://modulestudio.de) at Thu Jan 26 18:25:36 CET 2017.
 */

/**
 * Bootstrap called when application is first initialised at runtime.
 *
 * This is only called once, and only if the core has reason to initialise this module,
 * usually to dispatch a controller request or API.
 */
$container = ServiceUtil::get('service_container');


// check if own service exists (which is not true if the module is not installed yet)
$container = ServiceUtil::get('service_container');
if ($container->has('rk_download_module.archive_helper')) {
    $container->get('rk_download_module.archive_helper')->archiveObjects();
}

