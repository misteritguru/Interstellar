<?php

namespace CRC\Interstellar;

class Universe
{
    // Things to change to make debugging easier...
    private $universeRadius     = 2000;  // make this smaller and you'll likely get a much faster run-time.
    private $asteroidsOff       = false; // turn off asteroids.
    private $asteroidDensity    = 350;   // NB: It's the reciporal, i.e., one asteroid per 350 3d grid cells.
    private $wormholeSpawnLimit = 50;    // the number of wormholes that will spawn in the universe
    private $gasCloudsLimit     = 100;   // the number of gas clouds to spawn
    private $universeRunTime    = 300;   // run time in seconds

    // change stuff below here and you'll probably break something.
    private $spaceShip = null;
    private $home      = null;
    private $startTime = null;
    private $asteroids = array();
    private $wormholes = array();
    private $gasClouds = array();
    
    private $asteroidScanningRange = 6;
    private $asteroidPreloadRange  = 10;
    private $asteroidUnloadRange   = 50;
    
    public function __construct($shipClass = "\Ship")
    {
        $homeX = rand(-$this->universeRadius, $this->universeRadius);
        $homeY = rand(-$this->universeRadius, $this->universeRadius);
        $homeZ = rand(-$this->universeRadius, $this->universeRadius);
        
        // decide where in the universe the ship is going to spawn
        if (false === class_exists($shipClass) || false === is_subclass_of($shipClass, '\CRC\Interstellar\Ship')) {
            throw new Exception('Your spaceship is not an instance of \CRC\Interstellar\Ship.');
        }
        
        // decide where we drop off at
        $this->home = new Planet($homeX, $homeY, $homeZ);
        // generate our ship
        $this->spaceShip = new $shipClass($homeX, $homeY, $homeZ, $this, $this->home);
        // generate the initial asteroids
        $this->generateAsteroids();
        // generate the wormholes
        $this->generateWormholes();
        // generate the gas clouds
        $this->generateGasClouds();
        // set the start time
        $this->startTime = gettimeofday(true);
        // navigate ship
        $this->spaceShip->navigate();
    }

    // call after moving the ship to update the other objects
    public function update()
    {
        // we have to return the new location of the ship if we jumped through a wormhole.
        // store the current loction so that we'll just return that if there was no wormhole
        $newX = $this->spaceShip->getX();
        $newY = $this->spaceShip->getY();
        $newZ = $this->spaceShip->getZ();
        // update the asteroids and check their effects
        foreach ($this->asteroids as $asteroid) {
            $asteroid->update();
            // was there a collision?
            if ($asteroid->getX() == $this->spaceShip->getX() &&
                $asteroid->getY() == $this->spaceShip->getY() &&
                $asteroid->getZ() == $this->spaceShip->getZ()) {
                echo "Spaceship collided with an asteroid at " . $this->spaceShip->getX() . ', ' . $this->spaceShip->getY() . ', ' . $this->spaceShip->getZ() . "!\r\n";
                exit();
            }
        }
        // check if we went through a wormhole...
        foreach ($this->wormholes as $wormhole) {
            // see if we're at either end of the wormhole...
            $end = 0;
            if ($this->spaceShip->getX() == $wormhole->getX1() &&
                $this->spaceShip->getY() == $wormhole->getY1() &&
                $this->spaceShip->getZ() == $wormhole->getZ1()) {
                $end = 1;
            }
            if ($this->spaceShip->getX() == $wormhole->getX2() &&
                $this->spaceShip->getY() == $wormhole->getY2() &&
                $this->spaceShip->getZ() == $wormhole->getZ2()) {
                $end = 2;
            }
            // if we are at the end of a wormhole, then jump to the other end...
            if ($end != 0) {
                if ($end == 1) {
                    $jumpX = $wormhole->getX2();
                    $jumpY = $wormhole->getY2();
                    $jumpZ = $wormhole->getZ2();
                } else {
                    $jumpX = $wormhole->getX1();
                    $jumpY = $wormhole->getY1();
                    $jumpZ = $wormhole->getZ1();
                }
                echo "Jumped through wormhole!\r\n";
                $newX = $jumpX;
                $newY = $jumpY;
                $newZ = $jumpZ;
            }
        }
        // check to see if we mined a gas cloud
        foreach ($this->gasClouds as $key => $cloud) {
            if ($this->spaceShip->getX() == $cloud->getX() &&
                $this->spaceShip->getY() == $cloud->getY() &&
                $this->spaceShip->getZ() == $cloud->getZ()) {
                    echo "Encountered gas cloud.\r\n";
                    if (true === $this->spaceShip->mine($cloud)) {
                        echo "Successfully mined the gas cloud. You have ".$this->spaceShip->getCargo()." units of gas in your cargo.\r\n";
                        unset($this->gasClouds[$key]);
                        $this->generateGasClouds();
                    } else {
                        echo "Your cargo is full, could not mine.\r\n";
                    }
            }
        }
        // check if we're within time
        if (gettimeofday(true) > $this->startTime + $this->universeRunTime) {
            echo "Time's up.\r\n";
            echo "Your ".$this->spaceShip->getCargo()." units of cargo were sold remotely for 500 credits each.\r\n";
            $this->spaceShip->sellCargo();
            echo "Your balance is ".$this->spaceShip->getBalance()." credits.";
            exit();
        }
        // check if we are at home
        if ($this->home->getX() == $this->spaceShip->getX() &&
            $this->home->getY() == $this->spaceShip->getY() &&
            $this->home->getZ() == $this->spaceShip->getZ()) {
                if (true === $this->spaceShip->sellCargo()) {
                    echo "We are at our home planet. Successfully sold cargo for current balance of ".$this->spaceShip->getBalance()."\r\n";
                } else {
                    echo "We are at our home planet, but we had no cargo to sell.";
                }
        }
        // see if we should generate any asteroids in the surrounding space
        $this->generateAsteroids();
        // print where we currently are.
        echo "Currently at " . $newX . ', ' . $newY . ', ' . $newZ . ".\r\n";
        // tell the caller (probably Ship_Base::move()) where we should be now.
        return array('x' => $newX, 'y' => $newY, 'z' => $newZ);
    }

