<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kategori
 *
 * @Entity
 * @Table(name="kategori")
 */
class Kategori
{
	/**
     * @var integer
     * 
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * 
     * @Column(name="isim", type="string", length=255)
     */
    private $isim;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isim
     *
     * @param string $isim
     *
     * @return Kategori
     */
    public function setIsim($isim)
    {
        $this->isim = $isim;

        return $this;
    }

    /**
     * Get isim
     *
     * @return string
     */
    public function getIsim()
    {
        return $this->isim;
    }
}
