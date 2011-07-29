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
class JFormRuleIntegerTest extends JoomlaTestCase
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
					if ($expected == false){
						// Test fail conditions.
						$this->assertThat(
							$rule->test($xml->field[$i], $integer),
							$this->isFalse(),
							'Line:'.__LINE__.' The rule should return '.$expected.'.'
						);
					}
					if ($expected == true){
						// Test pass conditions.
						$this->assertThat(
							$rule->test($xml->field[$i], $integer),
							$this->isTrue(),
							'Line:'.__LINE__.' The rule should return '.$expected.'.'
						);
				}

			}
			$i++;
		}
	}
	/**
	 * Test the JFormRuleInteger::test method.
	 *
	 */
	public function provider()
	{
		
		return
		array(					// Value, field, expected
			'Positive integer'			=> array('0', '5', true),
			'+ sign integer'			=> array('0', '+5', true),
			'Negative integer'			=> array('0', '-7', true),
			'0 integer'					=> array('0', '0', true),
			'Blank integer'				=> array('0', '', true),
			'Characters integer'		=> array('0', 'aaa', false),
			'Decimal integer'			=> array('0', '1.5', false),
			'Mixed integer'				=> array('0', '1aaa', false),

			'Positive integerAll'			=> array('1', '5', true),
			'Negative integerAll'			=> array('1', '-7', true),
			'0 integerAll'					=> array('1', '0', true),
			'Blank integerAll'				=> array('1', '', true),
			'Characters integerAll'			=> array('1', 'aaa', false),
			'Decimal integerAll'			=> array('1', '1.5', false),
			'Mixed integerAll'				=> array('1', '1aaa', false),
			
			'Positive integerAll'			=> array('2', '5', true),
			'Negative integerPositive'			=> array('2', '-7', false),
			'0 integerPositive'					=> array('2', '0', false),
			'Blank integerPositive'				=> array('2', '', true),
			'Characters integerPositive'		=> array('2','aaa', false),
			'Decimal integerPositive'			=> array('2','1.5', false),
			'Mixed integerPositive'				=> array('2','1aaa', false),
			
			'Positive integerNonnegative'			=> array('3','5', true),
			'Negative integerNonnegative'			=> array('3','-7', false),
			'0 integerNonnegative'					=> array('3','0', true),
			'Blank integerNonnegative'				=> array('3','', true),
			'Characters integerNonnegative'			=> array('3','aaa', false),
			'Decimal integerNonnegative' 			=> array('3','1.5', false),
			'Mixed integerNonnegative'				=> array('3','1aaa', false),
			
			
			'Positive integerNegative'			=> array('4','5', false),
			'Negative integerNegative'			=> array('4','-7', true),
			'0 integerNegative'					=> array('4','0', false),
			'Blank integerNegative'				=> array('4','', true),
			'Characters integerNegative' 		=> array('4','aaa', false),
			'Decimal integerNegative' 			=> array('4','1.5', false),
			'Mixed integerNegative'				=> array('4','1aaa', false),
	
			'Positive integerMax'			=> array('5','5', true),
			'Negative integerMax'			=> array('5','-7',true),
			'0 integerMax'					=> array('5','0', true),
			'Big number integerMax'			=> array('5','200', false),
			
			'Positive integerMin'			=> array('6','5', false),
			'Negative integerMin'			=> array('6','-7',false),
			'0 integerMin'					=> array('6','0', false),
			'Big number integerMin'			=> array('6','200', true),
	
			'Positive integerComp_1'			=> array('7','5', true),
			'Negative integerComp_1'			=> array('7','-7',false),
			'0 integerComp_1'					=> array('7','0', false),
			'Big number integerComp_1'			=> array('7','200', false),

			'Positive integerComp_2'			=> array('8','5', false),
			'Negative integerComp_2'			=> array('8','-7',false),
			'0 integerComp_2'					=> array('8','0', false),
			'Blank integerComp_2'				=> array('8','', true),
	
			'Positive integerComp_3'			=> array('9','5', false),
			'Negative integerComp_3'			=> array('9','-7',false),
			'0  integerComp_3'					=> array('9','0', false),
			'Blank  integerComp_3'				=> array('9','', true),
	
			'Positive integerComp_4'			=> array('10','5', false),
			'Negative integerComp_4'			=> array('10','-7',false),
			'0  integerComp_4'					=> array('10','0', false),
			'Blank  integerComp_4'				=> array('10','', true)
			
		);
	}	
}