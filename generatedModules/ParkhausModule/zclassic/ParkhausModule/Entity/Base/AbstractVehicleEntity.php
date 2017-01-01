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

namespace RK\ParkHausModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use RK\ParkHausModule\Traits\StandardFieldsTrait;
use Zikula\UsersModule\Entity\UserEntity;

use DataUtil;
use FormUtil;
use RuntimeException;
use ServiceUtil;
use UserUtil;
use Zikula_Workflow_Util;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for vehicle entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 *
 * @abstract
 */
abstract class AbstractVehicleEntity extends EntityAccess
{
    /**
     * Hook standard fields behaviour.
     * Updates createdUserId, updatedUserId, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'vehicle';
    
    /**
     * @Assert\Type(type="bool")
     * @var boolean Option to bypass validation if needed
     */
    protected $_bypassValidation = false;
    
    /**
     * @var array The current workflow data of this object
     */
    protected $__WORKFLOW__ = [];
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getWorkflowStateAllowedValues", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * please select which type fits best
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getVehicleTypeAllowedValues", multiple=false)
     * @var string $vehicleType
     */
    protected $vehicleType = 'Auto';
    
    /**
     * Title image meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $titleImageMeta
     */
    protected $titleImageMeta = [];
    
    /**
     * This image will be used as the title image. It shoud have a ratio of 3:1 and min 1200px. Bigger than 1800px will be reduced automatically.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Regex(pattern="/\s/", match=false, message="This value must not contain space chars.")
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
        mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
        minRatio = 2.95,
        maxRatio = 3.05,
        allowSquare = false,
        allowPortrait = false
     * )
     * @var string $titleImage
     */
    protected $titleImage = null;
    
    /**
     * Full title image path as url.
     *
     * @Assert\Type(type="string")
     * @Assert\Url()
     * @var string $titleImageUrl
     */
    protected $titleImageUrl = '';
    /**
     * Please fill the copyright. If you got the image from someone else please be fair and name him here.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $copyrightTitleImage
     */
    protected $copyrightTitleImage = '';
    
    /**
     * Vehicle image meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $vehicleImageMeta
     */
    protected $vehicleImageMeta = [];
    
    /**
     * This image should be in landscape format. It will represent your vehicle. In the album you can add multiple additional images.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Regex(pattern="/\s/", match=false, message="This value must not contain space chars.")
     * @Assert\Length(min="0", max="255")
     * @Assert\File(
        mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
        allowSquare = false,
        allowPortrait = false
     * )
     * @var string $vehicleImage
     */
    protected $vehicleImage = null;
    
    /**
     * Full vehicle image path as url.
     *
     * @Assert\Type(type="string")
     * @Assert\Url()
     * @var string $vehicleImageUrl
     */
    protected $vehicleImageUrl = '';
    /**
     * Please fill the copyright. If you qot the image from someone else please be fair and name him here.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $copyrightVehicleImage
     */
    protected $copyrightVehicleImage = '';
    
    /**
     * Vehicle description with max 500 char. It will been shown in the list overview and added in front of the rest of the vehicle description.
     * @ORM\Column(type="text", length=500)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="500")
     * @var text $vehicleDescriptionTeaser
     */
    protected $vehicleDescriptionTeaser = '';
    
    /**
     * This is the text which follows the teaser text at the display page. You can use max 5000 char.
     * @ORM\Column(type="text", length=5000, nullable=true)
     * @Assert\Length(min="0", max="5000")
     * @var text $vehicleDescription
     */
    protected $vehicleDescription = '';
    
    /**
     * if you do not know the manufacturer please type unknown.
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $manufacturer
     */
    protected $manufacturer = '';
    
    /**
     * vehicle version
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $model
     */
    protected $model = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $built
     */
    protected $built = '';
    
    /**
     * e.g. Otto, Diesel, Wankel, ...
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $engine
     */
    protected $engine = '';
    
