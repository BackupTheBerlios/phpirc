<?php
/**********************************************************************************
*     PhpIRC
*     /system/class/IRC.class.php
*     Version: $Id: IRC.class.php,v 1.1 2004/10/28 12:18:42 jcrawford Exp $
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

class IRC {

	var $_running;
	
	/*
	array (
		'channels' => array(
			0 => '#php',
			1 => '#php-gtk'
		)
		'socket' => 'connection resource',
		'server' => 'irc.freenode.net',
		'port' => 6667,
		'nickname' => 'PhpBot',
		'realname' => 'PhpIRC',
		'ident' => 'ident',
		'hostname' => '10.0.0.8'
	);
	*/
	var $_servers;
	
	// holds the value of the next server connection
	var $_next_id;
	
	// this holds the reference to the parent object.
	var $_parent;
	
	var $_default_options;
	
	function IRC(&$parent) {
		$this->_next_id = 0;
		$this->_parent = $parent;
		$this->_default_options = array(
           'server'    => 'irc.freenode.net', 
           'port'      => 6667,
           'nickname'      => 'PhpIRC'.$this->randstr(), 
           'realname'  => 'PhpIRC - PHP-GTK IRC Client',
           'identd'    => 'phpirc',
           'host'      => '10.0.0.8',
		);
	}
	
	function addConnection($options) {
		$server_id = $this->_next_id;
		$this->_next_id++;

        /* Load default Option Values.. */
        foreach( $this->_default_options as $key => $value){
            if(!isset($options[$key])){
                $options[$key] = $value;
            }
        }
        $this->_servers[$server_id] = array(
        	'connected' => 0,
        	'channels' => array(),
        	'socket' => FALSE,
        	'server' => $options['server'],
        	'port' => $options['port'],
        	'nickname' => $options['nickname'],
        	'realname' => $options['realname'],
        	'ident' => $options['ident'],
        	'host' => $options['host'],
        );
		return $server_id;
	}
}		
?>