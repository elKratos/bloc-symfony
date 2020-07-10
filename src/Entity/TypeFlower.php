<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeFlower
 *
 * @ORM\Table(name="type_flower")
 * @ORM\Entity(repositoryClass="App\Repository\TypeFlowerRepository")
 */
class TypeFlower
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getName();
    }
}