    /**
     * how much ccm
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $displacement
     */
    protected $displacement = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $cylinders
     */
    protected $cylinders = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $compression
     */
    protected $compression = '';
    
    /**
     * e.g. injection type, carburetor manufacturor and size ...
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $fuelManagement
     */
    protected $fuelManagement = '';
    
    /**
     * z.B. Benzine, Diesel or 1:25
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $fuel
     */
    protected $fuel = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $horsePower
     */
    protected $horsePower = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $maxSpeed
     */
    protected $maxSpeed = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $weight
     */
    protected $weight = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $brakes
     */
    protected $brakes = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $gearbox
     */
    protected $gearbox = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $rim
     */
    protected $rim = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $tire
     */
    protected $tire = '';
    
    /**
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $interior
     */
    protected $interior = '';
    
    /**
     * If there is somethin special what you can not fill into one of the other fields you may want to place it here.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $infoField1
     */
    protected $infoField1 = '';
    
    /**
     * If there is somethin special what you can not fill into one of the other fields you may want to place it here.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $infoField2
     */
    protected $infoField2 = '';
    
    /**
     * If there is something special what you can not fill into one of the other fields you may want to place it here.
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(min="0", max="255")
     * @var string $infoField3
     */
    protected $infoField3 = '';
    
    /**
     * the owner of the vehicle if the owner is registered at our site
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @var integer $owner
     */
    protected $owner = 0;
    
    /**
     * If not checked the registered user will not been shown to the public. Only IG members are able to see.
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     * @var boolean $showVehicleOwner
     */
    protected $showVehicleOwner = false;
    
    /**
     * Please change if white is not fitting due to the color of the title image. Preferred you should use #000000 in case.
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/\s/", match=false, message="This value must not contain space chars.")
     * @Assert\Length(min="0", max="255")
     * @Assert\Regex(pattern="/^#?(([a-fA-F0-9]{3}){1,2})$/", message="This value must be a valid html colour code [#123 or #123456].")
     * @var string $titleTextColor
     */
    protected $titleTextColor = '#ffffff';
    
    
    /**
     * Bidirectional - One vehicle [vehicle] has many vehicleImages [vehicle images] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="RK\ParkHausModule\Entity\VehicleImageEntity", mappedBy="vehicle")
     * @ORM\JoinTable(name="rk_parkha_vehiclevehicleimages")
     * @var \RK\ParkHausModule\Entity\VehicleImageEntity[] $vehicleImages
     */
    protected $vehicleImages = null;
    
    
    /**
     * Constructor.
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     *
     * @param TODO
     */
    public function __construct()
    {
        $this->initWorkflow();
        $this->vehicleImages = new ArrayCollection();
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        $this->_objectType = $_objectType;
    }
    
    /**
     * Returns the _bypass validation.
     *
     * @return boolean
     */
    public function get_bypassValidation()
    {
        return $this->_bypassValidation;
    }
    
    /**
     * Sets the _bypass validation.
     *
     * @param boolean $_bypassValidation
     *
     * @return void
     */
    public function set_bypassValidation($_bypassValidation)
    {
        $this->_bypassValidation = $_bypassValidation;
    }
    
    /**
     * Returns the __ w o r k f l o w__.
     *
     * @return array
     */
    public function get__WORKFLOW__()
    {
        return $this->__WORKFLOW__;
    }
    
    /**
     * Sets the __ w o r k f l o w__.
     *
     * @param array $__WORKFLOW__
     *
     * @return void
     */
    public function set__WORKFLOW__($__WORKFLOW__ = [])
    {
        $this->__WORKFLOW__ = $__WORKFLOW__;
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        $this->workflowState = isset($workflowState) ? $workflowState : '';
    }
    
    /**
     * Returns the vehicle type.
     *
     * @return string
     */
    public function getVehicleType()
    {
        return $this->vehicleType;
    }
    
    /**
     * Sets the vehicle type.
     *
     * @param string $vehicleType
     *
     * @return void
     */
    public function setVehicleType($vehicleType)
    {
        $this->vehicleType = isset($vehicleType) ? $vehicleType : '';
    }
    
