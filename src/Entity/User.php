<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"mail"},
 *     errorPath="mail",
 *     message="Ce mail est deja utilisé."
 * )
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "app_user_detail",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "list",
 *     href = @Hateoas\Route(
 *          "app_user_all",
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route(
 *          "app_user_post",
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *          "app_user_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Serializer\ExclusionPolicy("ALL")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
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
     * @Serializer\Expose
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
     *     message="Les chevrons dans le prénom ne sont pas autorisés!"
     * )
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le prénom d'un utilisateur ne peut pas contenir plus que {{ limit }} caractères !"
     * )
     * @Serializer\Expose
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
     * @Serializer\Expose
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
