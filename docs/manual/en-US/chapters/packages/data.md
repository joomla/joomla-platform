## The Data Package

### JData

`JData` is a class that is used to store data but allowing you to access the data by mimicking the way PHP handles class properties. Rather than explicitly declaring properties in the class, `JData` stores virtual properties of the class in a private internal array. Concrete properties can still be defined but these a separate from the data.

#### Construction

The constructor for a new `JData` object can optionally take an array or an object. The keys of the array, or the properties of the object will be bound to the properties of the `JData` object.

```php
// Create an empty object.
$object1 = new JData;

// Create an object with data. You can use an array or another object.
$data = array(
    'foo' => 'bar',
);

$object2 = new JData($data);

// The following should echo "bar".
echo $object2->foo;
```

#### General Usage

`JData` includes magic getters and setters to provide access to the internal property store as if they were explicitly declared properties of the class.

The `bind` method allows for injecting an existing array or object into the `JData` object.

The `dump` method gets a plain `stdClass` version of the `JData` object's properties. It will also support recursion to a specified number of levels where the default is 3 and a depth of 0 would return a `stdClass` object with all the properties in native form. Note that the `dump` method will only return virtual properties set binding and magic methods. It will not include any concrete properties defined in the class itself.

The `JsonSerializable` interface is implemented. This method proxies to the `dump` method (defaulting to a recursion depth of 3). Note that this interface only takes effect implicitly in PHP 5.4 so any code built for PHP 5.3 needs to explicitly use either the `jsonSerialize` or the `dump` method before passing to `json_encode`.

The `JData` class also implements the `IteratorAggregate` interface so it can easily be used in a `foreach` statement.

```php
// Create an empty object.
$object = new JData;

// Set a property.
$object->foo = 'bar';

// Get a property.
$foo = $object->foo;

// Binding some new data to the object.
$object->bind(array('goo' => 'car');

// Get a plain object version of the JData.
$stdClass = $object->dump();

// Get a property with a default value if it is not already set.
$foo = $object->foo ?: 'The default';

// Iterate over the properties as if the object were a real array.
foreach ($object as $key => $value)
{
    echo "\n$key = $value";
}

if (version_compare(PHP_VERSION, '5.4') >= 0)
{
	// PHP 5.4 is aware of the JsonSerializable interface.
	$json = json_encode($object);
}
else
{
	// Have to do it the hard way to be compatible with PHP 5.3.
	$json = json_encode($object->jsonSerialize());
}
```

### JDataSet

`JDataSet` is a collection class that allows the developer to operate on a list of `JData` objects as if they were in a typical PHP array (`JDataSet` implements the `ArrayAccess`, `Countable` and `Iterator` interfaces).

#### Construction

A typical `JDataSet` object will be instantiated by passing an array of `JData` objects in the constructor.

```php
// Create an empty object.
$players = new JDataSet(
    array(
        new JData(array('race' => 'Elf', 'level' => 1)),
        new JData(array('race' => 'Chaos Dwarf', 'level' => 2)),
    )
);
```

#### General Usage

Array elements can be manipulated with the `offsetSet` and `offsetUnset` methods, or by using PHP array nomenclature.

The magic `__get` method in the `JDataSet` class effectively works like a "get column" method. It will return an array of values of the properties for all the objects in the list.

The magic `__set` method is similar and works like a "set column" method. It will set all a value for a property for all the objects in the list.

The `clear` method will clear all the objects in the data set.

The `keys` method will return all of the keys of the objects stored in the set. It works like the `array_keys` function does on an PHP array.

```php
// Add a new element to the end of the list.
$players[] => new JData(array('race' => 'Skaven', 'level' => 2));

// Add a new element with an associative key.
$players['captain'] => new JData(array('race' => 'Human', 'level' => 3));

// Get a keyed element from the list.
$captain = $players['captain'];

// Set the value of a property for all objects. Upgrade all players to level 4.
$players->level = 4;

// Get the value of a property for all object and also the count (get the average level).
$average = $players->level / count($players);

// Clear all the objects.
$players->clear();
```

`JDataSet` supports magic methods that operate on all the objects in the list. Calling an arbitrary method will iterate of the list of objects, checking if each object has a callable method of the name of the method that was invoked. In such a case, the return values are assembled in an array forming the return value of the method invoked on the `JDataSet` object. The keys of the original objects are maintained in the result array.

```php
/**
 * A custom JData.
 *
 * @package   Joomla\Examples
 * @since     12.1
 */
class PlayerObject extends JData
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

$players = new JDataSet(
    array(
        // Add a normal player.
        new PlayerObject(array('race' => 'Chaos Dwarf', 'level' => 2,
        	'maxHealth' => 40, 'actualHealth' => '32')),
        // Add an invincible player.
        new JData(array('race' => 'Elf', 'level' => 1)),
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
```

### JDataDumpable

`JDataDumpable` is an interface that defines a `dump` method for dumping the properties of an object as a `stdClass` with or without recursion.

### JDataMapper

The `JDataMapper` class establishes a bridge between data objects and a data source. The purpose is to provide very light coupling from the data object to the mapper. In other words, the data object should not care too much about what mapper is used or where the data is coming from. However, there is usually very tight coupling from the data mapper to the data object. The mapper obviously needs to know a lot about the data source and, to a degree, also the _type_ of data that it is loading.

