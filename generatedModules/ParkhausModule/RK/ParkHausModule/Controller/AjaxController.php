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

namespace RK\ParkHausModule\Controller;

use RK\ParkHausModule\Controller\Base\AbstractAjaxController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;
use Zikula\Core\Response\Ajax\AjaxResponse;
use Zikula\Core\Response\Ajax\BadDataResponse;
use Zikula\Core\Response\Ajax\FatalResponse;
use Zikula\Core\Response\Ajax\NotFoundResponse;

/**
 * Ajax controller implementation class.
 *
 * @Route("/ajax")
 */
class AjaxController extends AbstractAjaxController
{
    
    /**
     *
     * @Route("/getVehicleOwnerUsers", options={"expose"=true})
     * @Method("GET")
     */
    public function getVehicleOwnerUsersAction(Request $request)
    {
        return parent::getVehicleOwnerUsersAction($request);
    }
    
    /**
     *
     * @Route("/getVehicleImageVehicleOwnerUsers", options={"expose"=true})
     * @Method("GET")
     */
    public function getVehicleImageVehicleOwnerUsersAction(Request $request)
    {
        return parent::getVehicleImageVehicleOwnerUsersAction($request);
    }
    
    /**
     * Retrieve a general purpose list of users.
     *
     * @Route("/getCommonUsersList", options={"expose"=true})
     
     *
     * @param string $fragment The search fragment
     *
     * @return JsonResponse
     */ 
    public function getCommonUsersListAction(Request $request)
    {
        return parent::getCommonUsersListAction($request);
    }
    
    /**
     * Retrieve item list for finder selections in Forms, Content type plugin and Scribite.
    *
    * @Route("/getItemListFinder", options={"expose"=true})
    
     *
     * @param string $ot      Name of currently used object type
     * @param string $sort    Sorting field
     * @param string $sortdir Sorting direction
     *
     * @return AjaxResponse
     */
    public function getItemListFinderAction(Request $request)
    {
        return parent::getItemListFinderAction($request);
    }
    
    /**
     * Searches for entities for auto completion usage.
    *
    * @Route("/getItemListAutoCompletion", options={"expose"=true})
    
     *
     * @param Request $request Current request instance
     *
     * @return JsonResponse
     */
    public function getItemListAutoCompletionAction(Request $request)
    {
        return parent::getItemListAutoCompletionAction($request);
    }
    
    /**
     * Changes a given flag (boolean field) by switching between true and false.
    *
    * @Route("/toggleFlag", options={"expose"=true})
    
     *
     * @param Request $request Current request instance
     *
     * @return AjaxResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function toggleFlagAction(Request $request)
    {
        return parent::toggleFlagAction($request);
    }

    // feel free to add your own ajax controller methods here
}
