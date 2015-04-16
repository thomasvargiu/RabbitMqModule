<?php

namespace RabbitMqModule\Options;

use Zend\Stdlib\AbstractOptions;

class Queue extends AbstractOptions
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var boolean
     */
    protected $passive = false;
    /**
     * @var boolean
     */
    protected $durable = false;
    /**
     * @var boolean
     */
    protected $autoDelete = true;
    /**
     * @var boolean
     */
    protected $exclusive = false;
    /**
     * @var boolean
     */
    protected $noWait = false;
    /**
     * @var boolean
     */
    protected $declare = true;
    /**
     * @var array
     */
    protected $arguments = [];
    /**
     * @var int
     */
    protected $ticket = 0;
    /**
     * @var array
     */
    protected $routingKeys = [];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPassive()
    {
        return $this->passive;
    }

    /**
     * @param boolean $passive
     * @return $this
     */
    public function setPassive($passive)
    {
        $this->passive = $passive;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDurable()
    {
        return $this->durable;
    }

    /**
     * @param boolean $durable
     * @return $this
     */
    public function setDurable($durable)
    {
        $this->durable = $durable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoDelete()
    {
        return $this->autoDelete;
    }

    /**
     * @param boolean $autoDelete
     * @return $this
     */
    public function setAutoDelete($autoDelete)
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExclusive()
    {
        return $this->exclusive;
    }

    /**
     * @param boolean $exclusive
     * @return $this
     */
    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNoWait()
    {
        return $this->noWait;
    }

    /**
     * @param boolean $noWait
     * @return $this
     */
    public function setNoWait($noWait)
    {
        $this->noWait = $noWait;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeclare()
    {
        return $this->declare;
    }

    /**
     * @param boolean $declare
     * @return $this
     */
    public function setDeclare($declare)
    {
        $this->declare = $declare;
        return $this;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return int
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param int $ticket
     * @return $this
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoutingKeys()
    {
        return $this->routingKeys;
    }

    /**
     * @param array $routingKeys
     * @return $this
     */
    public function setRoutingKeys(array $routingKeys)
    {
        $this->routingKeys = $routingKeys;
        return $this;
    }
}
