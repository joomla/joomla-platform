## The Media Package

### Introduction

The *Media* package is designed to compile and compress assets such as `Javascript` and `CSS` files.

### Abstract Classes

#### JMediaCompressor

Abstract class `JMediaCompressor` contains functions to compress contents of a file by removing comment blocks, unnecessary white space  etc. It is extended by concrete classes such as `JMediaCompressorCss` and
`JMediaCompressorJs` which contains implementation of compress function for a particular file type.

##### *Example* : How to obtain a `JMediaCompressorJs` object for javascript files.

```php
	 // Options
 	$options = array('type' => 'css', 'REMOVE_COMMENTS' => true);

	$collection = JMediaCollection::getInstance($options);
```

#### JMediaCollection

Abstract class `JMediaCollection` contains functions to combine several files into a single file. It is extended by concrete classes such as `JMediaCollectionCss` and
`JMediaCollectionJs` which contains implementation of combine function for a particular file type.

##### *Example* : How to obtain a `JMediaCollectionCss` object for css files.

```php
	 // Options
 	$options = array('type' => 'css', 'FILE_COMMENTS' => true);

	$collection = JMediaCollection::getInstance($options);
```
