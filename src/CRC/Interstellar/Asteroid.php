<?php

namespace CRC\Interstellar;

class Asteroid
{
    private $x;
    private $y;
    private $z;
    private $dx;
    private $dy;
    private $dz;

    public static $maxComponentVelocity = 2; // the fastest that an assteroid should be able to move in any one axis.
    
    public function __construct($x, $y, $z, $dx, $dy, $dz)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->dx = $dx;
        $this->dy = $dy;
        $this->dz = $dz;
    }

    public function update()
    {
        // sum the velocity to the current location to get the new location.
        $this->x += $this->dx;
        $this->y += $this->dy;
        $this->z += $this->dz;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getZ()
    {
        return $this->z;
    }

    public function getDx()
    {
        return $this->dx;
    }

    public function getDy()
    {
        return $this->dy;
    }

    public function getDz()
    {
        return $this->dz;
    }
}