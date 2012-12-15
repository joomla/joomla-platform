## The Media Package

### Introduction

The *Media* package is designed to compile and compress assets such as `Javascript` and `CSS` files.

### Available Classes

#### JMediaCompressor

Abstract class `JMediaCompressor` contains functions to compress contents of a file by removing comment blocks, unnecessary white space  etc. It is extended by concrete classes such as `JMediaCompressorCss` and
`JMediaCompressorJs` which contains implementation of compress function for a particular file type.

 *Example* : How to obtain a `JMediaCompressorJs` object for javascript files and compress content.

```php
	$file = __DIR__ . test.js

	 // Options for compressor
 	$options = array('type' => 'js', 'REMOVE_COMMENTS' => true);

	$compressor = JMediaCompressor::getInstance($options);

	// Use setUncompressed function to set source contents
	$compressor->setUncompressed(file_get_contents($file));

	$compressor->compress();

	// Get compressed contents
	$compressedContent = $compressor->getCompressed();

```

##### Available static functions in JMediaCompressor

Example : How to use JMediaCompressor::compressString()

```php
	$file = __DIR__ . test.css;

	 // Options for compressor
 	$options = array('type' => 'css', 'REMOVE_COMMENTS' => true);

	$compressedContent = JMediaCompressor::compressString(file_get_contents($file), $options);

```

Example : How to use JMediaCompressor::compressFile()

```php

	$file = __DIR__ . test.css;

	$destinationFile = __DIR__ . test.min.css;

	 // Options for compressor
 	$options = array('REMOVE_COMMENTS' => true, 'overwrite' => true);

	JMediaCompressor::compressFile($file, $options, $destinationFile);

```



#### JMediaCollection

Abstract class `JMediaCollection` contains functions to combine several files into a single file. It is extended by concrete classes such as `JMediaCollectionCss` and
`JMediaCollectionJs` which contains implementation of combine function for a particular file type.

 *Example* : How to obtain a `JMediaCollectionCss` object for css files.

```php
	 // Options
 	$options = array('type' => 'css', 'FILE_COMMENTS' => true, 'COMPRESS' => false);

	$collection = JMediaCollection::getInstance($options);
```
