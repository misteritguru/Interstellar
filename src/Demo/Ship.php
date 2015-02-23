<?php

namespace Demo;

class Ship extends \CRC\Interstellar\Ship
{
    protected $authorName  = "JoinCRC Team";
    protected $authorEmail = "php@joincrc.com";
    
    protected function init()
    {
        // put any initialisation code you need here, this is called by the constructor of the parent class for your convenience
    }

    // put your implementation in here.......
    public function navigate()
    {
        do {
            // scan whats out there...
            $scan = $this->scan();
            // head to the first gas cloud
            $cloud = reset($scan['gasclouds']);
            $x     = $cloud['x'];
            $y     = $cloud['y'];
            $z     = $cloud['z'];
            // if cargo is full, head home instead
            if ($this->getCargo() == self::$maxCargo) {
                $home = $scan['home'];
                $x    = $home['x'];
                $y    = $home['y'];
                $z    = $home['z'];
                echo "Heading to home planet at ($x, $y, $z).\r\n";
            } else {
                echo "Cargo is ".$this->getCargo()." units.";
                echo "Heading to gas cloud at ($x, $y, $z).\r\n";
            }
            // figure out which direction to move to the first gas cloud
            if ($this->getX() > $x) $dx = -1;
            if ($this->getX() < $x) $dx = 1;
            if ($this->getX() == $x) $dx = 0;
            if ($this->getY() > $y) $dy = -1;
            if ($this->getY() < $y) $dy = 1;
            if ($this->getY() == $y) $dy = 0;
            if ($this->getZ() > $z) $dz = -1;
            if ($this->getZ() < $z) $dz = 1;
            if ($this->getZ() == $z) $dz = 0;
            // fire the thrusters
            $this->move($dx, $dy, $dz);
            // count the asteroids
            echo count($scan['asteroids']) . " asteroid(s) nearby.\r\n";
            // ...but completely ignore them.
        } while (true);
    }
}

