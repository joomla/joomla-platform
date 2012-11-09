## JObjectList

`JObjectList` is a smart class that allows the developer to operate on a list of `JObject` objects as if they were in a typical PHP array (JObjectList implements the ArrayAccess, Countable and Iterator interfaces).

### Construction

A typical `JObjectList` object will be instantiated by passing an array of `JObject` objects in the constructor.

    // Create an empty object.
    $players = new JObjectList(
        array(
            new JObject(array('race' => 'Elf', 'level' => 1)),
            new JObject(array('race' => 'Chaos Dwarf', 'level' => 2)),
        )
    );

### General Usage

Array elements can be manipulated with the `offsetSet` and `offsetUnset` methods, or by using PHP array nomenclature.

The magic `__get` method in the `JObjectList` class effectively works like a "get column" method. It will return an array of values of the properties for all the objects in the list.

The magic `__set` method is similar and works like a "set column" method. It will set all a value for a property for all the objects in the list.

    // Add a new element to the end of the list.
    $players[] => new JObject(array('race' => 'Skaven', 'level' => 2));

    // Add a new element with an associative key.
    $players['captain'] => new JObject(array('race' => 'Human', 'level' => 3));

    // Get a keyed element from the list.
    $captain = $players['captain'];

    // Set the value of a property for all objects. Upgrade all players to level 4.
    $players->level = 4;

    // Get the value of a property for all object and also the count (get the average level).
    $average = $players->level / count($players);

### Smart Methods

`JObjectList` supports some smart methods. Calling an arbitrary method will iterate of the list of objects, checking if each object has a callable method of the name of the method that was invoked. In such a case, the return values are assembled in an array forming the return value of the method invoked on the `JObjectList` object. The keys of the original objects are maintained in the result array.

    /**
     * A custom JObject.
     *
     * @package   Joomla\Examples
     * @since     12.1
     */
    class PlayerObject extends JObject
    {
        /**
         * Get player damage.
         *
         * @return  integer  The amount of damage the player has received.
         *
         * @since   12.1
         */
        public function hurt()
        {
            return (int) $this->maxHealth - $this->actualHealth;
        }
    }

    $players = new JObjectList(
        array(
            // Add a normal player.
            new PlayerObject(array('race' => 'Chaos Dwarf', 'level' => 2,
            	'maxHealth' => 40, 'actualHealth' => '32')),
            // Add an invincible player.
            new JObject(array('race' => 'Elf', 'level' => 1)),
        )
    );

    // Get an array of the hurt players.
    $hurt = $players->hurt();

    if (!empty($hurt))
    {
        // In this case, $hurt = array(0 => 8);
        // There is no entry for the second player
        // because that object does not have a "hurt" method.
        foreach ($hurt as $playerKey => $player)
        {
            // Do something with the hurt players.
        }
    };

### Revision History

This class was introduced in version 12.3 of the Joomla Platform.

