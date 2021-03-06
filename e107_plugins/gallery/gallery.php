<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2012 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Cron Administration
 *
 * $URL: https://e107.svn.sourceforge.net/svnroot/e107/trunk/e107_0.8/e107_admin/cron.php $
 * $Id: cron.php 12492 2011-12-30 16:09:10Z e107steved $
 *
 */

/**
 *
 * @package     e107
 * @subpackage	frontend
 * @version     $Id: cron.php 12492 2011-12-30 16:09:10Z e107steved $
 *	Ultra-simple Image-Gallery
 */
 

require_once("../../class2.php");
if (!plugInstalled('gallery'))
{
	header('location:'.e_BASE.'index.php');
	exit;
}


require_once(HEADERF);

class gallery
{
	private $catList = array();
	
	function __construct()
	{
		$this->catList = e107::getMedia()->getCategories('gallery');
		
		if((vartrue($_GET['cat'])) && isset($this->catList[$_GET['cat']]))
		{
			$this->showImages($_GET['cat']);	
		}
		else
		{
			$this->listCategories();		
		}
	}
	
	function listCategories()
	{
		$template 	= e107::getTemplate('gallery');	
		$sc 		= e107::getScBatch('gallery',TRUE);
		
		$text = "";		
		
		
		$text = e107::getParser()->parseTemplate($template['CAT_START'],TRUE, $sc);
		
		foreach($this->catList as $val)
		{
			$sc->setVars($val);	
			$text .= e107::getParser()->parseTemplate($template['CAT_ITEM'],TRUE, $sc);
		}	
		
		$text .= e107::getParser()->parseTemplate($template['CAT_END'],TRUE, $sc);
		
		e107::getRender()->tablerender(LAN_PLUGIN_GALLERY_TITLE, $text);
	}
	

	function showImages($cat)
	{
		$mes 		= e107::getMessage();	
		$tp			= e107::getParser();			
		$template 	= e107::getTemplate('gallery');
		$sc 		= e107::getScBatch('gallery',TRUE);
					
		$sc->total 	= e107::getMedia()->countImages($cat);
		$sc->amount = 12; // TODO Add Pref. amount per page. 
		$sc->curCat = $cat;
		$sc->from 	= ($_GET['frm']) ? intval($_GET['frm']) : 0;
		
		$list 		= e107::getMedia()->getImages($cat,$sc->from,$sc->amount);
		$catname	= $tp->toHtml($this->catList[$cat]['media_cat_title'],false,'defs');
	
		$inner = "";	
		
		foreach($list as $row)
		{
			$sc->setVars($row);	
			$inner .= $tp->parseTemplate($template['LIST_ITEM'],TRUE, $sc);
		}
					
		$text = $tp->parseTemplate($template['LIST_START'],TRUE, $sc);
		$text .= $inner; 	
		$text .= $tp->parseTemplate($template['LIST_END'],TRUE, $sc);
		
		e107::getRender()->tablerender(LAN_PLUGIN_GALLERY_TITLE, $mes->render().$text);
		
	}
	
}


new gallery;

require_once(FOOTERF);
exit;


?>