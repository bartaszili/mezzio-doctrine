<?php

declare(strict_types=1);

namespace Api\Entity;

use Api\Entity\Pricelog;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="properties")
 **/
class Property
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $price;

    /**
     * @ORM\Column(type="string", length=3, nullable=false)
     */
    protected $currency;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $category;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    protected $country;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $county;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $district;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $town;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $street;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $house;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $floor;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $door;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $postcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $contact_email;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $origin_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $origin_image_path;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $origin_updated;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $origin_url;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $is_active;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $modified;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $archived;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $duplicates;

    /**
    * @ORM\OneToMany(targetEntity="Api\Entity\Pricelog", mappedBy="property", cascade={"persist"}, orphanRemoval=true)
    */
    protected $price_log;

    public function __construct()
    {
        $this->price_log = new ArrayCollection();
    }

    /**
     * @param bool $withPricelog
     * @return array|mixed
     */
    public function getProperty(bool $withPricelog=false): array
    {
        $return = [
            'id'                => $this->getId(),
            'name'              => $this->getName(),
            'description'       => $this->getDescription(),
            'price'             => $this->getPrice(),
            'currency'          => $this->getCurrency(),
            'type'              => $this->getType(),
            'category'          => $this->getCategory(),
            'latitude'          => $this->getLatitude(),
            'longitude'         => $this->getLongitude(),
            'country'           => $this->getCountry(),
            'county'            => $this->getCounty(),
            'district'          => $this->getDistrict(),
            'town'              => $this->getTown(),
            'street'            => $this->getStreet(),
            'house'             => $this->getHouse(),
            'floor'             => $this->getFloor(),
            'door'              => $this->getDoor(),
            'postcode'          => $this->getPostcode(),
            'contact_name'      => $this->getContactName(),
            'contact_phone'     => $this->getContactPhone(),
            'contact_email'     => $this->getContactEmail(),
            'origin_id'         => $this->getOriginId(),
            'origin_image_path' => $this->getOriginImagePath(),
            'origin_updated'    => $this->getOriginUpdated()->format('Y-m-d H:i:s'),
            'origin_url'        => $this->getOriginUrl(),
            'is_active'         => $this->getIsActive(),
            'created'           => $this->getCreated()->format('Y-m-d H:i:s'),
            'modified'          => $this->getModified()->format('Y-m-d H:i:s'),
            'archived'          => ($this->getArchived()) ? $this->getArchived()->format('Y-m-d H:i:s') : $this->getArchived(),
            'duplicates'        => $this->getDuplicates()
        ];

        if ($withPricelog && (count($this->price_log) > 0))
        {
            $return['price_log'] = $this->withPricelog($withPricelog);
        }

        return $return;
    }

    /**
     * @param array $requestBody
     * @throws \Exception
     */
    public function setProperty(array $requestBody): void
    {
        if (isset($requestBody['name']) && !empty($requestBody['name'])){ $this->setName($requestBody['name']); }
        if (isset($requestBody['description']) && !empty($requestBody['description'])){ $this->setDescription($requestBody['description']); }
        if (isset($requestBody['price']) && !empty($requestBody['price']))
        {
            if ($requestBody['price'] != (float) $this->price) {
                $this->setPriceLog($requestBody['price']);
            }
            $this->setPrice($requestBody['price']);
        }
        if (isset($requestBody['currency']) && !empty($requestBody['currency'])){ $this->setCurrency($requestBody['currency']); }
        if (isset($requestBody['type']) && !empty($requestBody['type'])){ $this->setType($requestBody['type']); }
        if (isset($requestBody['category']) && !empty($requestBody['category'])){ $this->setCategory($requestBody['category']); }
        if (isset($requestBody['latitude']) && !empty($requestBody['latitude'])){ $this->setLatitude($requestBody['latitude']); }
        if (isset($requestBody['longitude']) && !empty($requestBody['longitude'])){ $this->setLongitude($requestBody['longitude']); }
        if (isset($requestBody['country']) && !empty($requestBody['country'])){ $this->setCountry($requestBody['country']); }
        if (isset($requestBody['county']) && !empty($requestBody['county'])){ $this->setCounty($requestBody['county']); }
        if (isset($requestBody['district']) && !empty($requestBody['district'])){ $this->setDistrict($requestBody['district']); }
        if (isset($requestBody['town']) && !empty($requestBody['town'])){ $this->setTown($requestBody['town']); }
        if (isset($requestBody['street']) && !empty($requestBody['street'])){ $this->setStreet($requestBody['street']); }
        if (isset($requestBody['house']) && !empty($requestBody['house'])){ $this->setHouse($requestBody['house']); }
        if (isset($requestBody['floor']) && !empty($requestBody['floor'])){ $this->setFloor($requestBody['floor']); }
        if (isset($requestBody['door']) && !empty($requestBody['door'])){ $this->setDoor($requestBody['door']); }
        if (isset($requestBody['postcode']) && !empty($requestBody['postcode'])){ $this->setPostcode($requestBody['postcode']); }
        if (isset($requestBody['contact_name']) && !empty($requestBody['contact_name'])){ $this->setContactName($requestBody['contact_name']); }
        if (isset($requestBody['contact_phone']) && !empty($requestBody['contact_phone'])){ $this->setContactPhone($requestBody['contact_phone']); }
        if (isset($requestBody['contact_email']) && !empty($requestBody['contact_email'])){ $this->setContactEmail($requestBody['contact_email']); }
        if (isset($requestBody['origin_id']) && !empty($requestBody['origin_id'])){ $this->setOriginId($requestBody['origin_id']); }
        if (isset($requestBody['origin_image_path']) && !empty($requestBody['origin_image_path'])){ $this->setOriginImagePath($requestBody['origin_image_path']); }
        if (isset($requestBody['origin_updated']) && !empty($requestBody['origin_updated'])){ $this->setOriginUpdated(new DateTime($requestBody['origin_updated'])); }
        if (isset($requestBody['origin_url']) && !empty($requestBody['origin_url'])){ $this->setOriginUrl($requestBody['origin_url']); }
        (isset($requestBody['is_active'])) ? $this->setIsActive($requestBody['is_active']) : $this->setIsActive(true);
        if (isset($requestBody['duplicates']) && !empty($requestBody['duplicates'])){ $this->setDuplicates($requestBody['duplicates']); }
    }

    /**
     * Insert new record into pricelog
     */
    public function setPriceLog(float $price)
    {
        $pricelogObj = new Pricelog();
        $pricelogObj->setPrice($price);
        $pricelogObj->setCreated(new DateTime("now"));
        $pricelogObj->setProperty($this);
        $this->price_log->add($pricelogObj);
    }

    /**
     * @param bool $withPricelog
     * @return mixed|array
     */
    public function withPricelog(bool $withPricelog): array
    {
        if ($withPricelog) {
            return $this->getPriceLog()->map(function(Pricelog $pricelog) {
                $pricelog->resetProperty();
                return $pricelog->getPricelog(false);
            })->toArray();
        }

        return ['---'];
    }

    /**
     * @return array|mixed
     */
    public function getPriceLog()
    {
        return $this->price_log;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
    * @return string
    */
    public function getType(): string
    {
        return $this->type;
    }

    /**
    * @param string $type
    */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
    * @return string
    */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
    * @param string $category
    */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
    * @return string
    */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
    * @param string $country
    */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
    * @return string
    */
    public function getCounty(): string
    {
        return $this->county;
    }

    /**
    * @param string $county
    */
    public function setCounty(string $county): void
    {
        $this->county = $county;
    }

    /**
    * @return string
    */
    public function getDistrict(): string
    {
        return $this->district;
    }

    /**
    * @param string $district
    */
    public function setDistrict(string $district): void
    {
        $this->district = $district;
    }

    /**
    * @return string
    */
    public function getTown(): string
    {
        return $this->town;
    }

    /**
    * @param string $town
    */
    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    /**
    * @return string|null
    */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
    * @param string $street
    */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
    * @return string|null
    */
    public function getHouse(): ?string
    {
        return $this->house;
    }

    /**
    * @param string $house
    */
    public function setHouse(string $house): void
    {
        $this->house = $house;
    }

    /**
    * @return string|null
    */
    public function getFloor(): ?string
    {
        return $this->floor;
    }

    /**
    * @param string $floor
    */
    public function setFloor(string $floor): void
    {
        $this->floor = $floor;
    }

    /**
    * @return string|null
    */
    public function getDoor(): ?string
    {
        return $this->door;
    }

    /**
    * @param string $door
    */
    public function setDoor(string $door): void
    {
        $this->door = $door;
    }

    /**
    * @return string|null
    */
    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
    * @param string $postcode
    */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
    * @return string|null
    */
    public function getContactName(): ?string
    {
        return $this->contact_name;
    }

    /**
    * @param string $contact_name
    */
    public function setContactName(string $contact_name): void
    {
        $this->contact_name = $contact_name;
    }

    /**
    * @return string|null
    */
    public function getContactPhone(): ?string
    {
        return $this->contact_phone;
    }

    /**
    * @param string $contact_phone
    */
    public function setContactPhone(string $contact_phone): void
    {
        $this->contact_phone = $contact_phone;
    }

    /**
    * @return string|null
    */
    public function getContactEmail(): ?string
    {
        return $this->contact_email;
    }

    /**
    * @param string $contact_email
    */
    public function setContactEmail(string $contact_email): void
    {
        $this->contact_email = $contact_email;
    }

    /**
    * @return string
    */
    public function getOriginId(): string
    {
        return $this->origin_id;
    }

    /**
    * @param string $origin_id
    */
    public function setOriginId(string $origin_id): void
    {
        $this->origin_id = $origin_id;
    }

    /**
    * @return string|null
    */
    public function getOriginImagePath(): ?string
    {
        return $this->origin_image_path;
    }

    /**
    * @param string $origin_image_path
    */
    public function setOriginImagePath(string $origin_image_path): void
    {
        $this->origin_image_path = $origin_image_path;
    }

    /**
    * @return DateTime
    */
    public function getOriginUpdated(): DateTime
    {
        return $this->origin_updated;
    }

    /**
    * @param DateTime $origin_updated
    */
    public function setOriginUpdated(DateTime $origin_updated): void
    {
        $this->origin_updated = $origin_updated;
    }

    /**
    * @return string
    */
    public function getOriginUrl(): string
    {
        return $this->origin_url;
    }

    /**
    * @param string $origin_url
    */
    public function setOriginUrl(string $origin_url): void
    {
        $this->origin_url = $origin_url;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @param bool $is_active
     */
    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     * @throws \Exception
     */
    public function setCreated(DateTime $created = null): void
    {
        if (!$created && empty($this->getId())) {
            $this->created = new DateTime("now");
        } else {
            $this->created = $created;
        }
    }

    /**
     * @return DateTime
     */
    public function getModified(): DateTime
    {
        return $this->modified;
    }

    /**
     * @param DateTime $modified
     * @throws \Exception
     */
    public function setModified(DateTime $modified = null): void
    {
        if (!$modified) {
            $this->modified = new DateTime("now");
        } else {
            $this->modified = $modified;
        }
    }

    /**
     * @return DateTime|null
     */
    public function getArchived(): ?DateTime
    {
        return $this->archived;
    }

    /**
     * @param DateTime $archived
     * @throws \Exception
     */
    public function setArchived(DateTime $archived = null): void
    {
        if (!$archived) {
            $this->archived = new DateTime("now");
        } else {
            $this->archived = $archived;
        }
    }

    /**
    * @return array
    */
    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    /**
    * @param array $duplicates
    */
    public function setDuplicates(array $duplicates): void
    {
        $this->duplicates = $duplicates;
    }
}
