<?php
// src/Entity/User.php
namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 *
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends BaseUser
{
    /**
     * The id of the user.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The name of the user.
     *
     * @Assert\NotBlank()
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
     */
    protected $email;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}