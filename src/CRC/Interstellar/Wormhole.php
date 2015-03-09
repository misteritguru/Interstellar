<?php

namespace CRC\Interstellar;

class Wormhole
{
    private $x1;
    private $y1;
    private $z1;
    private $x2;
    private $y2;
    private $z2;

    public function __construct($x1, $y1, $z1, $x2, $y2, $z2)
    {
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->z1 = $z1;
        $this->x2 = $x2;
        $this->y2 = $y2;
        $this->z2 = $z2;
    }

    public function getX1()
    {    
        return $this->x1;
    }

    public function getY1()
    {
        return $this->y1;
    }

    public function getZ1()
    {
        return $this->z1;
    }

    public function getX2()
    {
        return $this->x2;
    }

    public function getY2()
    {
        return $this->y2;
    }

    public function getZ2()
    {
        return $this->z2;
    }
}