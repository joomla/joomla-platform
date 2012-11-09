## JObject

`JObject` is a smart object class following a Value Object pattern. Rather than explicitly declaring properties in the class, `JObject` stores virtual properties of the class in a private internal array. This differs from implementations prior to 12.3 where `JObject` operated on real class properties.

### Construction

The constructor for a new `JObject` object can optionally take an array or an object. The keys of the array, or the properties of the object will be bound to the properties of the `JObject` object.

    // Create an empty object.
    $object1 = new JObject;

    // Create an object with data. You can use an array or another object.
    $data = array(
        'foo' => 'bar',
    );

    $object2 = new JObject($data);

    // The following should echo "bar".
    echo $object2->foo;

### General Usage

`JObject` includes magic getters and setters to provide access to the internal property store as if they were explicitly declared properties of the class (the old `get` and `set` methods are no longer required). The `def` method is still included.

The `bind` method allows for injecting an existing array or object into the `JObject` object.

The `dump` method gets a plain `stdClass` version of the `JObject` object's properties. It will also support recursion to a specified number of levels where the default is 3 and a depth of 0 would return a `stdClass` object with all the properties in native form.

The `JsonSerializable` interface is implemented. The `jsonSerialize` proxies to the `dump` method (defaulting to a recursion depth of 3). Note that this interface only takes effect implicitly in PHP 5.4 so any code built for PHP 5.3 needs to explicitly use either the `jsonSerialize` or the `dump` method before passing to `json_encode`.

The `JObject` class also implements the `IteratorAggregate` interface so it can easily be used in a `foreach` statement.

    // Create an empty object.
    $object = new JObject;

    // Set a property.
    $object->foo = 'bar';

    // Get a property.
    $foo = $object->foo;

    // Binding some new data to the object.
    $object->bind(array('goo' => 'car');

    // Get a plain object version of the JObject.
    $stdClass = $object->dump();

    // Set a property with a default value if it is not already set.
    $object->def('foo', 'oof');

    // An alternative technique to get a value or a default.
    $foo = $object->foo || 'The default';

    // Convert the object into a JSON string.
    echo (string) $object;

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

### Revision History

This class was introduced in version 11.1 of the Joomla Platform.

The following method was removed in version 12.3:

* `__toString`

Version 12.3 introduced the following new methods:

* `__get`, `__set`, `__isset`, `__unset`
* `bind`
* `dump`
* `getiterator`
* `jsonSerialize`
* `dumpProperty` (protected)
* `getProperty` (protected)
* `setProperty` (protected)

The following methods are marked as deprecated and available until version 13.1 of the Joomla Platform for backward compatibility.

* `get` & `set` - Use direct property accessors instead of using these methods.
* `getError` & `getErrors` - Throw exceptions instead of relying on these methods. 
* `getProperties` - Use the dump method instead.
* `setProperties` - Use the bind method instead.

Be carefull when refactoring your code to use `dump` instead of `getProperties`. These methods are not exactly the same and will give slightly different results depending on how the class is defined.  Take the follow class as an example:

	class JExample extends JObject
	{
		public $publicVar;
		
		protected $protectedVar;

		protected $_privateVar;

		private $privateVar;
	}
	
	$object = new JExample(array('foo' => 'bar'));

	var_dump($object->getProperties());
	var_dump($object->dump());

The example above will output:
	
	array(3) {
	  ["foo"]=>
	  string(3) "bar"
	  ["publicVar"]=>
	  NULL
	  ["protectedVar"]=>
	  NULL
	}
	
	object(stdClass)#267 (1) {
	  ["foo"]=>
	  string(3) "bar"
	}
	
The `getProperties` method will return all of the new hidden properties (stored in `_properties`) as well as public and protected class properties without the underscore prefix.

The `dump` method will only expose the new hidden properties and ignore true class properties regardless of the scope.