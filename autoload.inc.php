<?php
/***********************************************************************
Copyright (C) 2012 Matyas Danter
Modifications by Daniel Morante Copyright (C) 2014

Permission is hereby granted, free of charge, to any person obtaining 
a copy of this software and associated documentation files (the "Software"), 
to deal in the Software without restriction, including without limitation 
the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES 
OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
OTHER DEALINGS IN THE SOFTWARE.
*************************************************************************/

/*
 * This file sets up class-loading and the environment
 * also tests whether GMP, BCMATH, or both are defined
 * if the GMP php extension exists it is preffered
 * because it is at least an order of magnitude faster
 */

function __autoload($f) {
    //load the interfaces first otherwise contract errors occur
    $interfaceFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "phpecc/classes/interface/" . $f . "Interface.php";

    if (file_exists($interfaceFile)) {
        require_once $interfaceFile;
    }

    //load class files after interfaces
    $classFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "phpecc/classes/" . $f . ".php";
    if (file_exists($classFile)) {
        require_once $classFile;
    }

    //if utilities are needed load them last
    $utilFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "phpecc/classes/util/" . $f . ".php";
    if (file_exists($utilFile)) {
        require_once $utilFile;
    }
}

$seconds = 7200;
set_time_limit($seconds);

if(extension_loaded('gmp') && !defined('USE_EXT')){
    define ('USE_EXT', 'GMP');
}else if(extension_loaded('bcmath') && !defined('USE_EXT')){
    define ('USE_EXT', 'BCMATH');
}
?>