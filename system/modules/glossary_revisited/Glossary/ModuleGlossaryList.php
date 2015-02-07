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

class ModuleGlossaryList extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_glossary_list';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### GLOSSARY LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->glossaries = deserialize($this->glossaries);

		// Return if there are no glossaries
		if (!is_array($this->glossaries) || count($this->glossaries) < 1)
		{
			return '';
		}
		
		if($this->objModel->navigationTpl != $this->strTemplate)
		{
			$this->strTemplate = $this->objModel->navigationTpl;
		}

		return parent::generate();
	}
	

	/**
	 * Generate module
	 */
	protected function compile()
	{
		$objTerm = null;
		
		// check get filter
		if(\Input::get('gl'))
		{
			$char = str_replace('gl', '', \Input::get('gl'));
			$objTerm = \Database::getInstance()->execute("SELECT * FROM tl_glossary_term WHERE published=1 AND pid IN(" . implode(',', array_map('intval', $this->glossaries)) . ") AND (term LIKE '".$char."%' OR term LIKE '".strtolower($char)."%')" . " ORDER BY term");
		}
		else
		{
			$objTerm = \Database::getInstance()->execute("SELECT * FROM tl_glossary_term WHERE published=1 AND pid IN(" . implode(',', array_map('intval', $this->glossaries)) . ")" . " ORDER BY term");
		}
		
		if ($objTerm->numRows < 1)
		{
			$this->Template->terms = array();
			return;
		}

		global $objPage;
		$this->import('String');
		$arrTerms = array();

		while ($objTerm->next())
		{
			$objTemplate = new \FrontendTemplate($this->strTemplate);
			$key = utf8_substr($objTerm->term, 0, 1);

			$objTemplate->term = $objTerm->term;
			$objTemplate->anchor = 'gl' . utf8_romanize($key);
			$objTemplate->id = standardize($objTerm->term);

			// Clean the RTE output
			if ($objPage->outputFormat == 'xhtml')
			{
				$objTerm->definition = $this->String->toXhtml($objTerm->definition);
			}
			else
			{
				$objTerm->definition = $this->String->toHtml5($objTerm->definition);
			}

			$objTemplate->definition = $this->String->encodeEmail($objTerm->definition);
			$objTemplate->addImage = false;
			$objTemplate->addGallery = false;
			
			$multiSRC = deserialize($objTerm->multiSRC);
			// handle multiple images
			if($objTerm->addImage && count($multiSRC) > 0)
			{
				if(count($multiSRC) > 1)
				{
					$objTemplate->addGallery = true;
				}
				else
				{
					$objTemplate->addImage = true;
					$objTerm->singleSRC = $multiSRC[0];
				}
			}

			// Add image (fallback)
			if ($objTemplate->addImage)
			{
				$objFile = \FilesModel::findByPk($objTerm->singleSRC);
				if(is_file(TL_ROOT . '/' . $objFile->path))
				{
					$row = $objTerm->row();
					$row['singleSRC'] = $objFile->path;
					$this->addImageToTemplate($objTemplate, $row);
				}
			}
			
			if($objTemplate->addGallery)
			{
				$objGallery = new \ContentGallery($objTerm);
				$objGallery->type = 'gallery';
				$objGallery->perRow = 1;
				$objTemplate->gallery = $objGallery->generate();
				
				$objTemplate->addBefore = ($objTerm->floating == 'below' ? false : true);
			}
			
			$objTemplate->enclosures = array();

			// Add enclosures
			if ($objTerm->addEnclosure)
			{
				$this->addEnclosuresToTemplate($objTemplate, $objTerm->row());
			}
			$arrTerms[$key][] = $objTemplate;
		}

		$this->Template->terms = $arrTerms;
		
		$this->Template->request = ampersand($this->Environment->request, true);
		$this->Template->topLink = $GLOBALS['TL_LANG']['MSC']['backToTop'];
	}
}

?>