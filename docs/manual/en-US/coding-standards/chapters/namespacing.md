## Namespacing

This chapter outlines things to remember when dealing with namespacing. Full native namespacing has not yet been implemented in the Joomla! Platform. So far, there are some preparations being made to make the transition, but we are a long way from making that large change.


### Things to Remember When Contributing

When writing code to contribute back to the platform, there are some things to keep in mind.

* Preface internal PHP or SPL classes with a backslash. This tells PHP to search the global namespace. More information here: [Using Global Classes in Namespaces on php.net](http://www.php.net/manual/en/language.namespaces.faq.php#language.namespaces.faq.globalclass)

```php
/**
 * This is the wrong way to call the class.
 * When full namespacing is achieved, this
 * will result in a class load error.
 * Ex. "Class \Foo\Bar\stdClass not found"
 */
$obj = new stdClass;
$obj->property = 'foo';

/**
 * Same when working with Exceptions. This
 * will fail in a fully namespaced environment.
 */
throw new RuntimeException('Error Message Here.');

/**
 * This is the correct way to call it. The
 * The forward slash tells it to search for
 * the class in the global namespace
 */
$obj = new \stdClass;
$obj->property = 'bar';

// And Exceptions
throw new \RuntimeException('Yay namespaces!')
```

* Calls to included libraries must also follow proper namespacing rules. If you wanted to use the `PDO` classes, you'll need to preface that with a backslash as well.

```php
\PDO::query();
```

* When using callables, use the magic `__CLASS__` constant whenever possible. For example, in `JLoader` we call `spl_autoload_register()` several times. This function takes a callable as it's argument, which is passed as an array of class name and method name. (See example below.) The reason this is important is that the `__CLASS__` constant returns the calling class name. If that class is in a namespace, it returns the fully qualified class name. This means the code you write in this fashion will not need to be re-written when full namespacing is achieved. It will just work. More information here: [`__CLASS__` on php.net](http://php.net/manual/en/language.constants.predefined.php)

```php
// This is the old way
spl_autoload_register(array('JLoader', 'load'));

// This is the namespaced way
spl_autoload_register(array(__CLASS__, 'load'));
```

### Namespace Compatibility Layer

When full namespacing is achieved, there will be a compatibility layer that you can enable. This will allow you to use the latest platform code base with your existing app, without requiring a full re-write. By allowing users to make the transition as time allows, we reduce headaches without reducing our user base. All type hinting, `is_subclass_of()`, `instanceof`, etc continues to work with this approach as well. It's much more seamless than importing all the classes at the top of each file where they are used.

This compatibility layer will consist of basically a class map that will use the `class_alias` function to create aliases to all the class names as they exist now. One of the bigger benefits of this approach is that it requires little interaction from the end-user/developer. Simply enable the compatibility later, and your app will continue to function as it always has (in theory).

This layer will be available at least for 2 platform releases after achieving full namespace compatibility, after which it may be retained by any applications that are using it, such as the CMS if it chooses to implement these changes.