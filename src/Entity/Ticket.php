<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @Entity 
 * @Table(name="ticket")
 */
class Ticket
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
     * @Column(name="baslik", type="text")
     */
    private $baslik;

    /**
     * @var text
     *
     * @Column(name="icerik", type="text")
     */
    private $icerik;

    /**
     * @var text
     *
     * @Column(name="filepath", type="text")
     */
    private $filepath;    

    /**
     * @var integer
     *
     * @Column(name="durum", type="integer", length=11)
     */
    private $durum;

    /**
     * @var string
     *
     * @Column(name="onem", type="string", length=255)
     */
    private $onem;

    /**
     * @var string
     *
     * @Column(name="kategori", type="string", length=255)
     */
    private $kategori;
    
    /**
     * @var datetime
     *
     * @Column(name="datetime", type="datetime")
     */
    private $datetime;

    /**
     * @var integer
     *
     * @ManyToOne(targetEntity="TicketCevap")
     * @JoinColumn(name="ticketcevap_id", referencedColumnName="id")
     */
    private $ticketcevap;



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
     * Set baslik
     *
     * @param string $baslik
     *
     * @return Ticket
     */
    public function setBaslik($baslik)
    {
        $this->baslik = $baslik;

        return $this;
    }

    /**
     * Get baslik
     *
     * @return string
     */
    public function getBaslik()
    {
        return $this->baslik;
    }

    /**
     * Set durum
     *
     * @param integer $durum
     *
     * @return Ticket
     */
    public function setDurum($durum)
    {
        $this->durum = $durum;

        return $this;
    }

    /**
     * Get durum
     *
     * @return integer
     */
    public function getDurum()
    {
        return $this->durum;
    }

    /**
     * Set onem
     *
     * @param string $onem
     *
     * @return Ticket
     */
    public function setOnem($onem)
    {
        $this->onem = $onem;

        return $this;
    }

    /**
     * Get onem
     *
     * @return string
     */
    public function getOnem()
    {
        return $this->onem;
    }

    /**
     * Set kategori
     *
     * @param string $kategori
     *
     * @return Ticket
     */
    public function setKategori($kategori)
    {
        $this->kategori = $kategori;

        return $this;
    }

    /**
     * Get kategori
     *
     * @return string
     */
    public function getKategori()
    {
        return $this->kategori;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return Ticket
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

    /**
     * Set ticketcevap
     *
     * @param \Entity\TicketCevap $ticketcevap
     *
     * @return Ticket
     */
    public function setTicketcevap(\Entity\TicketCevap $ticketcevap = null)
    {
        $this->ticketcevap = $ticketcevap;

        return $this;
    }

    /**
     * Get ticketcevap
     *
     * @return \Entity\TicketCevap
     */
    public function getTicketcevap()
    {
        return $this->ticketcevap;
    }

    /**
     * Set icerik
     *
     * @param string $icerik
     *
     * @return Ticket
     */
    public function setIcerik($icerik)
    {
        $this->icerik = $icerik;

        return $this;
    }

    /**
     * Get icerik
     *
     * @return string
     */
    public function getIcerik()
    {
        return $this->icerik;
    }

    /**
     * Set filepath
     *
     * @param string $filepath
     *
     * @return Ticket
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * Get filepath
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }
}
