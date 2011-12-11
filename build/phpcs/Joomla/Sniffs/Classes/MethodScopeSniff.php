<?php
/**
 * Verifies that class members have scope modifiers.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: MethodScopeSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if(class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false)
{
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found');
}

/**
 * Verifies that class members have scope modifiers.
 *
 * Example:
 * class Foo
 * {
 *     <b class="good">public function foo()</b>
 *     {
 *     }
 *
 *     <b class="bad">function foo()</b>
 *     {
 *     }
 * }
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Joomla_Sniffs_Classes_MethodScopeSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * Constructs a Squiz_Sniffs_Scope_MethodScopeSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION));
    }//function

    /**
     * Processes the function tokens within the class.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param integer                  $stackPtr  The position where the token was found.
     * @param integer                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        if($methodName === null)
        {
            // Ignore closures.
            return;
        }

        $modifier = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$scopeModifiers, $stackPtr);

        if(($modifier === false) || ($tokens[$modifier]['line'] !== $tokens[$stackPtr]['line']))
        {
            $error = sprintf('No scope modifier specified for function "%s"'
            , $methodName);

            $phpcsFile->addWarning($error, $stackPtr, 'Missing');
        }
    }//function
}//class
