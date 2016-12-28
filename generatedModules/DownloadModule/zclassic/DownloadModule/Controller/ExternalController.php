<?php
/**
 * DownLoad.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\DownLoadModule\Controller;

use RK\DownLoadModule\Controller\Base\AbstractExternalController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for external calls implementation class.
 *
 * @Route("/external")
 */
class ExternalController extends AbstractExternalController
{
    /**
     * Displays one item of a certain object type using a separate template for external usages.
     *
     * @Route("/display/{ot}/{id}/{source}/{displayMode}",
     *        requirements = {"id" = "\d+", "source" = "contentType|scribite", "displayMode" = "link|embed"},
     *        defaults = {"source" = "contentType", "contentType" = "embed"},
     *        methods = {"GET"}
     * )
     *
     * @param string $ot          The currently treated object type
     * @param int    $id          Identifier of the entity to be shown
     * @param string $source      Source of this call (contentType or scribite)
     * @param string $displayMode Display mode (link or embed)
     *
     * @return string Desired data output
     */
    public function displayAction($ot, $id, $source, $displayMode)
    {
        return parent::displayAction($ot, $id, $source, $displayMode);
    }

    /**
     * Popup selector for Scribite plugins.
     * Finds items of a certain object type.
     *
     * @Route("/finder/{objectType}/{editor}/{sort}/{sortdir}/{pos}/{num}",
     *        requirements = {"editor" = "tinymce|ckeditor", "sortdir" = "asc|desc", "pos" = "\d+", "num" = "\d+"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 0},
     *        methods = {"GET"},
     *        options={"expose"=true}
     * )
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
        return parent::finderAction($request, $objectType, $editor, $sort, $sortdir, $pos, $num);
    }

    // feel free to extend the external controller here
}
