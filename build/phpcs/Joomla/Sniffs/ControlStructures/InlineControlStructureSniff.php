<?php
/**
 * Joomla_Sniffs_ControlStructures_InlineControlStructureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: InlineControlStructureSniff.php 258843 2008-05-01 00:49:32Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Joomla_Sniffs_ControlStructures_InlineControlStructureSniff.
 *
 * Verifies that inline control statements are not present.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Joomla_Sniffs_ControlStructures_InlineControlStructureSniff extends Generic_Sniffs_ControlStructures_InlineControlStructureSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_IF,
                T_ELSE,
                T_FOREACH,
                T_WHILE,
                T_DO,
                T_SWITCH,
                T_FOR,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            // Ignore the ELSE in ELSE IF. We'll process the IF part later.
            if (($tokens[$stackPtr]['code'] === T_ELSE) && ($tokens[($stackPtr + 2)]['code'] === T_IF)) {
                return;
            }

            if ($tokens[$stackPtr]['code'] === T_WHILE) {
                // This could be from a DO WHILE, which doesn't have an opening brace.
                $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if ($tokens[$lastContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                    $brace = $tokens[$lastContent];
                    if (isset($brace['scope_condition']) === true) {
                        $condition = $tokens[$brace['scope_condition']];
                        if ($condition['code'] === T_DO) {
                            return;
                        }
                    }
                }
            }

            // This is a control structure without an opening brace,
            // so it is an inline statement.
            if ($this->error === true) {
                $phpcsFile->addError('Inline control structures are not allowed', $stackPtr, 'NotAllowed');
            } else {
                $phpcsFile->addWarning('Inline control structures are discouraged', $stackPtr, 'Discouraged');
            }

            return;
        }//end if

    }//end process()


}//end class

?>