    /**
     * Returns the title image.
     *
     * @return string
     */
    public function getTitleImage()
    {
        return $this->titleImage;
    }
    
    /**
     * Sets the title image.
     *
     * @param string $titleImage
     *
     * @return void
     */
    public function setTitleImage($titleImage)
    {
        $this->titleImage = $titleImage;
    }
    
    /**
     * Returns the title image url.
     *
     * @return string
     */
    public function getTitleImageUrl()
    {
        return $this->titleImageUrl;
    }
    
    /**
     * Sets the title image url.
     *
     * @param string $titleImageUrl
     *
     * @return void
     */
    public function setTitleImageUrl($titleImageUrl)
    {
        $this->titleImageUrl = $titleImageUrl;
    }
    
    /**
     * Returns the title image meta.
     *
     * @return array
     */
    public function getTitleImageMeta()
    {
        return $this->titleImageMeta;
    }
    
    /**
     * Sets the title image meta.
     *
     * @param array $titleImageMeta
     *
     * @return void
     */
    public function setTitleImageMeta($titleImageMeta = [])
    {
        $this->titleImageMeta = $titleImageMeta;
    }
    
    /**
     * Returns the copyright title image.
     *
     * @return string
     */
    public function getCopyrightTitleImage()
    {
        return $this->copyrightTitleImage;
    }
    
    /**
     * Sets the copyright title image.
     *
     * @param string $copyrightTitleImage
     *
     * @return void
     */
    public function setCopyrightTitleImage($copyrightTitleImage)
    {
        $this->copyrightTitleImage = $copyrightTitleImage;
    }
    
    /**
     * Returns the vehicle image.
     *
     * @return string
     */
    public function getVehicleImage()
    {
        return $this->vehicleImage;
    }
    
    /**
     * Sets the vehicle image.
     *
     * @param string $vehicleImage
     *
     * @return void
     */
    public function setVehicleImage($vehicleImage)
    {
        $this->vehicleImage = $vehicleImage;
    }
    
    /**
     * Returns the vehicle image url.
     *
     * @return string
     */
    public function getVehicleImageUrl()
    {
        return $this->vehicleImageUrl;
    }
    
    /**
     * Sets the vehicle image url.
     *
     * @param string $vehicleImageUrl
     *
     * @return void
     */
    public function setVehicleImageUrl($vehicleImageUrl)
    {
        $this->vehicleImageUrl = $vehicleImageUrl;
    }
    
    /**
     * Returns the vehicle image meta.
     *
     * @return array
     */
    public function getVehicleImageMeta()
    {
        return $this->vehicleImageMeta;
    }
    
    /**
     * Sets the vehicle image meta.
     *
     * @param array $vehicleImageMeta
     *
     * @return void
     */
    public function setVehicleImageMeta($vehicleImageMeta = [])
    {
        $this->vehicleImageMeta = $vehicleImageMeta;
    }
    
    /**
     * Returns the copyright vehicle image.
     *
     * @return string
     */
    public function getCopyrightVehicleImage()
    {
        return $this->copyrightVehicleImage;
    }
    
    /**
     * Sets the copyright vehicle image.
     *
     * @param string $copyrightVehicleImage
     *
     * @return void
     */
    public function setCopyrightVehicleImage($copyrightVehicleImage)
    {
        $this->copyrightVehicleImage = $copyrightVehicleImage;
    }
    
    /**
     * Returns the vehicle description teaser.
     *
     * @return text
     */
    public function getVehicleDescriptionTeaser()
    {
        return $this->vehicleDescriptionTeaser;
    }
    
    /**
     * Sets the vehicle description teaser.
     *
     * @param text $vehicleDescriptionTeaser
     *
     * @return void
     */
    public function setVehicleDescriptionTeaser($vehicleDescriptionTeaser)
    {
        $this->vehicleDescriptionTeaser = isset($vehicleDescriptionTeaser) ? $vehicleDescriptionTeaser : '';
    }
    
