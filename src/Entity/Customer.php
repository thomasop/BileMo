<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity(
 *     fields={"mail"},
 *     errorPath="mail",
 *     message="Ce mail est deja utilisé."
 * )
 */
class Customer implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom d'un client ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le prénom d'un client ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(
     *      message = "Ce champ email est requis !"
     * )
     * @Assert\Regex(
     *     pattern="#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$#",
     *     match=true,
     *     message="Mot de passe incorrect: Une lettre en majuscule, minuscule, un chiffre et caractère speciaux attendu ainsi que 8 caractères minimum!"
     * )
     * @Assert\Length(
     *      min = 8,
     *      max = 150,
     *      minMessage = "Votre mot de passe ne peut pas contenir moin que {{ limit }} caractères !",
     *      maxMessage = "Votre mot de passe ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Assert\Email(
     *      message = "Veuillez entrer un email valide !"
     * )
     * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     * @Assert\Length(
     *      max = 150,
     *      maxMessage = "L'email d'un client ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     */
    private $mail;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank(
     *      message = "Ce champ est requis !"
     * )
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="customer")
     */
    private $users;

    public function __construct()
    {
        $this->roles = ['ROLE_ADMIN'];
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return (string) $this->mail;
    }

    public function getSalt()
    {
        return null;
    }
}
