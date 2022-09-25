<?php

namespace App\Entity;

use App\Repository\Product as RepositoryProduct;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Cocur\Slugify\Slugify;
use JsonSerializable;
use stdClass;

#[Entity(repositoryClass: RepositoryProduct::class), Table(name: 'products')]
final class Product implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $name;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $slug;

    #[Column(name: 'image_url', type: 'string', nullable: true)]
    private ?string $imageUrl;

    #[Column(type: 'integer', nullable: false)]
    private int $price = 0;

    #[Column(type: 'integer', nullable: false)]
    private int $stock = 0;

    #[Column(name: 'created_at', type: 'datetimetz', nullable: false)]
    private DateTime $createdAt;

    #[Column(name: 'updated_at', type: 'datetimetz', nullable: false)]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * Create a new product from request.
     *
     * @param array|stdClass $requestBody
     * @return self
     */
    public static function createFromRequest(array|stdClass $requestBody): self
    {
        return (new self())->fillDataFromRequest($requestBody);
    }

    /**
     * Fill data from request
     *
     * @param array|stdClass $requestBody
     * @return self
     */
    public function fillDataFromRequest(array|stdClass $requestBody): self
    {
        if (is_array($requestBody)) {
            $requestBody = (object) $requestBody;
        }

        if (isset($requestBody->name)) {
            $this->setName($requestBody->name);
        }

        if (isset($requestBody->price)) {
            $this->setPrice($requestBody->price);
        }

        if (isset($requestBody->stock)) {
            $this->setStock($requestBody->stock);
        }

        if (isset($requestBody->image_url)) {
            $this->setImageUrl($requestBody->image_url);
        }

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "slug" => $this->getSlug(),
            "image_url" => $this->getImageUrl(),
            "price" => $this->getPrice(),
            "stock" => $this->getStock(),
            "updated_at" => $this->getUpdatedAt(),
            "created_at" => $this->getCreatedAt(),
        ];
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        $this->setSlug($name);

        return $this;
    }

    /**
     * Get the value of slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */
    public function setSlug(string $slug): self
    {
        $slug = (new Slugify())->slugify($slug);

        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of price
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of stock
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     *
     * @return  self
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get the value of imageUrl
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * Set the value of imageUrl
     *
     * @return  self
     */
    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
