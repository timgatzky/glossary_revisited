<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Leo Feyer, Tim Gatzky 
 * @author		Leo Feyer <http://www.contao.org>, Tim Gatzky <info@tim-gatzky.de>
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
 * ModuleGlossaryMenu
 */
class ModuleGlossaryMenu extends \Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_glossary_menu';
	
	/**
	 * Database result
	 */
	protected $objResult = null;


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### GLOSSARY MENU ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		$this->strTemplate = $this->navigationTpl;
		$this->glossaries = deserialize($this->glossaries);

		// Return if there are no glossaries
		if (!is_array($this->glossaries) || count($this->glossaries) < 1)
		{
			return '';
		}
		
		// fetch entries
		$objTerm = \Database::getInstance()->execute("SELECT * FROM tl_glossary_term WHERE published=1 AND pid IN(" . implode(',', array_map('intval', $this->glossaries)) . ")" . " ORDER BY term");
		if ($objTerm->numRows < 1)
		{
			return '';
		}
		
		$this->objResult = $objTerm;
		
		return parent::generate();
	}
	

	/**
	 * Generate module
	 */
	protected function compile()
	{
		$objTerm = $this->objResult;

		if ($objTerm->numRows < 1)
		{
			return '';
		}

		$arrAnchor = array();
		$arrLinks = array();

		while ($objTerm->next())
		{
			$link = utf8_substr($objTerm->term, 0, 1);
			$key = 'gl' . utf8_romanize($link);
			
			$arrAnchor[$key] = $link;
			
			$arrLinks[$key] = array('link'=>$link);
			if($this->glossary_menu_filter)
			{
				$href .= '&amp;gl='.$key;
				$arrLinks[$key]['href'] = $this->addToUrl($href);
			}
			else
			{
				$arrLinks[$key]['href'] = ampersand($this->Environment->request, true).'#'.$key;
			}
			
			if(\Input::get('gl') == $key)
			{
				$arrLinks[$key]['active'] = 1;
			}
		}

		$this->Template->request = ampersand($this->Environment->request, true);
		$this->Template->anchors = $arrAnchor;
		$this->Template->links = $arrLinks;
	}
}

?>