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

namespace RK\ParkhausModule\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ModUtil;
use RuntimeException;
use System;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;

/**
 * Admin controller class.
 */
abstract class AbstractAdminController extends AbstractController
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
        $objectType = $request->query->getAlnum('ot', 'vehicle');
        
        $permLevel = ACCESS_ADMIN;
        if (!$this->hasPermission($this->name . '::', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        
        // redirect to view action
        $routeArea = 'admin';
        
        return $this->redirectToRoute('rkparkhausmodule_' . strtolower($objectType) . '_' . $routeArea . 'view');
    }

}
