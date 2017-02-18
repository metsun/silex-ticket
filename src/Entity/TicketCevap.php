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
     * @var integer
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;

    /**
     * @var integer
     *
     * @ManyToOne(targetEntity="Ticket")
     * @JoinColumn(name="ticketcevap_id", referencedColumnName="id")
     */
    private $ticketcevap;

    /**
     * @var text
     *
     * @Column(name="cevap", type="text")
     */
    private $cevap;

    /**
     * @var string
     *
     * @Column(name="datetime", type="string")
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
     * Set userId
     *
     * @param \Entity\User $userId
     *
     * @return TicketCevap
     */
    public function setUserId(\Entity\User $userId = null)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \Entity\User
     */
    public function getUserId()
    {
        return $this->user_id;
    }


    /**
     * Set ticketcevap
     *
     * @param \Entity\Ticket $ticketcevap
     *
     * @return TicketCevap
     */
    public function setTicketcevap(\Entity\Ticket $ticketcevap = null)
    {
        $this->ticketcevap = $ticketcevap;

        return $this;
    }

    /**
     * Get ticketcevap
     *
     * @return \Entity\Ticket
     */
    public function getTicketcevap()
    {
        return $this->ticketcevap;
    }

    /**
     * Set datetime
     *
     * @param string $datetime
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
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }
}
