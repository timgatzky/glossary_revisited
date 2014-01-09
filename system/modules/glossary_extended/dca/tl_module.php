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

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('Glossary\TableModule', 'modifyPalette');

/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['glossaryMenu'] = '{title_legend},name,headline,type;{config_legend},glossaries,glossary_menu_filter;{template_legend:hide},navigationTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['glossaryList'] = '{title_legend},name,headline,type;{config_legend},glossaries;{template_legend:hide},navigationTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['glossaries'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['glossaries'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_glossary.title',
	'eval'                    => array('multiple'=>true, 'mandatory'=>true),
	'sql'                     => "text NULL"
);

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['glossary_menu_filter'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['glossary_menu_filter'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'sql'                     => "char(1) NOT NULL default ''"
);