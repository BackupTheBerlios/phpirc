<?php
/**********************************************************************************
*     PhpIRC
*     /system/class/GUI.class.php
*     Version: $Id: GUI.class.php,v 1.1 2004/10/28 12:18:42 jcrawford Exp $
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

class GUI {

	/* an array to hold all of the created tabs.
	array(
	array(
	'type' => 1,
	'title' => 'title here'
	)
	);

	*/
	var $_tabs;

	// array to hold the different types of tabs
	var $_tabtypes;

	var $_parent;

	function GUI(&$parent) {
		$this->_tabtypes = 	array(
			0 => 'none',
			1 => 'Server',
			2 => 'Channel',
			3 => 'Query',
			4 => 'Chat',
			5 => 'Dcc'
		);

		$this->_tabs = array();

		$this->_parent = $parent;
	}

	function main() {
		if($this->_parent->_configs['serversonstart']) $this->serverList();
		$this->_parent->debug('In GUI::main()');
	}

	function serverList() {

		$w = &new GtkWindow();
		$w->set_usize('380', '240');
		$w->set_title("Networks");
		$w->connect("destroy", array('Actions', 'killwindow'), null);
		
		$scrolledwindow = &new GtkScrolledWindow();
		$scrolledwindow->set_policy(GTK_POLICY_NEVER, GTK_POLICY_ALWAYS);
		

		$tree = &new GtkCTree(1, 0);
		$networks = array();
		$servers = array();
		foreach($this->_parent->_networks as $network => $net) {
			$parent = $tree->insert_node(null,null,array($network),5,null,null,null,null,false,false);
			foreach($net['servers'] as $server) {
				$self = $tree->insert_node($parent,null,array($server['host'].":".$server['port']),5,null,null,null,null,false,false);
			}
		}
		$tree->set_line_style(GTK_CTREE_LINES_NONE);
		$tree->show();
		
		
		$panel = &new GtkVBox();
		$panel->set_border_width(5);
		$panel->set_usize(380, 240);
		
		$btnpanel = &new GtkHButtonBox();
		$chkbox = &new GtkHBox();
		$chkbox->set_usize(360, -1);
		
		$hbox = &new GtkHBox();
		$hbox->set_border_width(5);
		$hbox->set_usize(360, 160);
		
		$scrolledwindow->add($tree);
		$scrolledwindow->set_usize(360, -1);
		$hbox->pack_start($scrolledwindow, false, false, 2);
		
		$panel->pack_start($hbox, false, false, 2);
		$panel->pack_start($btnpanel, false, false, 2);
		$panel->pack_start($chkbox, false, false, 2);
		
		$button = &new GtkButton("Connect");
		$button1 = &new GtkButton("Add");
		$button2 = &new GtkButton("Remove");
		
		$serversOnStart = &new GtkCheckButton("Skip servers on startup");
		$chkbox->pack_start($serversOnStart, false, false, 2);
		
		$button->set_usize(80, -1);
		$button1->set_usize(80, -1);
		$button2->set_usize(80, -1);
		$btnpanel->pack_start($button, false, false, 2);
		$btnpanel->pack_start($button1, false, false, 2);
		$btnpanel->pack_start($button2, false, false, 2);

		$w->add($panel);
		$w->show_all();

	}
}
?>