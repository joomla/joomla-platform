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
 * Test class for JMediaCompressor.
 */
class JMediaCollectionTest extends TestCase
{
	/**
	* @var JMediaCollectionCss
	*/
	protected $object;

	/**
	* Sets up the fixture, for example, opens a network connection.
	* This method is called before a test is executed.
	*/
	protected function setUp()
	{
		$this->object = JMediaCollection::getInstance(array('type' => 'css'));
	}

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

	public function testSetSources()
	{
		$path = JPATH_BASE . '/test_files/css';

		$files = JFolder::files($path,'.',false,true, array(), array('.min.css', '.php', '.html','.combined.css'));//get full path

		$this->object->addFiles($files);

		$test = $this->object->getSources();

		$this->assertEquals($files, $test);
		
		$this->object->clear();
	}

	public function testGetInstance()
	{
		$Combiner1 = JMediaCollection::getInstance(array('type'=>'css'));

		$this->assertInstanceOf('JMediaCollectionCss', $Combiner1);

		$Combiner2 = JMediaCollection::getInstance(array('type'=>'js'));

		$this->assertInstanceOf('JMediaCollectionJs', $Combiner2);
	}

	public function testGetCollectionTypes()
	{
		$expected = array('css','js');
	
		$test = JMediaCollection::getCollectionTypes();

		$this->assertEquals($expected, $test);
	
	}


	public function testCombineFiles()
	{
		// Path to source css files
		$path = JPATH_BASE . '/test_files/css';

		$files = JFolder::files($path,'.',false,true, array(), array('.min.css', '.php', '.html','.combined.css'));//get full path

		$this->object->addFiles($files);
		
		// Path to expected combined file without compression turned on
		$expected = file_get_contents($path . '/all.combined.css');


		$this->object->combine();

		$this->assertEquals($expected, $this->object->getCombined());

		// Path to expected combined file with compression turned on
		$expectedCompressed = file_get_contents($path . '/all.combined.min.css');

		$this->object->setOptions(array('COMPRESS' => true));

		$this->object->combine();

		// Assert with compression turned on
		$this->assertEquals($expectedCompressed, $this->object->getCombined());
	}

	public function  testIsSupported()
	{
		$file1 = JPATH_BASE . '/test_files/css/comments.css';

		$this->assertTrue(JMediaCollection::isSupported($file1));

		$file2 = JPATH_BASE . '/test_files/js/case2.js';

		$this->assertTrue(JMediaCollection::isSupported($file2));
	}

	public function testClear()
	{
		$this->object->addFiles($this->loadCssFiles());
		$this->object->combine();
		$this->object->clear();

		$this->assertAttributeEquals(array(), 'sources', $this->object);
		$this->assertAttributeEquals(0, 'sourceCount', $this->object);
		$this->assertAttributeEquals(null, 'combined', $this->object);

	}

	public function loadCssFiles()
	{
		// Path to source css files
		$path = JPATH_BASE . '/test_files/css';

		$files = JFolder::files($path,'.',false,true, array(), array('.min.css', '.php', '.html','.combined.css'));//get full path

		return $files;
	}
}