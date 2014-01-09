<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		glossary_extended
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace Glossary;

/**
 * Class file
 * TableModule
 */
class TableModule extends \Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser','User');
	}
	
	/**
	 * Modify palette
	 * @param object
	 */
	public function modifyPalette(\DataContainer $objDC)
	{
		$objActiveRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);
		
		if($objActiveRecord->type == 'glossaryMenu')
		{
			$GLOBALS['TL_DCA'][$objDC->table]['fields']['navigationTpl']['label'] = &$GLOBALS['TL_LANG']['tl_module']['glossary_template'];
			$GLOBALS['TL_DCA'][$objDC->table]['fields']['navigationTpl']['options_callback'] = array('Glossary\TableModule','getMenuTemplates');
		}
		else if($objActiveRecord->type == 'glossaryList')
		{
			$GLOBALS['TL_DCA'][$objDC->table]['fields']['navigationTpl']['label'] = &$GLOBALS['TL_LANG']['tl_module']['glossary_template'];
			$GLOBALS['TL_DCA'][$objDC->table]['fields']['navigationTpl']['options_callback'] = array('Glossary\TableModule','getListTemplates');
		}
		else {}
	}
	
	/**
	 * Return all navigation templates as array
	 * @param DataContainer
	 * @return array
	 */
	public function getMenuTemplates(\DataContainer $objDC)
	{
		$intPid = $objDC->activeRecord->pid;

		if (\Input::get('act') == 'overrideAll')
		{
			$intPid = \Input::get('id');
		}

		return $this->getTemplateGroup('mod_glossary_menu', $intPid);
	}
	
	/**
	 * Return all list templates as array
	 * @param DataContainer
	 * @return array
	 */
	public function getListTemplates(\DataContainer $objDC)
	{
		$intPid = $objDC->activeRecord->pid;

		if (\Input::get('act') == 'overrideAll')
		{
			$intPid = \Input::get('id');
		}

		return $this->getTemplateGroup('mod_glossary_list', $intPid);
	}

}