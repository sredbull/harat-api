<?php
// src/Entity/User.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMSSerializer;
use LdapTools\Bundle\LdapToolsBundle\Security\User\LdapUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 *
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 *
 * @JMSSerializer\ExclusionPolicy("all")
 */
class User extends BaseUser implements LdapUserInterface
{
    /**
     * The id of the user.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     */
    protected $id;

    /**
     * The ldap id of the user.
     *
     * @ORM\Column(type="string", length=100)
     */
    private $ldapGuid;


    /**
     * The name of the user.
     *
     * @Assert\NotBlank()
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    protected $username;

    /**
     * The email of the user.
     *
     * @Assert\Email(
     *     strict = true,
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true,
     *     checkHost = true
     * )
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    protected $email;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set ldapGuid.
     *
     * @param string $ldapGuid The ldap id of the user.
     *
     * @return User
     */
    public function setLdapGuid($ldapGuid) : User
    {
        $this->ldapGuid = $ldapGuid;

        return $this;
    }

    /**
     * Get ldapGuid.
     *
     * @return string
     */
    public function getLdapGuid() : string
    {
        return $this->ldapGuid;
    }
}