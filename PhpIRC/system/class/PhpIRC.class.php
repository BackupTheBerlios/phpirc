<?php
/**********************************************************************************
*     PhpIRC
*     /system/class/PhpIRC.class.php
*     Version: $Id: PhpIRC.class.php,v 1.1 2004/10/28 12:18:41 jcrawford Exp $
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

// include the class files for the system objects.
include_once('GUI.class.php');
include_once('Actions.class.php');
include_once('IRC.class.php');

class PhpIRC {
	var $_version;
	var $_user;
	var $_userHome;
	var $_configs;
	var $_configDir;
	var $_systemDir;
	var $_sysConfigDir;
	var $_debugging;
	var $_language;
	var $_os;
	var $_networks;

	// The following properties hold the objects for this application.
	var $_GUI;
	var $_Actions;
	var $_IRC;

	function PhpIRC() {
		$this->_version = '0.0.1';
		$this->_user = $_ENV['USER'];
		$this->_userHome = $_ENV['HOME'];
		$this->_configs = array();
		$this->_configDir = $this->_userHome.'/.PhpIRC/configs';
		$this->_systemDir = BASE_PATH.'/system';
		$this->_sysConfigDir = $this->_systemDir.'/configs';
		$this->_debugging = TRUE;
		$this->_language = 'en';
		$this->_os = strtoupper(substr(PHP_OS, 0, 3));
		$this->_networks = array();

		// if the users os is linux dont allow them to IRC as root or in super user mode
		if($this->_os == "LIN") {
			// do not allow the user to IRC using the root account
			if($this->isRootUser()) {
				$this->error("You cannot run PhpIRC while logged in as root, please login as another user and run PhpIRC again.");
			} elseif($this->isSuperUser()) {
				$this->error("You cannot run PhpIRC when you are in super user mode!");
			}
		}

		// show the startup message in console.
		$this->console("Starting PhpIRC v".$this->_version);

		// fire up the application.
		$this->Startup();
		
		// instanciate the objects.
		
		//$this->_IRC = new IRC($this);
		$this->_Actions = new Actions();
		$this->_GUI = new GUI($this);
		
		$this->_GUI->main();
	}

	function isRootUser() {
		if(strtolower($this->_user) == 'root') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function isSuperUser() {
		if(strstr($this->_userHome, $this->_user)) {
			return FALSE;
		} else {
			// the username was not found in the users home directory.
			return TRUE;
		}
	}

	function InitialSetup() {
		if (is_dir($this->_sysConfigDir)) {
			if (!is_dir($this->_userHome.'/.PhpIRC/')) {
				$this->debug("Creating Directories.");
				mkdir($this->_userHome."/.PhpIRC/") or $this->error("Cannot Create Directory: ".$this->_userHome."/.PhpIRC/");
				mkdir($this->_userHome."/.PhpIRC/configs/") or $this->error("Cannot Create Directory: ".$this->_userHome."/.PhpIRC/configs/");
				$dir = opendir($this->_sysConfigDir) or $this->error("Cannot Open ".$this->_sysConfigDir."!");
				$this->debug('Copying Files.');
				while (false !== ($file = readdir($dir))) {
					if($file != '.' && $file != '..') {
						copy($this->_sysConfigDir.'/'.$file, $this->_configDir."/".$file) or $this->error("Error copying ".$file."!");
					}
				}
				$this->debug("Copying Complete.");
				//return TRUE;
			}
		}
	}

	function LoadConfigs() {
		// read the file
		$this->debug("Loading ".$this->_user."'s Configs.");
		$lines = file($this->_configDir."/main.conf") or $this->error("Cannot Open ".$this->_configDir."/main.conf");
		// loop through each line in the file
		foreach($lines as $line) {
			// break the line into var/val
			list($var, $val) = explode("=", $line);
			// trim whitespace
			$var = trim($var);
			$val = trim($val);

			// set the default ident if none is set in the config
			if(($var == "ident") && ($val == "")) {
				$this->debug("Ident Not Set, Using Default: ".$this->_user);
				$val = $this->_user;
			}
			// load this config into the array
			$this->_configs[$var] = $val;
		}
	}

	function LoadServers() {
		$this->debug("Loading Networks.");
		$lines = file($this->_configDir.'/servers.conf') or $this->debug("Could not open server file.");
		$network = null;
		$net = array();

		foreach($lines as $line) {
			$line = trim($line);
			if((($line == '')) || ($line{0} == '#')) {
				continue;
			} elseif($line{0} == '[') {
				$network = substr($line, 1, -1);
				$this->debug("");
				$this->debug("Loading Network: ".$network);
			} else {
				$ln = explode("=", $line);
				switch($ln[0]) {
					case "J":
					$this->_networks[$network]['ajoin'] = trim($ln[1]);
					$this->debug("Loading Nickserv Password");
					break;
					case "B":
					$this->_networks[$network]['nickserv'] = trim($ln[1]);
					break;
					case "S":
					$sinfo = explode(":", $ln[1]);
					$server = array(
					'host' => trim($sinfo[0]),
					'port' => trim($sinfo[1])
					);
					$this->debug("Loading Server: ".$server['host']." with port: ".$server['port']);
					$this->_networks[$network]['servers'][] = $server;
					break;
					case "L":
					$this->_networks[$network]['ssl'] = trim($ln[1]);
					$this->debug("Loading SSL Mode.");
					break;
				}
			}
		}
		$this->debug("");
	}

	function debug($msg) {
		if($this->_debugging == TRUE) {
			echo "[ Debug ] - ".$msg."\r\n";
		}
	}

	function console($msg) {
		echo "[  Msg  ] - ".$msg."\r\n";
	}

	function error($msg) {
		echo "[ Error ] - ".$msg."\r\n";
		exit();
	}

	function Startup() {
		$this->InitialSetup();
		$this->LoadConfigs();
		$this->LoadServers();

	}
}
?>