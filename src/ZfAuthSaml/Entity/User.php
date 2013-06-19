<?php

namespace ZfAuthSaml\Entity;

use ZfcUser\Entity\UserInterface;

class User implements UserInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $displayName;
    
    /**
     * @var string
     */
    protected $firstName;
    
    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $state;
    
    /**
     * @var string[]
     */
    protected $groups;
    
    /**
     * @var string[]
     */
    protected $rawIdentity;
    
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     * @return UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     * @return UserInterface
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
    
    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     * @return UserInterface
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     * @return UserInterface
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     * @return UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $state
     * @return UserInterface
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }
    
    /**
     * Get groups
     * 
     * @return string
     */
    public function getGroups()
    {
        if(is_array($this->groups)){
            $groups = implode(',', $this->groups);
        } else {
            $groups = $this->groups;
        }
        
        return $groups;
    }
    
    /**
     * Set groups
     * 
     * @param string $groups
     * @return UserInterface
     */
    public function setGroups($groups)
    {
        $this->groups = is_array($groups) ? implode(',',$groups) : $groups;
        return $this;
    }
    
    /**
     * Get ramIdentity
     * 
     * @return string[]
     */
    public function getRawIdentity()
    {
        if(is_array($this->rawIdentity)){
            return serialize($this->rawIdentity);
        }
        return $this->rawIdentity;
    }
    
    /**
     * Set ramIdentity
     * 
     * @param string[] $rawIdentity
     * @return string[]
     */
    public function setRawIdentity($rawIdentity)
    {
        $this->rawIdentity = $rawIdentity;
        return $this;
    }
}