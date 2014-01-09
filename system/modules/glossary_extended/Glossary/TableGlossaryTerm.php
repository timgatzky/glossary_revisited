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
 * TableGlossaryTerm
 */
class TableGlossaryTerm extends \Backend
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
	 * Return the "toggle visibility" button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$objInput = \Input::getInstance();
		if (strlen($objInput->get('tid')))
		{
			$this->toggleVisibility($objInput->get('tid'), ($objInput->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_glossary_term::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}
	
	/**
	 * Disable/enable a user group
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnVisible)
	{
		// Check permissions to edit
		$objInput = \Input::getInstance();
		$objInput->setGet('id', $intId);
		$objInput->setGet('act', 'toggle');
		#$this->checkPermission();

		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_glossary_term::published', 'alexf'))
		{
			$this->log('Not enough permissions to publish/unpublish item ID "'.$intId.'"', 'tl_glossary_term toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new \Versions('tl_glossary_term', $intId);
		$objVersions->initialize();
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_glossary_term']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_glossary_term']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}
		
		// Update the database
		\Database::getInstance()->prepare("UPDATE tl_glossary_term SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);

		$objVersions->create();
		
		$this->log('A new version of record "tl_glossary_term.id='.$intId.'" has been created', 'tl_revolutionslider_slides toggleVisibility()', TL_GENERAL);

	}

	/**
	 * Modify palette
	 * @param object
	 */
	public function modifyPalette(\DataContainer $objDC)
	{
		$objActiveRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);
		
		// toggle between gallery mode or single image mode
		$multiSRC = deserialize($objActiveRecord->multiSRC);
		// gallery mode
		if(count($multiSRC) > 1)
		{
			$GLOBALS['TL_DCA'][$objDC->table]['subpalettes']['addImage'] = 'multiSRC,sortBy,size,imagemargin,fullsize';
		}
	}

}