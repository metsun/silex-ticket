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
     * @var text 
     *
     * @Column(name="isim", type="text")
     */
    private $isim;

    /**
     * Many Kategoris have Many Tickets.
     * @ManyToMany(targetEntity="Ticket", mappedBy="kategori")
     */
    private $holder;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->holder = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Add holder
     *
     * @param \Entity\Ticket $holder
     *
     * @return Kategori
     */
    public function addHolder(\Entity\Ticket $holder)
    {
        $this->holder[] = $holder;

        return $this;
    }

    /**
     * Remove holder
     *
     * @param \Entity\Ticket $holder
     */
    public function removeHolder(\Entity\Ticket $holder)
    {
        $this->holder->removeElement($holder);
    }

    /**
     * Get holder
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHolder()
    {
        return $this->holder;
    }
}
