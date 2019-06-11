<?php

namespace App\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @author dev1 -> Ore Richard
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application", inversedBy="clients")
     */
    private $application;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}