Note that the mapper class is not intended to be a full Object Relationship Mapper (ORM) but it could be used to interface with established, third-party solutions that provide such features (Doctrine for example).

#### Public methods

There are six public entry points to the mapper API.

The constructor takes no arguments so the developer is free to add additional arguments (probably relating to the type of data source).

The `create` method is used to create new data. It expects a `JDataDumpable` object. This is an object that defines a `dump` method and includes `JData` and `JDataSet`. If a singular object, like `JData` is passed to the mapper, and instance of a singular object is expected to be returned. However, if an instance of a `JDataSet` object is passed to the method, an instance of a `JDataSet` will be returned.

The `delete` method is used to remove data from the data source. It expects either a single object identifier (for example, the value of the primary key in a database table), or an array of object identifiers. Nothing is returned.

The `find` method is used to search for and load data from the data source. It takes an optional where and sort criteria, a paging offset and an paging limit. It returns a `JDataSet` of objects matching the criteria. If no criteria is supplied, it will return all results subject to the pagination values that are specified. The `findOne` method works the same as `find` but it only returns one (the first) result retrieved by `find`.

The `update` method is used to update data in the data source. It is otherwise identical to the `create` method.

#### Extending the mapper

When extending the mapper, there are four abstract methods to implement and one protected method that can be optionally overriden.

The protected `initialise` method is called in the `JDataMapper` constructor. It can be overriden to support any setup required by the developer.

The abstract `doCreate` method must be implemented. It takes an array of dumped objects. The objects must be added to the data source, and should add any additional properties that are required (for example, time stamps). The method must return a `JDataSet` containing the objects that were created in the data source, including additional data that may have been added by the data source (for example, setting the primary key or created time stamps).

The abstract `doDelete` method must be implemented. It takes an array of unique object identifiers (such as primary keys in a database, cache identifiers, etc). The method must delete the corresponding objects in the data source. Any return value is ignored.

The abstract `doFind` method must be implemented. It takes the same arguments as the `find` method. The method must return a `JDataSet` object regardless of whether any data was found to match the search criteria. If this method accidentally returns more data records than defined by `$limit`, the calling `find` method will truncate the data set to the pagination limit that was specificed.

The abstract `doUpdate` method must be implemented. Like `doCreate`, it takes an array of dumped objects that must be updated in the data source. The method must return a `JDataSet` containing the objects that were updated in the data source, including additional data that may have been added by the data source (for example, modified time stamps).

The following basic example shows how a base mapper class could be devised to support database table operations (most DocBlocks are removed for brevity).

```php
class JDatabaseTableMapper extends JDataMapper
{
	/**
	 * @var  JDatabaseDriver
	 */
	protected $db;

	/**
	 * @var  string
	 */
	protected $table;

	/**
	 * @var  string
	 */
	protected $tableKey;

	/**
	 * @var  array
	 */
	private $_columns;

	public function __construct(JDatabaseDriver $db, $table, $tableKey)
	{
		// As for JTable, set a database driver, the table name and the primary key.
		$this->db = $db;
		$this->table = $table;
		$this->tableKey = $tableKey;

		parent::__construct();
	}

	protected function doCreate(array $input)
	{
		$result = new JDataSet;

		foreach ($input as $object)
		{
			// Ensure only the columns for this table are inserted.
			$row = (object) array_intersect_key((array) $object, $this->_columns);
			$this->db->insertObject($this->table, $row, $this->tableKey);
			$result[$row->{$this->tableKey}] = new JData($row);
		}

		return $result;
	}

	protected function doDelete(array $input)
	{
		// Sanitise.
		$input = array_map('intval', $input);

		if (empty($input))
		{
			return;
		}

		$q = $this->db->getQuery(true);
		$q->delete($q->qn($this->table))
			->where($q->qn($this->tableKey) . ' IN (' . implode(',', $input). ')');
		$this->db->setQuery($q)->execute();
	}

	protected function doFind($where = null, $sort = null, $offset = 0, $limit = 0)
	{
		$q = $this->db->getQuery(true);
		$q->select('*')
			->from($q->qn($this->table));

		// A simple example of column-value conditions.
		if (is_array($where) && !empty($where))
		{
			foreach ($where as $column => $value)
			{
				$q->where($q->qn($column) . '=' . $q->q($value));
			}
		}

		// A simple example of column-direction pairs.
		if (is_array($sort) && !empty($sort))
		{
			foreach ($sort as $column => $direction)
			{
				$q->where($q->qn($column) . ' ' . (strtoupper($direction == 'DESC') ? 'DESC' : 'ASC'));
			}
		}

		return new JDataSet($this->db->setQuery($q)->loadObjectList($this->tableKey, 'JData'));
	}

	protected function doUpdate(array $input)
	{
		$result = new JDataSet;

		foreach ($input as $object)
		{
			// Ensure only the columns for this table are updated.
			$row = (object) array_intersect_key((array) $object, $this->_columns);
			$this->db->updateObject($this->table, $row, $this->tableKey);
			$result[$row->{$this->tableKey}] = new JData($row);
		}

		return $result;
	}

	protected function initialise()
	{
		// Stash the columns for this table.
		$this->_columns = $this->db->getTableColumns($this->table);
	}
}
```

### Revision History

The `JData`, `JDataSet` and `JDataMapper` classes and the `JDataDumpable` interface were introduced in version 12.3 of the Joomla Platform.
