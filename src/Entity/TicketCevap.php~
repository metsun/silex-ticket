<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketCevap
 *
 * @Entity 
 * @Table(name="ticketcevap")
 */
class TicketCevap
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
     * @Column(name="cevap", type="text")
     */
    private $cevap;

    /**
     * @var datetime 
     *
     * @Column(name="datetime", type="datetime")
     */
    private $datetime;


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
     * Set cevap
     *
     * @param string $cevap
     *
     * @return TicketCevap
     */
    public function setCevap($cevap)
    {
        $this->cevap = $cevap;

        return $this;
    }

    /**
     * Get cevap
     *
     * @return string
     */
    public function getCevap()
    {
        return $this->cevap;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return TicketCevap
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }
}