    /**
     * Returns the vehicle description.
     *
     * @return text
     */
    public function getVehicleDescription()
    {
        return $this->vehicleDescription;
    }
    
    /**
     * Sets the vehicle description.
     *
     * @param text $vehicleDescription
     *
     * @return void
     */
    public function setVehicleDescription($vehicleDescription)
    {
        $this->vehicleDescription = $vehicleDescription;
    }
    
    /**
     * Returns the manufacturer.
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
    
    /**
     * Sets the manufacturer.
     *
     * @param string $manufacturer
     *
     * @return void
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = isset($manufacturer) ? $manufacturer : '';
    }
    
    /**
     * Returns the model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * Sets the model.
     *
     * @param string $model
     *
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
    
    /**
     * Returns the built.
     *
     * @return string
     */
    public function getBuilt()
    {
        return $this->built;
    }
    
    /**
     * Sets the built.
     *
     * @param string $built
     *
     * @return void
     */
    public function setBuilt($built)
    {
        $this->built = $built;
    }
    
    /**
     * Returns the engine.
     *
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }
    
    /**
     * Sets the engine.
     *
     * @param string $engine
     *
     * @return void
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }
    
    /**
     * Returns the displacement.
     *
     * @return string
     */
    public function getDisplacement()
    {
        return $this->displacement;
    }
    
    /**
     * Sets the displacement.
     *
     * @param string $displacement
     *
     * @return void
     */
    public function setDisplacement($displacement)
    {
        $this->displacement = $displacement;
    }
    
    /**
     * Returns the cylinders.
     *
     * @return string
     */
    public function getCylinders()
    {
        return $this->cylinders;
    }
    
    /**
     * Sets the cylinders.
     *
     * @param string $cylinders
     *
     * @return void
     */
    public function setCylinders($cylinders)
    {
        $this->cylinders = $cylinders;
    }
    
    /**
     * Returns the compression.
     *
     * @return string
     */
    public function getCompression()
    {
        return $this->compression;
    }
    
    /**
     * Sets the compression.
     *
     * @param string $compression
     *
     * @return void
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;
    }
    
    /**
     * Returns the fuel management.
     *
     * @return string
     */
    public function getFuelManagement()
    {
        return $this->fuelManagement;
    }
    
    /**
     * Sets the fuel management.
     *
     * @param string $fuelManagement
     *
     * @return void
     */
    public function setFuelManagement($fuelManagement)
    {
        $this->fuelManagement = $fuelManagement;
    }
    
    /**
     * Returns the fuel.
     *
     * @return string
     */
    public function getFuel()
    {
        return $this->fuel;
    }
    
    /**
     * Sets the fuel.
     *
     * @param string $fuel
     *
     * @return void
     */
    public function setFuel($fuel)
    {
        $this->fuel = $fuel;
    }
    
    /**
     * Returns the horse power.
     *
     * @return string
     */
    public function getHorsePower()
    {
        return $this->horsePower;
    }
    
    /**
     * Sets the horse power.
     *
     * @param string $horsePower
     *
     * @return void
     */
    public function setHorsePower($horsePower)
    {
        $this->horsePower = $horsePower;
    }
    
    /**
     * Returns the max speed.
     *
     * @return string
     */
    public function getMaxSpeed()
    {
        return $this->maxSpeed;
    }
    
    /**
     * Sets the max speed.
     *
     * @param string $maxSpeed
     *
     * @return void
     */
    public function setMaxSpeed($maxSpeed)
    {
        $this->maxSpeed = $maxSpeed;
    }
    
    /**
     * Returns the weight.
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    /**
     * Sets the weight.
     *
     * @param string $weight
     *
     * @return void
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
    
    /**
     * Returns the brakes.
     *
     * @return string
     */
    public function getBrakes()
    {
        return $this->brakes;
    }
    
