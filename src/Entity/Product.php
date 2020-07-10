<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="product_ibfk_1", columns={"type_flower"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTimeInterface $publishedAt
     * @return $this
     */
    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlImg(): ?string
    {
        return $this->urlImg;
    }

    /**
     * @param string|null $urlImg
     * @return $this
     */
    public function setUrlImg(?string $urlImg): self
    {
        $this->urlImg = $urlImg;

        return $this;
    }

    /**
     * @return TypeFlower|null
     */
    public function getTypeFlower(): ?TypeFlower
    {
        return $this->typeFlower;
    }

    /**
     * @param TypeFlower|null $typeFlower
     * @return $this
     */
    public function setTypeFlower(?TypeFlower $typeFlower): self
    {
        $this->typeFlower = $typeFlower;

        return $this;
    }


}
