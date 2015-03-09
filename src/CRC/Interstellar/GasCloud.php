<?php

namespace CRC\Interstellar;

class GasCloud
{
    private $x;
    private $y;
    private $z;
    private $mined = false;
    
    public function __construct($x, $y, $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
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
    
    public function mine()
    {
        if (true === $this->mined) {
            return false;
        } else {
            $this->mined = true;
            return true;
        }
    }
}