    /**
     * Sets the brakes.
     *
     * @param string $brakes
     *
     * @return void
     */
    public function setBrakes($brakes)
    {
        $this->brakes = $brakes;
    }
    
    /**
     * Returns the gearbox.
     *
     * @return string
     */
    public function getGearbox()
    {
        return $this->gearbox;
    }
    
    /**
     * Sets the gearbox.
     *
     * @param string $gearbox
     *
     * @return void
     */
    public function setGearbox($gearbox)
    {
        $this->gearbox = $gearbox;
    }
    
    /**
     * Returns the rim.
     *
     * @return string
     */
    public function getRim()
    {
        return $this->rim;
    }
    
    /**
     * Sets the rim.
     *
     * @param string $rim
     *
     * @return void
     */
    public function setRim($rim)
    {
        $this->rim = $rim;
    }
    
    /**
     * Returns the tire.
     *
     * @return string
     */
    public function getTire()
    {
        return $this->tire;
    }
    
    /**
     * Sets the tire.
     *
     * @param string $tire
     *
     * @return void
     */
    public function setTire($tire)
    {
        $this->tire = $tire;
    }
    
    /**
     * Returns the interior.
     *
     * @return string
     */
    public function getInterior()
    {
        return $this->interior;
    }
    
    /**
     * Sets the interior.
     *
     * @param string $interior
     *
     * @return void
     */
    public function setInterior($interior)
    {
        $this->interior = $interior;
    }
    
    /**
     * Returns the info field 1.
     *
     * @return string
     */
    public function getInfoField1()
    {
        return $this->infoField1;
    }
    
    /**
     * Sets the info field 1.
     *
     * @param string $infoField1
     *
     * @return void
     */
    public function setInfoField1($infoField1)
    {
        $this->infoField1 = $infoField1;
    }
    
    /**
     * Returns the info field 2.
     *
     * @return string
     */
    public function getInfoField2()
    {
        return $this->infoField2;
    }
    
    /**
     * Sets the info field 2.
     *
     * @param string $infoField2
     *
     * @return void
     */
    public function setInfoField2($infoField2)
    {
        $this->infoField2 = $infoField2;
    }
    
    /**
     * Returns the info field 3.
     *
     * @return string
     */
    public function getInfoField3()
    {
        return $this->infoField3;
    }
    
    /**
     * Sets the info field 3.
     *
     * @param string $infoField3
     *
     * @return void
     */
    public function setInfoField3($infoField3)
    {
        $this->infoField3 = $infoField3;
    }
    
    /**
     * Returns the owner.
     *
     * @return integer
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * Sets the owner.
     *
     * @param integer $owner
     *
     * @return void
     */
    public function setOwner($owner)
    {
        $this->owner = intval($owner);
    }
    
    /**
     * Returns the show vehicle owner.
     *
     * @return boolean
     */
    public function getShowVehicleOwner()
    {
        return $this->showVehicleOwner;
    }
    
    /**
     * Sets the show vehicle owner.
     *
     * @param boolean $showVehicleOwner
     *
     * @return void
     */
    public function setShowVehicleOwner($showVehicleOwner)
    {
        if ($showVehicleOwner !== $this->showVehicleOwner) {
            $this->showVehicleOwner = (bool)$showVehicleOwner;
        }
    }
    
    /**
     * Returns the title text color.
     *
     * @return string
     */
    public function getTitleTextColor()
    {
        return $this->titleTextColor;
    }
    
    /**
     * Sets the title text color.
     *
     * @param string $titleTextColor
     *
     * @return void
     */
    public function setTitleTextColor($titleTextColor)
    {
        $this->titleTextColor = isset($titleTextColor) ? $titleTextColor : '';
    }
    
    
    /**
     * Returns the vehicle images.
     *
     * @return \RK\ParkHausModule\Entity\VehicleImageEntity[]
     */
    public function getVehicleImages()
    {
        return $this->vehicleImages;
    }
    
