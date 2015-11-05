<?php
/**
 * This file is part of the Symfony2-coding-standard (phpcs standard)
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Thomas BEAUJEAN
 * @license  http://spdx.org/licenses/MIT MIT License
 * @version  GIT: master
 * @link     https://github.com/escapestudios/Symfony2-coding-standard
 */

/**
 * Symfony2_Sniffs_Constant_SelfSniff.
 *
 * Throws warnings if the self is used instead of static
 *
 * @category PHP
 * @package  PHP_CodeSniffer-Symfony2
 * @author   Thomas BEAUJEAN
 * @license  http://spdx.org/licenses/MIT MIT License
 * @link     https://github.com/escapestudios/Symfony2-coding-standard
 */
class Symfony2_Sniffs_Formatting_LocalVariableImmediatelyReturnedSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                  );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_RETURN);
    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        //if the return is a variable
        $functionEnd = false;
        $current = $stackPtr + 1;
        $variableReturned = false;
        $returnedName = null;
        while ($functionEnd === false) {
            if ($tokens[$current]['type'] === 'T_SEMICOLON') {
                $functionEnd = true;
            } elseif ($tokens[$current]['type'] === 'T_VARIABLE') {
                $variableReturned = true;
                $returnedName = $tokens[$current]['content'];
                $functionEnd = true;
            } elseif ($tokens[$current]['type'] !== 'T_WHITESPACE') {
                $functionEnd = true;
            }
            $current++;
        }

        //does the function contains more than once an assignation for this variable
        $count = 0;
        $current = $stackPtr;
        while ($tokens[$current]['type'] !== 'T_FUNCTION') {
            if ($tokens[$current]['type'] === 'T_VARIABLE'  && $returnedName === $tokens[$current]['content']) {
                $count++;
            }
            $current--;
        }

        //so raise error
        if ($variableReturned && $count === 1) {
            $phpcsFile->addError(
                'Local variables should not be declared and then immediately returned or thrown',
                $stackPtr
            );
        }

        return;
    }//end process()
}//end class
