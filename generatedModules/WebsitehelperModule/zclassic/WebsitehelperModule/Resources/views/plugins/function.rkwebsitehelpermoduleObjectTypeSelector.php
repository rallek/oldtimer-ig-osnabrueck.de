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

/**
 * The rkwebsitehelpermoduleObjectTypeSelector plugin provides items for a dropdown selector.
 *
 * Available parameters:
 *   - assign: If set, the results are assigned to the corresponding variable instead of printed out.
 *
 * @param  array            $params All attributes passed to this function from the template
 * @param  Zikula_Form_View $view   Reference to the view object
 *
 * @return string The output of the plugin
 */
function smarty_function_rkwebsitehelpermoduleObjectTypeSelector($params, $view)
{
    $result = [];

    $result[] = ['text' => $this->__('Linkers'), 'value' => 'linker'];
    $result[] = ['text' => $this->__('Carousel items'), 'value' => 'carouselItem'];
    $result[] = ['text' => $this->__('Carousells'), 'value' => 'carousel'];
    $result[] = ['text' => $this->__('Website images'), 'value' => 'websiteImage'];

    if (array_key_exists('assign', $params)) {
        $view->assign($params['assign'], $result);

        return;
    }

    return $result;
}