    // add more asteroids to the universe if needed
    private function generateAsteroids()
    {
        if (true === $this->asteroidsOff) {
            return true;
        }
        // find out how many asteroids are in the local area
        $asteroidCount = 0;
        foreach ($this->asteroids as $asteroidId => $asteroid) {
            if ($asteroid->getX() > $this->spaceShip->getX() - $this->asteroidPreloadRange &&
                $asteroid->getY() > $this->spaceShip->getY() - $this->asteroidPreloadRange &&
                $asteroid->getZ() > $this->spaceShip->getZ() - $this->asteroidPreloadRange &&
                $asteroid->getX() < $this->spaceShip->getX() + $this->asteroidPreloadRange &&
                $asteroid->getY() < $this->spaceShip->getY() + $this->asteroidPreloadRange &&
                $asteroid->getZ() < $this->spaceShip->getZ() + $this->asteroidPreloadRange) {
                $asteroidCount++;
            }
            // if any asteroid is now so far from the ship that it can't have an effect then destroy it...
            if (abs($asteroid->getX() - $this->spaceShip->getX()) > $this->asteroidUnloadRange ||
                abs($asteroid->getY() - $this->spaceShip->getY()) > $this->asteroidUnloadRange ||
                abs($asteroid->getZ() - $this->spaceShip->getZ()) > $this->asteroidUnloadRange) {
                unset($this->asteroids[$asteroidId]);
            }   
        }
        // how many were we expecting?
        $expectedAsteroidCount = pow(((2 * $this->asteroidPreloadRange) + 1), 3) / $this->asteroidDensity;
        if ($asteroidCount < $expectedAsteroidCount) {
            // keep adding asteroids...
            do {
                // randomly choose where to place the new asteroid, if the location is too near to the ships current location then regenerate the asteroid spawn point
                do {
                    $xRand = rand(-$this->asteroidPreloadRange, $this->asteroidPreloadRange) + $this->spaceShip->getX();
                    $yRand = rand(-$this->asteroidPreloadRange, $this->asteroidPreloadRange) + $this->spaceShip->getY();
                    $zRand = rand(-$this->asteroidPreloadRange, $this->asteroidPreloadRange) + $this->spaceShip->getZ();
                    $dxRand = rand(-Asteroid::$maxComponentVelocity, Asteroid::$maxComponentVelocity);
                    $dyRand = rand(-Asteroid::$maxComponentVelocity, Asteroid::$maxComponentVelocity);
                    $dzRand = rand(-Asteroid::$maxComponentVelocity, Asteroid::$maxComponentVelocity);
                } while ( // check the location we just chose, if its too close to the ship then go round the loop again
                    (abs($xRand - $this->spaceShip->getX()) < $this->asteroidScanningRange &&
                    abs($yRand - $this->spaceShip->getY()) < $this->asteroidScanningRange &&
                    abs($zRand - $this->spaceShip->getZ()) < $this->asteroidScanningRange)
                    // also the asteroid must have some velocity...
                    || abs($dxRand) + abs($dyRand) + abs($dzRand) == 0
                );
                // make the new asteroid
                $this->asteroids[] = new Asteroid($xRand, $yRand, $zRand, $dxRand, $dyRand, $dzRand);
                $asteroidCount++;
            } while ($asteroidCount < $expectedAsteroidCount); // keep adding asteroids until there are the number we were expecting
        }
    }

