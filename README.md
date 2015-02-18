# Do you have what it takes? #

## The Mission ##

You have been hired by the Interstellar Mining Corporation to program their latest batch of gas-mining drones.
You will need to scan for gas clouds, seek them out, harvest the gases, sell the harvest and keep clear of asteroids.
Did we mention the drones only have a limited battery life?
The programmer whos drones harvest the most gas within a single charge of the battery will win.

## The Prize ##

The winner will receive a Raspberry Pi 2.

## The Rules ##

* The drones are implemented using PHP.
* You will need to write your own class which implements `\CRC\Interstellar\Ship`, then instantiate a new
`\CRC\Interstellar\Universe` passing your `Ship` object as the parameter.
* You will need to write a `navigate()` method and optional `init()` method to define the operation of your drone.
* Whenever your mining drone makes contact with a gas cloud, it will harvest a unit of gas into its cargo.
* Whenever your mining drone makes contact with your home planet, it will sell each unit of gas for 1,000 credits.
* You may sell your cargo at any time for half of this rate, due to the buyer having to pay for collection.
* Your drone cannot hold infinite units of gas. (See `\CRC\Interstellar\Ship::$maxCargo`)
* At the end of the battery life (see `\CRC\Interstellar\Universe::$universeRunTime`), your credits will be totalled.
* Any gas left in your cargo at the end of the charge will be sold at half of the normal rate (500 credits per unit)
due to the buyer having to collect.