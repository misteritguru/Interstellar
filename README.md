# Do you have what it takes? #

## The Mission ##

You have been hired by the Interstellar Mining Corporation to program their latest batch of gas-mining drones.
You will need to scan for gas clouds, seek them out, harvest the gases, sell the harvest and keep clear of asteroids.
Did we mention the drones only have a limited battery life?
The programmer whos drones harvest the most gas within a single charge of the battery will win.

## The Prize ##

The winner will receive a Raspberry Pi kit.

## Getting Set Up ##

* Download the code from [https://github.com/JoinCRC/Interstellar](https://github.com/JoinCRC/Interstellar).
* Take a look at `demo.php` and `src/Demo/Ship.php` for a very simple example implementation.

## The Rules ##

* The drones are implemented using PHP.
* You will need to write your own class which implements `\CRC\Interstellar\Ship`, then instantiate a new
`\CRC\Interstellar\Universe` passing your `Ship` object as the parameter.
* You will need to write a `navigate()` method and optional `init()` method to define the operation of your drone.
* Asteroids move at up to 2 spacial units on the X, Y and Z axis per update. If you collide with an asteroid, your
ship will be destroyed.
* There are several wormholes dotted throughout the system, you will only know the location of the closest side of
each wormhole to your ship when you scan. Each wormhole has a signature which is its exit point as a SHA256 sum (see
`\CRC\Interstellar\Wormhole` and `\CRC\Interstellar\Universe` for more details). If you collide with one side of a
wormhole, you will be transported to the other side.
* Whenever your mining drone makes contact with a gas cloud, it will harvest a unit of gas into its cargo.
* Whenever your mining drone makes contact with your home planet, it will sell each unit of gas for 1,000 credits.
* You may sell your cargo at any time for half of this rate, due to the buyer having to pay for collection.
* Your drone cannot hold infinite units of gas. (See `\CRC\Interstellar\Ship::$maxCargo`)
* At the end of the battery life (see `\CRC\Interstellar\Universe::$universeRunTime`), your credits will be totalled.
* Any gas left in your cargo at the end of the charge will be sold at half of the normal rate (500 credits per unit)
due to the buyer having to collect.
* Do not edit anything in the `\CRC\Interstellar` namespace. We will run your code against our own implementation.
* All entries should be submitted by e-mail to php@joincrc.com before 30th March 2015.

## Core Ship Functions ##

You should look at the `\CRC\Interstellar\Ship` class for details but briefly:

* `scan()` will return a multidimensional array containing location of home planet, gas clouds, wormholes and relative
location of nearby asteroids.
* `sellCargo()` will allow you to sell your cargo for half the going rate at any time, this will be called automatically
upon contact with your home planet.
* `move($dx, $dy, $dz)` will move the ship. `$dx`, `$dy` and `$dz` are all integers between -1 and 1 to determine the
direction of movement.
* `halt()` is an alias of `move(0, 0, 0)`.