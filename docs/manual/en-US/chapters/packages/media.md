## The Media Package

### Introduction

The *Media* package is designed to compile and compress assets such as `Javascript` and `CSS` files.

### Available Classes

#### JMediaCompressor

Abstract class `JMediaCompressor` defines functions to compress contents of a file by removing comment blocks, unnecessary white space  etc. It is extended by concrete classes such as `JMediaCompressorCss` and
`JMediaCompressorJs` which contains implementation of compress function for a particular file type.

 *Example* : How to obtain a `JMediaCompressorJs` object for javascript files and compress content.

```php
	$file = __DIR__ . 'test.js';

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
(1) static function getInstance() can be used to obtain a compressor object. This function takes an array of options as the
argument.


(2) static function compressString() can be used to compress a string without creating and getting a compressor object.
It needs two arguments, source string and options.

*Example* : How to use JMediaCompressor::compressString()

```php
	$file = __DIR__ . 'test.css';

	 // Options for compressor
 	$options = array('type' => 'css', 'REMOVE_COMMENTS' => true);

	$compressedContent = JMediaCompressor::compressString(file_get_contents($file), $options);

```

(3) static function compressFiles() takes three arguments, source file, options and destination file. It will compress the contents of source
file using the given options and write the compressed contents into destination file.

static function isSupported() can be used to check whether there is a compressor supported for a particular file type.

*Example* : How to use JMediaCompressor::compressFile()

```php

	$file = __DIR__ . 'test.css';

	$destinationFile = __DIR__ . 'test.min.css';

	 // Options for compressor
 	$options = array('REMOVE_COMMENTS' => true, 'overwrite' => true);

	if(JMediaCompressor::isSupported($file))
	{
		JMediaCompressor::compressFile($file, $options, $destinationFile);
	}

```

##### Available options for compressors.

- `REMOVE_COMMENTS` : `boolean` :- Defines whether to remove comments or not

specific options to `JMediaCompressor::compressFile()`

- `overwrite` : `boolean`   :- To define whether to overwrite if destination file already exists
- `prefix` : `string`       :- Name prefix to be used for destination file if the no file path is passed

specific options to `JMediaCompressorCss`

- `MIN_COLOR_CODES` : `boolean`   :- To define whether try to compress HTML Color codes
- `LIMIT_LINE_LENGTH` : `boolean` :- To define whether to break compressed content in to a few lines



#### JMediaCollection

Abstract class `JMediaCollection` defines functions to combine several files into a single file. It is extended by concrete classes such as `JMediaCollectionCss` and
`JMediaCollectionJs` which contains implementation of combine function for a particular file type.

*Example* : How to obtain a `JMediaCollectionCss` object for css files and combine a set of files.

```php

	$files = array(__DIR__.'file1.css', __DIR__.'file2.css', __DIR__.'file3.css' );

	 // Options for collection
 	$options = array('type' => 'css', 'FILE_COMMENTS' => true, 'COMPRESS' => false);

	$collection = JMediaCollection::getInstance($options);

	$collection->addFiles($files);

	$collection->combine();

	file_put_contents(__DIR__.'combined.css', $collection->getCombined());

```

*Example* : How to obtain a `JMediaCollectionCss` object for css files with a custom compressor and compress and combine a set of files.

If no compressor object is passed via options and `COMPRESS` is set to `true` in options, a compressor with default options will be used.

```php

	// Options for compressor
	$options_com = array('type' => 'css', 'REMOVE_COMMENTS' => true);

    $compressor = JMediaCompressor::getInstance(options_com);

    // a set of files to compress and combine
	$files = array(__DIR__.'file1.css', __DIR__.'file2.css', __DIR__.'file3.css' );

	 // Options for Collection
 	$options = array('type' => 'css', 'FILE_COMMENTS' => true, 'COMPRESS' => true, 'COMPRESSOR' => $compressor);

	$collection = JMediaCollection::getInstance($options);

	$collection->addFiles($files);

	// Since 'COMPRESS' => true in options, this will compress and combine the contents of files
	$collection->combine();

	file_put_contents(__DIR__.'combined.min.css', $collection->getCombined());

```
##### Available static functions in JMediaCollection