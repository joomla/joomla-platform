<?php
/**
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @package     Joomla.UnitTest
 */

/**
 * Test class for JForm.
 *
 * @package		Joomla.UnitTest
 * @subpackage  Form
 *
 */
class JFormRuleTelTest extends JoomlaTestCase
{
	/**
	 * set up for testing
	 *
	 * @return void
	 */
		public function setUp()
	{
		jimport('joomla.form.formrule');
		jimport('joomla.utilities.xmlelement');
		require_once JPATH_PLATFORM.'/joomla/form/rules/integer.php';
	}
	
	/**
	 * Test the JFormRuleUrl::test method.
	 *
	 * @dataProvider provider
     */
	public function testInteger($xmlfield,$integer,$expected)
	{
		// Initialise variables.

		$rule = new JFormRuleInteger;

		// The field allows you to optionally limit the range of integers in specific ways.
		// integer tests unrestricted integers.
		// integerAll tests unrestricted integers with the All attribute.
		// integerPositive accepts only positive integers.
		// integerNonNegative accepts positive integers and 0.
		// integerNegative  accepts only negative integers.
		// integerMax restricts to values less than or equal to a maximum.
		// integerMin restricts to values greater than or equal to a minimum.
		// integerComp_1 uses both a type and a maximum.
		// integerComp_2 uses both a type and a minimum.
		// integerComp_3 uses both a type and a maximum that are in conflict.
		// integerComp_4 uses both a type and a minimum that are in conflict.
		// integerComp_4 uses a maximum and a minimum that are in conflict.
		
		
		$xml = simplexml_load_string('<form>
		<field name="integer" />,
		<field name="integerAll" integertype="all"/>,
		<field name="integerPositive" integertype="positive"/>,
		<field name="integerNonnegative" integertype="nonnegative"/>,
		<field name="integerNegative" integertype="negative"/>,
		<field name="integerMax" max="100"/>,
		<field name="integerMin" min="10"/>,
		<field name="integerComp_1" integertype="positive" max="100"/>,
		<field name="integerComp_2" integertype="positive" min="10"/>,
		<field name="integerComp_3" integertype="positive" max="-1"/>,
		<field name="integerComp_4" integertype="negative" min="10"/>,
		<field name="integerComp_4" max="1" min="10"/>,
		
		</form>', 'JXMLElement');
		$i = 0;
		while ($i <= 10) {
			
				if ($xmlfield == $i){
					if ($expected == 'false'){
						// Test fail conditions.
						$this->assertThat(
							$rule->test($xml->field[$i], $integer),
							$this->isFalse(),
							'Line:'.__LINE__.' The rule should return'.$expected.'.'
						);
					}
					if ($expected == 'true'){
						// Test pass conditions.
						$this->assertThat(
							$rule->test($xml->field[$i], $integer),
							$this->isTrue(),
							'Line:'.__LINE__.' The rule should return'.$expected.'.'
						);
				}
				$i ++;
			}
		}
	}
	/**
	 * Test the JFormRuleInteger::test method.
	 *  @dataProvider provider
	 */
	public function provider()
	{
		
		return
		array(
			array('Positive'					=> '0','5', 'true'),
			array('Negative'					=> '0','-7', 'true'),
			array('0'							=> '0','0', 'true'),
			array('Blank'						=> '0','', 'true'),
			array('Characters'					=> '0','aaa', 'false'),
			array('Decimal'						=> '0','1.5', 'false'),
			array('Mixed'						=> '0','1aaa', 'false'),

			array('Positive'					=> '1','5', 'true'),
			array('Negative'					=> '1','-7', 'true'),
			array('0'							=> '1','0', 'true'),
			array('Blank'						=> '1','', 'true'),
			array('Characters'					=> '1','aaa', 'false'),
			array('Decimal'						=> '1','1.5', 'false'),
			array('Mixed'						=> '1','1aaa', 'false'),
			
			array('Positive'					=> '2','5', 'true'),
			array('Negative'					=> '2','-7', 'false'),
			array('0'							=> '2','0', 'false'),
			array('Blank'						=> '2','', 'true'),
			array('Characters'					=> '2','aaa', 'false'),
			array('Decimal'						=> '2','1.5', 'false'),
			array('Mixed'						=> '2','1aaa', 'false'),
			
			array('Positive'					=> '3','5', 'true'),
			array('Negative'					=> '3','-7', 'false'),
			array('0'							=> '3','0', 'true'),
			array('Blank'						=> '3','', 'true'),
			array('Characters'					=> '3','aaa', 'false'),
			array('Decimal' 					=> '3','1.5', 'false'),
			array('Mixed'						=> '3','1aaa', 'false'),
			
			
			array('Positive'					=> '4','5', 'false'),
			array('Negative'					=> '4','-7', 'true'),
			array('0'							=> '4','0', 'false'),
			array('Blank'						=> '4','', 'true'),
			array('Characters' 					=> '4','aaa', 'false'),
			array('Decimal' 					=> '4','1.5', 'false'),
			array('Mixed'						=> '4','1aaa', 'false'),
			
			array('Positive'					=> '5','5', 'true'),
			array('Negative'					=> '5','-7','true'),
			array('0'							=> '5','0', 'true'),
			array('Big number'					=> '5','200', 'false'),
			
			array('Positive'					=> '6','5', 'false'),
			array('Negative'					=> '6','-7','false'),
			array('0'							=> '6','0', 'false'),
			array('Big number'					=> '6','200', 'true'),
			
			array('Positive'					=> '7','5', 'true'),
			array('Negative'					=> '7','-7','true'),
			array('0'							=> '7','0', 'true'),
			array('Big number'					=> '7','200', 'false'),

			array('Positive'					=> '8','5', 'false'),
			array('Negative'					=> '8','-7','false'),
			array('0'							=> '8','0', 'false'),
			array('Blank'						=> '8','', 'true'),
			
			array('Positive'					=> '9','5', 'false'),
			array('Negative'					=> '9','-7','false'),
			array('0'							=> '9','0', 'false'),
			array('Blank'						=> '9','', 'true'),
			
			array('Positive'					=> '9','5', 'false'),
			array('Negative'					=> '9','-7','false'),
			array('0'							=> '9','0', 'false'),
			array('Blank'						=> '9','', 'true'),
			
		);
	}	
}