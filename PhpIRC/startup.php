<?php
/**********************************************************************************
*     PhpIRC
*     /startup.php
*     Version: $Id: startup.php,v 1.1 2004/10/28 12:18:41 jcrawford Exp $
*     Copyright (c) 2004, The PhpIRC Development Team

*     Permission is hereby granted, free of charge, to any person obtaining
*     a copy of this software and associated documentation files (the
*     "Software"), to deal in the Software without restriction, including
*     without limitation the rights to use, copy, modify, merge, publish,
*     distribute, sublicense, and/or sell copies of the Software, and to
*     permit persons to whom the Software is furnished to do so, subject to
*     the following conditions:

*     The above copyright notice and this permission notice shall be
*     included in all copies or substantial portions of the Software.

*     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
*     EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
*     MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
*     NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
*     BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
*     ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
*     CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*     SOFTWARE.
*********************************************************************************/

// get the current path for this file
define("BASE_PATH", dirname(__FILE__));


// this is for development only, turn all errors off on release
error_reporting(E_ALL);

// check to see if the GTK extension is loaded, if not load it.
if (!extension_loaded('gtk')) {
	dl( 'php_gtk.' . PHP_SHLIB_SUFFIX);
}

// include the core of this system
include_once('system/class/PhpIRC.class.php');

// start the system
$PhpIRC = new PhpIRC();

include_once($PhpIRC->_systemDir.'/locale/'.$PhpIRC->_language.'/language.php');
?>