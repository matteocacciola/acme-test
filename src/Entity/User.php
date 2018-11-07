<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface {
    
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_CASH_REGISTER = 'ROLE_CASH_REGISTER';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
     */
    protected $orders;
    
    /**
     * 
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password = '') {
        $this->update($password, $email);
        $this->orders = new ArrayCollection();
    }

    /**
     * 
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * 
     * @param array $roles
     * @return \self
     */
    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }
    
    /**
     * 
     * @param string $role
     * @return \self
     */
    public function addRole(string $role): self {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
        
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt() {
        
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    
    /**
     * 
     * @param string $password
     * @param string|null $email
     * @return $this
     */
    public function update(string $password, string $email = null): self {
        $this->password = $password;
        
        if ($email) {
            $this->email = $email;
        }
       
        return $this;
    }
    
    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders() {
        return $this->orders;
    }

}
