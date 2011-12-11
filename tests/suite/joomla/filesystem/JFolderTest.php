<?php
/**
 * @package	 Joomla.UnitTest
 * @subpackage  filesystem
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE
 */

require_once JPATH_PLATFORM . '/joomla/filesystem/folder.php';
require_once JPATH_PLATFORM . '/joomla/filesystem/path.php';

/**
 * A unit test class for JFolder
 */
class JFolderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Creating a directory structure
	 * ./tmp/test/
	 * ./tmp/test/index.html
	 * ./tmp/test/index.txt
	 * ./tmp/test/foo1/
	 * ./tmp/test/foo1/index.html
	 * ./tmp/test/foo1/index.txt
	 * ./tmp/test/foo1/bar1/
	 * ./tmp/test/foo1/bar1/index.html
	 * ./tmp/test/foo1/bar1/index.txt
	 * ./tmp/test/foo1/bar2/
	 * ./tmp/test/foo1/bar2/index.html
	 * ./tmp/test/foo1/bar2/index.txt
	 * ./tmp/test/foo2/
	 * ./tmp/test/foo2/index.html
	 * ./tmp/test/foo2/index.txt
	 * ./tmp/test/foo2/bar1/
	 * ./tmp/test/foo2/bar1/index.html
	 * ./tmp/test/foo2/bar1/index.txt
	 * ./tmp/test/foo2/bar2/
	 * ./tmp/test/foo2/bar2/index.html
	 * ./tmp/test/foo2/bar2/index.txt
	 */
	protected function setUp()
	{
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test'), 0777, true);
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test/foo1'), 0777, true);
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1'), 0777, true);
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2'), 0777, true);
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test/foo2'), 0777, true);
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1'), 0777, true);
		mkdir(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2'), 0777, true);
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/index.txt'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/index.txt'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1/index.txt'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2/index.txt'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.txt'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1/index.txt'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2/index.html'), 'test');
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2/index.txt'), 'test');
	}

	/**
	 * JFolder::copy copy a folder
	 */
	public function testCopy()
	{
		$this->assertTrue(
			JFolder::copy(JPATH_ROOT . '/tmp/test/foo1', JPATH_ROOT . '/tmp/test/foo3'),
			'Line: ' . __LINE__. ' Folder does not copy'
		);
		$this->assertEquals(
			array(
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/index.html'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/index.html'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar2/index.html'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar2/index.txt'),
			),
			JFolder::files(JPATH_ROOT . '/tmp/test/foo3', '.', true, true),
			'Line: ' . __LINE__ . ' Copy from ' . JPATH_ROOT . '/tmp/test/foo1 to ' . JPATH_ROOT . '/tmp/test/foo3 does not succeed'
		);
		$this->assertEquals(
			'test',
			file_get_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo3/index.txt')),
			'Line: ' . __LINE__. ' Folder does not copy'
		);
		file_put_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.txt'), 'new content');
		$this->assertTrue(
			JFolder::copy(JPATH_ROOT . '/tmp/test/foo2', JPATH_ROOT . '/tmp/test/foo3', '', true),
			'Line: ' . __LINE__. ' Folder does not copy'
		);
		$this->assertEquals(
			'new content',
			file_get_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.txt')),
			'Line: ' . __LINE__. ' Folder does not copy'
		);
		$this->assertEquals(
			'new content',
			file_get_contents(JPath::clean(JPATH_ROOT . '/tmp/test/foo3/index.txt')),
			'Line: ' . __LINE__. ' Folder does not copy'
		);
	}

	/**
	 * JFolder::create create a folder
	 */
	public function testCreate()
	{
		$this->assertTrue(
			JFolder::create(JPATH_ROOT . '/tmp/test/foo3/bar1'),
			'Line: ' . __LINE__. ' Folder does not create'
		);
		$this->assertTrue(
			JFolder::create(JPATH_ROOT . '/tmp/test/foo3/bar1'),
			'Line: ' . __LINE__. ' Folder does not create'
		);
		$this->assertEquals(
			array(
				JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1'),
			),
			JFolder::folders(JPATH_ROOT . '/tmp/test', 'bar1' , true, true),
			'Line: ' . __LINE__ . ' Folder does not create'
		);
	}

	/**
	 * JFolder::delete delete a folder
	 */
	public function testDelete()
	{
		$this->assertTrue(
			JFolder::delete(JPATH_ROOT . '/tmp/test/foo1'),
			'Line: ' . __LINE__. ' Folder does not delete'
		);
		$this->assertEquals(
			array(
				JPath::clean(JPATH_ROOT . '/tmp/test/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2/index.txt'),
			),
			JFolder::files(JPATH_ROOT . '/tmp/test', 'index.txt' , true, true),
			'Line: ' . __LINE__ . ' Delete ' . JPATH_ROOT . '/tmp/test/foo1 does not succeed'
		);
		$this->assertFalse(
			JFolder::delete(JPATH_ROOT . '/tmp/test/foo1'),
			'Line: ' . __LINE__. ' Folder delete'
		);
	}

	/**
	 * JFolder::move move a folder to another location
	 */
	public function testMove()
	{
		$this->assertTrue(
			JFolder::move(JPATH_ROOT . '/tmp/test/foo1', JPATH_ROOT . '/tmp/test/foo3'),
			'Line: ' . __LINE__. ' Folder does not move'
		);
		$this->assertEquals(
			array(
				JPath::clean(JPATH_ROOT . '/tmp/test/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/index.txt'),
				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar2/index.txt'),
			),
			JFolder::files(JPATH_ROOT . '/tmp/test', 'index.txt' , true, true),
			'Line: ' . __LINE__ . ' Move from ' . JPATH_ROOT . '/tmp/test/foo1 to ' . JPATH_ROOT . '/tmp/test/foo3 does not succeed'
		);
		$this->assertTrue(
			JFolder::move(JPATH_ROOT . '/tmp/test/foo2', JPATH_ROOT . '/tmp/test/foo3/bar1/foo2'),
			'Line: ' . __LINE__. ' Folder does not move'
		);
		$this->assertEquals(
			array(
 				JPath::clean(JPATH_ROOT . '/tmp/test/index.txt'),
 				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/index.txt'),
 				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/index.txt'),
 				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/foo2/index.txt'),
 				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/foo2/bar1/index.txt'),
  				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar1/foo2/bar2/index.txt'),
 				JPath::clean(JPATH_ROOT . '/tmp/test/foo3/bar2/index.txt'),
			),
			JFolder::files(JPATH_ROOT . '/tmp/test', 'index.txt' , true, true),
			'Line: ' . __LINE__ . ' Move from ' . JPATH_ROOT . '/tmp/test/foo1 to ' . JPATH_ROOT . '/tmp/test/foo3 does not succeed'
		);
	}

	/**
	 * JFolder::exists verify the existence of a folder
	 */
	public function testExists()
	{
		$this->assertTrue(
			JFolder::exists(JPATH_ROOT . '/tmp/test'),
			'Line: ' . __LINE__. ' Folder does not exists'
		);
		$this->assertFalse(
			JFolder::exists(JPATH_ROOT . '/tmp/test/not/exists'),
			'Line: ' . __LINE__. ' Folder exists'
		);
	}

	/**
	 * Cases for testFiles
	 */
	public function casesFiles()
	{
		$cases = array(
			array(
				'Should exclude index.html files',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/index.txt'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/index.txt'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1/index.txt'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2/index.txt'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.txt'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1/index.txt'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2/index.txt'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'index.*',
				true,
				true,
				array('index.html'),
			),
			array(
				'Should include full path of both index.html files',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2/index.html'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'index.html',
				true,
				true,
			),
			array(
				'Should include only file names of both index.html files',
				array(
					JPath::clean('index.html'),
					JPath::clean('index.html'),
					JPath::clean('index.html'),
					JPath::clean('index.html'),
					JPath::clean('index.html'),
					JPath::clean('index.html'),
					JPath::clean('index.html'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'index.html',
				true,
				false
			),
			array(
				'Non-recursive should only return top folder file full path',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/index.html'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'index.html',
				false,
				true,
			),
			array(
				'Recursive with depth=1 should only return top and level one folder file full path',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/index.html'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/index.html'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'index.html',
				1,
				true,
			),
			array(
				'Non-recursive should return only file name of top folder file',
				array(
					JPath::clean('index.html'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'index.html',
				false,
				false,
			),
			array(
				'Non-existent path should return false',
				false,
				'/this/is/not/a/path'
			),
			array(
				'When nothing matches the filter, should return empty array',
				array(),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'nothing.here',
				true,
				true,
				array(),
				array(),
			),
		);
		return $cases;
	}

	/**
	 * JFolder::files give an array of files found
	 *
	 * @dataProvider casesFiles
	 */
	public function testFiles($message, $expected, $path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $excludefilter = array('^\..*', '.*~'))
	{
		$this->assertEquals(
			$expected,
			JFolder::files($path, $filter, $recurse, $full, $exclude, $excludefilter),
			'Line: ' . __LINE__ . ' ' . $message
		);
	}

	/**
	 * Cases for testFolders
	 */
	public function casesFolders()
	{
		$cases = array(

			array(
				'Should exclude bar1 folders',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'.',
				true,
				true,
				array('bar1'),
			),
			array(
				'Should show only bar1 folders',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'bar1',
				true,
				true,
			),
			array(
				'Should show only bar* folders',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'bar',
				true,
				true,
			),
			array(
				'Should show only bar1 folders excluding foo2',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'bar1',
				true,
				true,
				array('foo2'),
			),
			array(
				'Should include full path of all folders',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1/bar2'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2/bar2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'.',
				true,
				true,
			),
			array(
				'Should include all folders names',
				array(
					JPath::clean('foo1'),
					JPath::clean('bar1'),
					JPath::clean('bar2'),
					JPath::clean('foo2'),
					JPath::clean('bar1'),
					JPath::clean('bar2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'.',
				true,
			),
			array(
				'Non-recursive should only return top folders full path',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'.',
				false,
				true,
			),
			array(
				'Recursive with depth=1 should only return top folders full path',
				array(
					JPath::clean(JPATH_ROOT . '/tmp/test/foo1'),
					JPath::clean(JPATH_ROOT . '/tmp/test/foo2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'.',
				0,
				true,
			),
			array(
				'Non-recursive should only return top folders name',
				array(
					JPath::clean('foo1'),
					JPath::clean('foo2'),
				),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
			),
			array(
				'Non-existent path should return false',
				false,
				'/this/is/not/a/path'
			),
			array(
				'When nothing matches the filter, should return empty array',
				array(),
				JPath::clean(JPATH_ROOT . '/tmp/test'),
				'nothing.here',
				true,
				true,
				array(),
				array(),
			),
		);
		return $cases;
	}

	/**
	 * JFolder::folders give an array of folders found
	 *
	 * @dataProvider casesFolders
	 */
	public function testFolders($message, $expected, $path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'), $excludefilter = array('^\..*', '.*~'))
	{
		$this->assertEquals(
			$expected,
			JFolder::folders($path, $filter, $recurse, $full, $exclude, $excludefilter),
			'Line: ' . __LINE__ . ' ' . $message
		);
	}

	/**
	 * Cases for testListFolderTree
	 */
	public function casesListFolderTree()
	{
		$cases = array(
			array(
				array(
					array(
						'id' => 1,
						'parent' => 0,
						'name'=> 'foo1',
						'fullname' => JPATH_ROOT . '/tmp/test/foo1',
						'relname' => '/tmp/test/foo1',
					),
					array(
						'id' => 2,
						'parent' => 1,
						'name'=> 'bar1',
						'fullname' => JPATH_ROOT . '/tmp/test/foo1/bar1',
						'relname' => '/tmp/test/foo1/bar1',
					),
					array(
						'id' => 3,
						'parent' => 1,
						'name'=> 'bar2',
						'fullname' => JPATH_ROOT . '/tmp/test/foo1/bar2',
						'relname' => '/tmp/test/foo1/bar2',
					),
					array(
						'id' => 4,
						'parent' => 0,
						'name'=> 'foo2',
						'fullname' => JPATH_ROOT . '/tmp/test/foo2',
						'relname' => '/tmp/test/foo2',
					),
					array(
						'id' => 5,
						'parent' => 4,
						'name'=> 'bar1',
						'fullname' => JPATH_ROOT . '/tmp/test/foo2/bar1',
						'relname' => '/tmp/test/foo2/bar1',
					),
					array(
						'id' => 6,
						'parent' => 4,
						'name'=> 'bar2',
						'fullname' => JPATH_ROOT . '/tmp/test/foo2/bar2',
						'relname' => '/tmp/test/foo2/bar2',
					),
				),
				JPATH_ROOT . '/tmp/test',
				'.',
			),
			array(
				array(
					array(
						'id' => 1,
						'parent' => 0,
						'name'=> 'foo1',
						'fullname' => JPATH_ROOT . '/tmp/test/foo1',
						'relname' => '/tmp/test/foo1',
					),
					array(
						'id' => 2,
						'parent' => 1,
						'name'=> 'bar1',
						'fullname' => JPATH_ROOT . '/tmp/test/foo1/bar1',
						'relname' => '/tmp/test/foo1/bar1',
					),
				),
				JPATH_ROOT . '/tmp/test',
				'1',
			),
			array(
				array(
					array(
						'id' => 1,
						'parent' => 0,
						'name'=> 'foo1',
						'fullname' => JPATH_ROOT . '/tmp/test/foo1',
						'relname' => '/tmp/test/foo1',
					),
					array(
						'id' => 2,
						'parent' => 0,
						'name'=> 'foo2',
						'fullname' => JPATH_ROOT . '/tmp/test/foo2',
						'relname' => '/tmp/test/foo2',
					),
				),
				JPATH_ROOT . '/tmp/test',
				'.',
				1,
			),
		);
		return $cases;
	}

	/**
	 * JFolder::listFolderTree return a tree representation of a folder
	 *
	 * @dataProvider casesListFolderTree
	 */
	public function testListFolderTree($expected, $path, $filter, $maxLevel = 3)
	{
		$this->assertEquals(
			$expected,
			JFolder::listFolderTree($path, $filter, $maxLevel),
			'Line: ' . __LINE__ . ' Folder tree representation is not correct'
		);
	}

	/**
	 * Cases for testMakeSafe
	 */
	public function casesMakeSafe()
	{
		$cases = array(
			array('test-1/test_directory','test-1/test_directory'),
			array("test-1\# |@.)]'\"test_directory",'test-1\test_directory'),
		);
		return $cases;
	}

	/**
	 * JFolder::makeSafe remove all characters that are not A-Z a-z 0-9 : _ \ / -
	 *
	 * @dataProvider casesMakeSafe
	 */
	public function testMakeSafe($path, $expected) {
		$this->assertEquals(
			$expected,
			JFolder::makeSafe($path),
			'Line: ' . __LINE__ . ' JFolder::makeSafe of ' . $path . ' produces ' . JFolder::makeSafe($path) . ' instead of ' . $expected
		);
	}

	/**
	 * Delete the directory structure
	 *
	 * ./tmp/test
	 */
	protected function tearDown()
	{
		$this->_deleteDir(JPATH_ROOT . '/tmp/test');
	}

	/**
	 * Convenience method for deleting a directory
	 */
	private function _deleteDir($path)
	{
		// Read the source directory
		if ($handle = @opendir($path))
		{
			// Loop on all items (files and folders)
			while(($item = readdir($handle)) !== false)
			{
				if ($item != '..' && $item != '.')
				{
					$item = $path . '/' . $item;
					if (is_dir($item))
					{
						// Delete directory
						$this->_deleteDir($item);
					}
					else
					{
						// Delete file
						unlink($item);
					}
				}
			}
			rmdir($path);
			closedir($handle);
		}
	}
}