    /**
     * Sets the vehicle images.
     *
     * @param \RK\ParkHausModule\Entity\VehicleImageEntity[] $vehicleImages
     *
     * @return void
     */
    public function setVehicleImages($vehicleImages)
    {
        foreach ($vehicleImages as $vehicleImageSingle) {
            $this->addVehicleImages($vehicleImageSingle);
        }
    }
    
    /**
     * Adds an instance of \RK\ParkHausModule\Entity\VehicleImageEntity to the list of vehicle images.
     *
     * @param \RK\ParkHausModule\Entity\VehicleImageEntity $vehicleImage The instance to be added to the collection
     *
     * @return void
     */
    public function addVehicleImages(\RK\ParkHausModule\Entity\VehicleImageEntity $vehicleImage)
    {
        $this->vehicleImages->add($vehicleImage);
        $vehicleImage->setVehicle($this);
    }
    
    /**
     * Removes an instance of \RK\ParkHausModule\Entity\VehicleImageEntity from the list of vehicle images.
     *
     * @param \RK\ParkHausModule\Entity\VehicleImageEntity $vehicleImage The instance to be removed from the collection
     *
     * @return void
     */
    public function removeVehicleImages(\RK\ParkHausModule\Entity\VehicleImageEntity $vehicleImage)
    {
        $this->vehicleImages->removeElement($vehicleImage);
        $vehicleImage->setVehicle(null);
    }
    
    
    
    /**
     * Returns the formatted title conforming to the display pattern
     * specified for this entity.
     *
     * @return string The display title
     */
    public function getTitleFromDisplayPattern()
    {
        $serviceManager = ServiceUtil::getManager();
        $listHelper = $serviceManager->get('rk_parkhaus_module.listentries_helper');
    
        $formattedTitle = ''
                . $this->getManufacturer()
                . ' '
                . $this->getModel()
                . ' '
                . $this->getBuilt();
    
        return $formattedTitle;
    }
    
    
    /**
     * Returns a list of possible choices for the workflowState list field.
     * This method is used for validation.
     *
     * @return array List of allowed choices
     */
    public static function getWorkflowStateAllowedValues()
    {
        $serviceManager = ServiceUtil::getManager();
        $helper = $serviceManager->get('rk_parkhaus_module.listentries_helper');
        $listEntries = $helper->getWorkflowStateEntriesForVehicle();
    
        $allowedValues = ['initial'];
        foreach ($listEntries as $entry) {
            $allowedValues[] = $entry['value'];
        }
    
        return $allowedValues;
    }
    
    /**
     * Returns a list of possible choices for the vehicleType list field.
     * This method is used for validation.
     *
     * @return array List of allowed choices
     */
    public static function getVehicleTypeAllowedValues()
    {
        $serviceManager = ServiceUtil::getManager();
        $helper = $serviceManager->get('rk_parkhaus_module.listentries_helper');
        $listEntries = $helper->getVehicleTypeEntriesForVehicle();
    
        $allowedValues = [];
        foreach ($listEntries as $entry) {
            $allowedValues[] = $entry['value'];
        }
    
        return $allowedValues;
    }
    
    /**
     * Checks whether the owner field contains a valid user id.
     * This method is used for validation.
     *
     * @Assert\IsTrue(message="This value must be a valid user id.")
     *
     * @return boolean True if data is valid else false
     */
    public function isOwnerUserValid()
    {
        if ($this['owner'] < 1) {
            return true;
        }
    
        $uname = UserUtil::getVar('uname', $this['owner']);
    
        return (!is_null($uname) && !empty($uname));
    }
    
