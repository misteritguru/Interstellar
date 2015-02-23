<?php

namespace CRC\Interstellar;

abstract class Ship
{
    public static $maxComponentVelocity = 1;
    public static $maxCargo             = 5;

    // private so that the entrant can't access these directly and cheat
    private $currentX;
    private $currentY;
    private $currentZ;
    private $universe;
    private $home;
    private $cargo   = 0;
    private $balance = 0;
    
    // information about the ship's programmer
    protected $authorName  = null;
    protected $authorEmail = null;

    public abstract function navigate();

    final public function __construct($x, $y, $z, Universe $universe, Planet $home)
    {
        $this->currentX = $x;
        $this->currentY = $y;
        $this->currentZ = $z;
        $this->universe = $universe;
        $this->home     = $home;
        $this->init();
    }

    final public function getX()
    {
        return $this->currentX;
    }

    final public function getY()
    {
        return $this->currentY;
    }
    
    final public function getZ()
    {
        return $this->currentZ;
    }
    
    final public function getCargo()
    {
        return $this->cargo;
    }
    
    final public function getBalance()
    {
        return $this->balance;
    }

    final private function getUniverse()
    {
        return $this->universe;
    }

    final private function updateUniverse()
    {
        // update the universe
        $newLocation = $this->getUniverse()->update();
        // it is possible we went through a wormhole and the universe moved us...
        $this->currentX = $newLocation['x'];
        $this->currentY = $newLocation['y'];
        $this->currentZ = $newLocation['z'];
    }

    // protected so that it can be called from the entrants implementation but final so that it can't be overridden
    final protected function move($dx, $dy, $dz)
    {
        $dx = floor($dx);
        $dy = floor($dy);
        $dz = floor($dz);
        // make sure that the pilot is not trying to move the ship faster than we allow...
        if (abs($dx) <= self::$maxComponentVelocity &&
            abs($dy) <= self::$maxComponentVelocity &&
            abs($dz) <= self::$maxComponentVelocity) {
                // update the location
                $this->currentX += $dx;
                $this->currentY += $dy;
                $this->currentZ += $dz;
                // update the universe (asteroids and 'did we go through a wormhole')
                $this->updateUniverse();
        } else {
            throw new Exception("Tried to move too fast! $dx, $dy, $dz - Speed limit for any one axis is " . self::$maxComponentVelocity . ".");
        }
    }

    final protected function scan()
    {
        return $this->getUniverse()->scan();
    }

    final protected function halt()
    {
        $this->move(0, 0, 0);
    }
    
    final public function mine(GasCloud $cloud)
    {
        if ($this->currentX == $cloud->getX() &&
            $this->currentY == $cloud->getY() &&
            $this->currentZ == $cloud->getZ()) {
                if ($this->cargo < self::$maxCargo) {
                    if (true === $cloud->mine()) {
                        $this->cargo++;
                        return true;
                    } else {
                        throw new Exception("That gas cloud is depleted.");
                    }
                } else {
                    return false;
                }
        } else {
            throw new Exception("The cloud is not at the same location as the ship.");
        }
    }
    
    final public function sellCargo()
    {
        if ($this->currentX == $this->home->getX() &&
            $this->currentY == $this->home->getY() &&
            $this->currentZ == $this->home->getZ()) {
                if ($this->cargo > 0) {
                    $this->balance += $this->cargo * 1000;
                    $this->cargo    = 0;
                    return true;
                } else {
                    return false;
                }
        } else {
            if ($this->cargo > 0) {
                $this->balance += $this->cargo * 500;
                $this->cargo    = 0;
                return true;
            } else {
                return false;
            }
        }
    }
}
