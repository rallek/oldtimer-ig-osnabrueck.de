<?php
/**
 * Websitehelper.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://oldtimer-ig-osnabrueck.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace RK\WebsitehelperModule\Event\Base;

use Symfony\Component\EventDispatcher\Event;
use RK\WebsitehelperModule\Entity\CarouselEntity;

/**
 * Event base class for filtering carousel processing.
 */
class AbstractFilterCarouselEvent extends Event
{
    /**
     * @var CarouselEntity Reference to treated entity instance.
     */
    protected $carousel;

    public function __construct(CarouselEntity $carousel)
    {
        $this->carousel = $carousel;
    }

    public function getCarousel()
    {
        return $this->carousel;
    }
}