    // randomly add the required number of warmholes to the universe
    private function generateWormHoles()
    {
        for ($i = 0; $i < $this->wormholeSpawnLimit; $i++) {
            $this->wormholes[] = new Wormhole(
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius)
            );
        }
    }
    
    private function generateGasClouds()
    {
        do {
            $this->gasClouds[] = new GasCloud(
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius),
                rand(-$this->universeRadius, $this->universeRadius)
            );
        } while (count($this->gasClouds) < $this->gasCloudsLimit);
    }

    // scan the space surrounding the ship for asteroids, and return the info we know about the wormholes.
    public function scan()
    {
        $result = array("wormholes" => array(), "gasclouds" => array(), "asteroids" => array(), "home" => array());
        // asteroids first.
        foreach ($this->asteroids as $asteroid) {
            // if the asteroid is within scanning range of the ship....
            if (abs($asteroid->getX() - $this->spaceShip->getX()) < $this->asteroidScanningRange &&
                abs($asteroid->getY() - $this->spaceShip->getY()) < $this->asteroidScanningRange &&
                abs($asteroid->getZ() - $this->spaceShip->getZ()) < $this->asteroidScanningRange) {
                // then add it's details to the result
                $result['asteroids'][] = array(
                    // we don't give absolute positions of asteroids, only their location relative to the ship
                    'x' => $asteroid->getX() - $this->spaceShip->getX(),
                    'y' => $asteroid->getY() - $this->spaceShip->getY(),
                    'z' => $asteroid->getZ() - $this->spaceShip->getZ(),
                    'dx' => $asteroid->getDx(),
                    'dy' => $asteroid->getDY(),
                    'dz' => $asteroid->getDz()
                );
            }
        }
        // now return the absolute location of all of the gas clouds
        foreach($this->gasClouds as $gasCloud) {
            $result['gasclouds'][] = array(
                'x' => $gasCloud->getX(),
                'y' => $gasCloud->getY(),
                'z' => $gasCloud->getZ()
            );
        }
        // now do the same for the wormholes...
        // for wormholes we only return the nearest end, and then the SHA1 of the furthest end
        // the navigator will have to figure out what the input of the SHA1 was. If you've actually
        // bothered to read these comments, please be assured that there is a better way to do it
        // then to brute force them.
        foreach ($this->wormholes as $wormhole) {
            // we'll do pythagorean distance
            // first for end1
            $diffX = abs($wormhole->getX1() - $this->spaceShip->getX());
            $diffY = abs($wormhole->getY1() - $this->spaceShip->getY());
            $diffZ = abs($wormhole->getZ1() - $this->spaceShip->getZ());
            $diffXY = sqrt(($diffX * $diffX) + ($diffY * $diffY));
            $diffXYZ1 = sqrt(($diffZ * $diffZ) + ($diffXY * $diffXY));
            // then for end2
            $diffX = abs($wormhole->getX2() - $this->spaceShip->getX());
            $diffY = abs($wormhole->getY2() - $this->spaceShip->getY());
            $diffZ = abs($wormhole->getZ2() - $this->spaceShip->getZ());
            $diffXY = sqrt(($diffX * $diffX) + ($diffY * $diffY));
            $diffXYZ2 = sqrt(($diffZ * $diffZ) + ($diffXY * $diffXY));
            // now show them the details for the nearest end, and let them figure out the further one...
            if ($diffXYZ1 < $diffXYZ2) {
                $result['wormholes'][] = array(
                    'x' => $wormhole->getX1(),
                    'y' => $wormhole->getY1(),
                    'z' => $wormhole->getZ1(),
                    'destination' => sha1($wormhole->getX2() . ',' . $wormhole->getY2() . ',' . $wormhole->getZ2())
                );
            } else {
                $result['wormholes'][] = array(
                    'x' => $wormhole->getX2(),
                    'y' => $wormhole->getY2(),
                    'z' => $wormhole->getZ2(),
                    'destination' => sha1($wormhole->getX1() . ',' . $wormhole->getY1() . ',' . $wormhole->getZ1())
                );
            }
        }
        // finally, the home planet
        $result['home'] = array(
            'x' => $this->home->getX(),
            'y' => $this->home->getY(),
            'z' => $this->home->getZ()
        );
        return $result;
    }
}