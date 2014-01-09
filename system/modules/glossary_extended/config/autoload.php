<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Glossary
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Glossary'
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'Glossary\ModuleGlossaryMenu' 	=> 'system/modules/glossary_extended/Glossary/ModuleGlossaryMenu.php',
	'Glossary\ModuleGlossaryList' 	=> 'system/modules/glossary_extended/Glossary/ModuleGlossaryList.php',
	'Glossary\TableGlossaryTerm' 	=> 'system/modules/glossary_extended/Glossary/TableGlossaryTerm.php',
	'Glossary\TableModule' 			=> 'system/modules/glossary_extended/Glossary/TableModule.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_glossary_list' 			=> 'system/modules/glossary_extended/templates',
	'mod_glossary_menu' 			=> 'system/modules/glossary_extended/templates',
));