    /**
     * Sets/retrieves the workflow details.
     *
     * @param boolean $forceLoading load the workflow record
     *
     * @throws RuntimeException Thrown if retrieving the workflow object fails
     */
    public function initWorkflow($forceLoading = false)
    {
        $currentFunc = FormUtil::getPassedValue('func', 'index', 'GETPOST', FILTER_SANITIZE_STRING);
        $isReuse = FormUtil::getPassedValue('astemplate', '', 'GETPOST', FILTER_SANITIZE_STRING);
    
        // apply workflow with most important information
        $idColumn = 'id';
        
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = $serviceManager->get('rk_parkhaus_module.workflow_helper');
        
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = [
            'module' => 'RKParkHausModule',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => $idColumn,
            'obj_id' => $this[$idColumn],
            'schemaname' => $schemaName
        ];
        
        // load the real workflow only when required (e. g. when func is edit or delete)
        if ((!in_array($currentFunc, ['index', 'view', 'display']) && empty($isReuse)) || $forceLoading) {
            $result = Zikula_Workflow_Util::getWorkflowForObject($this, $this['_objectType'], $idColumn, 'RKParkHausModule');
            if (!$result) {
                $flashBag = $serviceManager->get('session')->getFlashBag();
                $flashBag->add('error', $serviceManager->get('translator.default')->__('Error! Could not load the associated workflow.'));
            }
        }
        
        if (!is_object($this['__WORKFLOW__']) && !isset($this['__WORKFLOW__']['schemaname'])) {
            $workflow = $this['__WORKFLOW__'];
            $workflow['schemaname'] = $schemaName;
            $this['__WORKFLOW__'] = $workflow;
        }
    }
    
    /**
     * Resets workflow data back to initial state.
     * To be used after cloning an entity object.
     */
    public function resetWorkflow()
    {
        $this->setWorkflowState('initial');
    
        $serviceManager = ServiceUtil::getManager();
        $workflowHelper = $serviceManager->get('rk_parkhaus_module.workflow_helper');
    
        $schemaName = $workflowHelper->getWorkflowName($this['_objectType']);
        $this['__WORKFLOW__'] = [
            'module' => 'RKParkHausModule',
            'state' => $this['workflowState'],
            'obj_table' => $this['_objectType'],
            'obj_idcolumn' => 'id',
            'obj_id' => 0,
            'schemaname' => $schemaName
        ];
    }
    
    /**
     * Start validation and raise exception if invalid data is found.
     *
     * @return boolean Whether everything is valid or not
     */
    public function validate()
    {
        if (true === $this->_bypassValidation) {
            return true;
        }
    
        $serviceManager = ServiceUtil::getManager();
    
        $validator = $serviceManager->get('validator');
        $errors = $validator->validate($this);
    
        if (count($errors) > 0) {
            $flashBag = $serviceManager->get('session')->getFlashBag();
            foreach ($errors as $error) {
                $flashBag->add('error', $error->getMessage());
            }
    
            return false;
        }
    
        return true;
    }
    
    /**
     * Return entity data in JSON format.
     *
     * @return string JSON-encoded data
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array The resulting arguments list
     */
    public function createUrlArgs()
    {
        $args = [];
    
        $args['id'] = $this['id'];
    
        if (property_exists($this, 'slug')) {
            $args['slug'] = $this['slug'];
        }
    
        return $args;
    }
    
    /**
     * Create concatenated identifier string (for composite keys).
     *
     * @return String concatenated identifiers
     */
    public function createCompositeIdentifier()
    {
        $itemId = $this['id'];
    
        return $itemId;
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'rkparkhausmodule.ui_hooks.vehicles';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects The objects are added to this array. Default: []
     * 
     * @return array of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = []) 
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Vehicle ' . $this->createCompositeIdentifier();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // If the entity has an identity, proceed as normal.
        if ($this->id) {
            // unset identifiers
            $this->setId(0);
    
            // reset Workflow
            $this->resetWorkflow();
    
            // reset upload fields
            $this->setTitleImage('');
            $this->setTitleImageMeta([]);
            $this->setVehicleImage('');
            $this->setVehicleImageMeta([]);
    
            $this->setCreatedDate(null);
            $this->setCreatedUserId(null);
            $this->setUpdatedDate(null);
            $this->setUpdatedUserId(null);
    
        }
        // otherwise do nothing, do NOT throw an exception!
    }
}
