<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"mail"},
 *     errorPath="mail",
 *     message="Ce mail est deja utilisé."
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     * @Assert\Regex(
     *     pattern="#^[^<>]*$#",
     *     match=true,
     *     message="Les chevrons dans le nom ne sont pas autorisés!"
     * )
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom d'un utilisateur ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     * @Groups("user_read")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     * @Assert\Regex(
     *     pattern="#^[^<>]*$#",
     *     match=true,
     *     message="Les chevrons dans le prenom ne sont pas autorisés!"
     * )
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le prénom d'un utilisateur ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     * @Groups("user_read")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Assert\Email(
     *      message = "Veuillez entrer un email valide !"
     * )
     * * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     * @Assert\Length(
     *      max = 150,
     *      maxMessage = "L'email d'un utilisateur ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     * @Groups("user_read")
     */
    private $mail;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="customer", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customer;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
