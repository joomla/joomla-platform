## The Media Package

### Introduction

The *Media* package is designed to compile and compress assets such as `Javascript` and `CSS` files.

### Abstract Classes

#### JMediaCollection

Abstract class `JMediaCollection` contains functions to combine several files into a single file. It is extended by concrete classes such as `JMediaCollectionCss` and
`JMediaCollectionJs` which contains implementations of combine functions for a particular file type.

Example : How to obtain a `JMediaCollectionCss` object for css files.

```php
	 // Options
 	$options = array('type' => 'css', 'FILE_COMMENTS' => true);

	$collection = JMediaCollection::getInstance($options);
```

