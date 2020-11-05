<?php

declare(strict_types=1);

namespace Api\Entity;

use Api\Entity\Property;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pricelog")
 **/
class Pricelog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="guid", nullable=false)
     */
    protected $property_id;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $price;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $created;

    /**
    * @ORM\ManyToOne(targetEntity="Api\Entity\Property", inversedBy="price_log", cascade={"persist"})
    * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
    */
    protected $property;

    /**
     * @param bool $withProperty
     * @return array|mixed
     */
    public function getPricelog(bool $withProperty=false): array
    {
        $return = [
            'id'          => $this->getId(),
            'property_id' => $this->getPropertyId(),
            'price'       => $this->getPrice(),
            'created'     => $this->getCreated()->format('Y-m-d H:i:s')
        ];

        if ($withProperty)
        {
            $return['property'] = $this->getProperty();
        }

        return $return;
    }

    /**
     * @param array $requestBody
     * @throws \Exception
     */
    public function setPricelog(array $requestBody): void
    {
        if (isset($requestBody['property_id']) && !empty($requestBody['property_id'])){ $this->setPropertyId($requestBody['property_id']); }
        if (isset($requestBody['price']) && !empty($requestBody['price'])){ $this->setPrice($requestBody['price']); }
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
    public function getPropertyId(): string
    {
        return $this->property_id;
    }

    /**
     * @param string $property_id
     */
    public function setPropertyId(string $property_id): void
    {
        $this->property_id = $property_id;
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
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @throws \Exception
     */
    public function setCreated(\DateTime $created = null): void
    {
        if (!$created && empty($this->getId())) {
            $this->created = new \DateTime("now");
        } else {
            $this->created = $created;
        }
    }

    /**
     * @return array|mixed
     */
    public function getProperty()
    {
        return $this->property->getProperty();
    }

    /**
     * @param array|mixed $property
     */
    public function setProperty(Property $property): void
    {
        $this->property = $property;
    }

    /**
     * Removes Property ManyToOne relationship to prevent infinite recursion
     */
    public function resetProperty(): void
    {
        $this->property = ['reset'];
    }
}
