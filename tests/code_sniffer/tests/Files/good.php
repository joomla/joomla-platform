<?php
/**
 * Joomla! Coding Standards checker.
 *
 * This file contains all the valid notations for the Joomla! coding standard.
 * Target is to create a style checker that validates all of this constructs.
 *
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/* Standard: No parentheses are required for statements.
(Joomla.Files.IncludingFile.BracketsNotRequired)
*/
include $foo;
include_once $foo;
require $foo;
require_once $foo;
/* Standard: File is being unconditionally included; use "require" instead
(Joomla.Files.IncludingFile.UseRequire)
*/
require $foo;
/* Standard: File is being unconditionally included; use "require_once" instead
 (Joomla.Files.IncludingFile.UseRequireOnce)
*/
require_once $foo;
