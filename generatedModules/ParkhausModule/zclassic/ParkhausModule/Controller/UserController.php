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

namespace RK\ParkhausModule\Controller;

use RK\ParkhausModule\Controller\Base\AbstractUserController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * User controller class providing navigation and interaction functionality.
 */
class UserController extends AbstractUserController
{
    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @Route("/user",
     *        methods = {"GET"}
     * )
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    // feel free to add your own controller methods here
}
