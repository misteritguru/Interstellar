<?php

namespace CRC\Interstellar;

final class Planet
{
    private $x;
    private $y;
    private $z;
    
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
}
