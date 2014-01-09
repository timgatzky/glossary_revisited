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
 * Add back end modules
 */
$GLOBALS['BE_MOD']['content']['glossary'] = array
(
	'tables' => array('tl_glossary', 'tl_glossary_term'),
	'icon'   => 'system/modules/glossary_extended/assets/img/icon.gif'
);


/**
 * Add front end modules
 */
array_insert($GLOBALS['FE_MOD'], 4, array
(
	'glossary' => array
	(
		'glossaryMenu' => 'Glossary\ModuleGlossaryMenu',
		'glossaryList' => 'Glossary\ModuleGlossaryList'
	)
));