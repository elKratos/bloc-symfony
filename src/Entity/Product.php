<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="product_ibfk_1", columns={"type_flower"})})
 * @ORM\Entity
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="date", nullable=false)
     */
    private $publishedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url_img", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $urlImg = 'NULL';

    /**
     * @var \TypeFlower
     *
     * @ORM\ManyToOne(targetEntity="TypeFlower")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_flower", referencedColumnName="id")
     * })
     */
    private $typeFlower;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getUrlImg(): ?string
    {
        return $this->urlImg;
    }

    public function setUrlImg(?string $urlImg): self
    {
        $this->urlImg = $urlImg;

        return $this;
    }

    public function getTypeFlower(): ?TypeFlower
    {
        return $this->typeFlower;
    }

    public function setTypeFlower(?TypeFlower $typeFlower): self
    {
        $this->typeFlower = $typeFlower;

        return $this;
    }


}
