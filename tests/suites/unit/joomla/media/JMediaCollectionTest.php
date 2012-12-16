<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Media
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Test class for JMediaCollection.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Media
 *
 * @since       12.1
 */
class JMediaCollectionTest extends TestCase
{
	/**
	* @var JMediaCollectionCss
	*/
	protected $object;

	/**
	 * @var  array  files needed for tests
	 */
	protected $files;

	/**
	 * @var  string  path to test files
	 */
	protected $pathToTestFiles;

	/**
	 * @var  string  file extension suffix for compressed files
	 */
	protected $suffix;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function setUp()
	{
		$this->object = JMediaCollection::getInstance(array('type' => 'css'));
		$this->pathToTestFiles = JPATH_TESTS . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'css';
		$this->loadFiles();
		$this->suffix = 'combined';
	}

	/**
	 * Loads Necessary files
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function loadFiles()
	{
		// Load only non compressed css files in to array
		// Skip other files
		$this->files = JFolder::files(
			$this->pathToTestFiles, '.', false, true, array(),
			array('.min.css', '.php', '.html', '.combined.css')
		);
	}

	/**
	 * Test setOptions and getOptions methods
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testSetOptions()
	{
		$existing_options = $this->object->getOptions();

		$expected = array('COMPRESS' => true, 'FILE_COMMENTS' => false, 'COMPRESS_OPTIONS' => array('REMOVE_COMMENTS' => true));

		$this->object->setOptions($expected);

		$test = $this->object->getOptions();

		foreach ($expected as $key => $value)
		{
			$this->arrayHasKey($key, $test);
			$this->assertEquals($value, $test[$key]);
		}
		// Replace the existed options to avoid any harm to other tests
		$this->object->setOptions($existing_options);
	}

	/**
	 * Test addFiles and getFiles
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testAddFiles()
	{
		$this->object->addFiles($this->files);

		$test = $this->object->getFiles();

		$this->assertEquals($this->files, $test);

		$this->object->clear();
	}

	/**
	 * Test JMediaCollection::combineFiles Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testCombineFiles()
	{
		// Path to expected combined file without compression turned on
		$expectedFile = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'all.combined.css';

		$destinationFile = $this->pathToTestFiles . DIRECTORY_SEPARATOR . 'all.tmp.combined.css';

		$this->assertTrue(JMediaCollection::combineFiles($this->files, array('type' => 'css', 'OVERWRITE' => true), $destinationFile));

		$this->assertFileExists($destinationFile);

		$this->assertFileEquals($expectedFile, $destinationFile);

		unlink($destinationFile);

	}

	/**
	 * Test JMediaCollection::getCollectionTypes Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetCollectionTypes()
	{
		$expected = array('css','js');

		$test = JMediaCollection::getCollectionTypes();

		$this->assertEquals($expected, $test);
	}

	/**
	 * Test JMediaCollection::getInstance Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInstance()
	{
		$Combiner1 = JMediaCollection::getInstance(array('type' => 'css'));

		$this->assertInstanceOf('JMediaCollectionCss', $Combiner1);

		$Combiner2 = JMediaCollection::getInstance(array('type' => 'js'));

		$this->assertInstanceOf('JMediaCollectionJs', $Combiner2);
	}

	/**
	 * Test JMediaCompressor::isSupported Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function  testIsSupported()
	{
		$file1 = __DIR__ . 'comments.css';

		$this->assertTrue(JMediaCollection::isSupported($file1));

		$file2 = __DIR__ . 'case2.js';

		$this->assertTrue(JMediaCollection::isSupported($file2));

		$this->assertFalse(JMediaCompressor::isSupported('index.php'));
	}

	/**
	 * test clear Method
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testClear()
	{
		$this->object->addFiles($this->files);
		$this->object->combine();
		$this->object->clear();

		$this->assertAttributeEquals(array(), 'sources', $this->object);
		$this->assertAttributeEquals(0, 'sourceCount', $this->object);
		$this->assertAttributeEquals(null, 'combined', $this->object);

	}

}
