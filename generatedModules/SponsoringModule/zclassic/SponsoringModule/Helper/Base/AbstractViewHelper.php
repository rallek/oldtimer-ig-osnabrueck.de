<?php
/**
 * Sponsoring.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\SponsoringModule\Helper\Base;

use DataUtil;
use PageUtil;
use System;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\Response\PlainResponse;

/**
 * Helper base class for view layer methods.
 */
abstract class AbstractViewHelper
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
     * Constructor.
     * Initialises member vars.
     *
     * @param ContainerBuilder    $container  ContainerBuilder service instance
     * @param TranslatorInterface $translator Translator service instance
     *
     * @return void
     */
    public function __construct(ContainerBuilder $container, TranslatorInterface $translator)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Determines the view template for a certain method with given parameters.
     *
     * @param Twig_Environment $twig     Reference to view object
     * @param string           $type    Current controller (name of currently treated entity)
     * @param string           $func    Current function (index, view, ...)
     * @param Request          $request Current request
     *
     * @return string name of template file
     */
    public function getViewTemplate(Twig_Environment $twig, $type, $func, Request $request)
    {
        // create the base template name
        $template = '@RKSponsoringModule/' . ucfirst($type) . '/' . $func;
    
        // check for template extension
        $templateExtension = $this->determineExtension($twig, $type, $func, $request);
    
        // check whether a special template is used
        $tpl = '';
        if ($request->isMethod('POST')) {
            $tpl = $request->request->getAlnum('tpl', '');
        } elseif ($request->isMethod('GET')) {
            $tpl = $request->query->getAlnum('tpl', '');
        }
    
        $templateExtension = '.' . $templateExtension;
        
        // check if custom template exists
        if (!empty($tpl)) {
            $template .= '_' . DataUtil::formatForOS($tpl);
        }
        $template .= $templateExtension;
    
        return $template;
    }

    /**
     * Helper method for managing view templates.
     *
     * @param Twig_Environment $twig     Reference to view object
     * @param string           $type     Current controller (name of currently treated entity)
     * @param string           $func     Current function (index, view, ...)
     * @param Request          $request            Current request
     * @param array            $templateParameters Template data
     * @param string           $template Optional assignment of precalculated template file
     *
     * @return mixed Output
     */
    public function processTemplate(Twig_Environment $twig, $type, $func, Request $request, $templateParameters = [], $template = '')
    {
        $templateExtension = $this->determineExtension($twig, $type, $func, $request);
        if (empty($template)) {
            $template = $this->getViewTemplate($twig, $type, $func, $request);
        }
    
        // look whether we need output with or without the theme
        $raw = false;
        if ($request->isMethod('POST')) {
            $raw = (bool) $request->request->get('raw', false);
        } elseif ($request->isMethod('GET')) {
            $raw = (bool) $request->query->get('raw', false);
        }
        if (!$raw && $templateExtension != 'html.twig') {
            $raw = true;
        }
    
        $response = null;
        if (true === $raw) {
            // standalone output
            if ($templateExtension == 'pdf.twig') {
                $template = str_replace('.pdf', '.html', $template);
    
                return $this->processPdf($twig, $request, $templateParameters, $template);
            }
    
            $response = new PlainResponse($twig->render($template, $templateParameters));
        }
    
        // normal output
        $response = new Response($twig->render($template, $templateParameters));
    
        // check if we need to set any custom headers
        switch ($templateExtension) {
        }
    
        return $response;
    }

    /**
     * Get extension of the currently treated template.
     *
     * @param Twig_Environment $twig     Reference to view object
     * @param string           $type    Current controller (name of currently treated entity)
     * @param string           $func    Current function (index, view, ...)
     * @param Request          $request Current request
     *
     * @return array List of allowed template extensions
     */
    protected function determineExtension(Twig_Environment $twig, $type, $func, Request $request)
    {
        $templateExtension = 'html.twig';
        if (!in_array($func, ['view', 'display'])) {
            return $templateExtension;
        }
    
        $extensions = $this->availableExtensions($type, $func);
        $format = $request->getRequestFormat();
        if ($format != 'html' && in_array($format, $extensions)) {
            $templateExtension = $format . '.twig';
        }
    
        return $templateExtension;
    }

    /**
     * Get list of available template extensions.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     *
     * @return array List of allowed template extensions
     */
    public function availableExtensions($type, $func)
    {
        $extensions = [];
        $permissionApi = $this->container->get('zikula_permissions_module.api.permission');
        $hasAdminAccess = $permissionApi->hasPermission('RKSponsoringModule:' . ucfirst($type) . ':', '::', ACCESS_ADMIN);
        if ($func == 'view') {
            if ($hasAdminAccess) {
                $extensions = [];
            } else {
                $extensions = [];
            }
        } elseif ($func == 'display') {
            if ($hasAdminAccess) {
                $extensions = [];
            } else {
                $extensions = [];
            }
        }
    
        return $extensions;
    }

    /**
     * Processes a template file using dompdf (LGPL).
     *
     * @param Twig_Environment $twig     Reference to view object
     * @param Request          $request            Current request
     * @param array            $templateParameters Template data
     * @param string           $template Name of template to use
     *
     * @return mixed Output
     */
    protected function processPdf(Twig_Environment $twig, Request $request, $templateParameters = [], $template)
    {
        // first the content, to set page vars
        $output = $twig->render($template, $templateParameters);
    
        // make local images absolute
        $output = str_replace('img src="/', 'img src="' . $request->server->get('DOCUMENT_ROOT') . '/', $output);
    
        // see http://codeigniter.com/forums/viewthread/69388/P15/#561214
        //$output = utf8_decode($output);
    
        // then the surrounding
        $output = $twig->render('includePdfHeader.html.twig') . $output . '</body></html>';
    
        $controllerHelper = $this->container->get('rk_sponsoring_module.controller_helper');
        // create name of the pdf output file
        $fileTitle = $controllerHelper->formatPermalink(System::getVar('sitename'))
                   . '-'
                   . $controllerHelper->formatPermalink(PageUtil::getVar('title'))
                   . '-' . date('Ymd') . '.pdf';
    
        // if ($_GET['dbg'] == 1) die($output);
    
        // instantiate pdf object
        $pdf = new \DOMPDF();
        // define page properties
        $pdf->set_paper('A4');
        // load html input data
        $pdf->load_html($output);
        // create the actual pdf file
        $pdf->render();
        // stream output to browser
        $pdf->stream($fileTitle);
    
        // prevent additional output by shutting down the system
        System::shutDown();
    
        return true;
    }

    /**
     * Display a given file size in a readable format
     *
     * @param string  $size     File size in bytes
     * @param boolean $nodesc   If set to true the description will not be appended
     * @param boolean $onlydesc If set to true only the description will be returned
     *
     * @return string File size in a readable form
     */
    public function getReadableFileSize($size, $nodesc = false, $onlydesc = false)
    {
        $sizeDesc = $this->translator->__('Bytes');
        if ($size >= 1024) {
            $size /= 1024;
            $sizeDesc = $this->translator->__('KB');
        }
        if ($size >= 1024) {
            $size /= 1024;
            $sizeDesc = $this->translator->__('MB');
        }
        if ($size >= 1024) {
            $size /= 1024;
            $sizeDesc = $this->translator->__('GB');
        }
        $sizeDesc = '&nbsp;' . $sizeDesc;
    
        // format number
        $dec_point = ',';
        $thousands_separator = '.';
        if ($size - number_format($size, 0) >= 0.005) {
            $size = number_format($size, 2, $dec_point, $thousands_separator);
        } else {
            $size = number_format($size, 0, '', $thousands_separator);
        }
    
        // append size descriptor if desired
        if (!$nodesc) {
            $size .= $sizeDesc;
        }
    
        // return either only the description or the complete string
        $result = ($onlydesc) ? $sizeDesc : $size;
    
        return $result;
    }
}
