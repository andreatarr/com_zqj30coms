<?php
defined('_JEXEC') or die;

/**
 * HTML View Class for item display
 */

class Zqj30comsViewZqj30com extends JViewLegacy
{
	protected $state;
	protected $item;
	
	function display($tpl= null)
	{
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		
		// Get some data from the model
		$item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Get the parameters
		$params = JComponentHelper::getParams('com_zqj30coms');
		
		if ($item) {
			// If we found an item, merge the item parameters
			$params->merge($item->params);
		}
			
		// check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		
		$this->params	= $params;
		$this->user		= $user;
		$this->item		= $item;
		
		$this->_prepareDocument();
		parent::display($tpl);
		
	}
	
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		
		$title	= null;
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu	= $menus->getActive();
		
		$head = JText::_('COM_ZQJ30COMS_VIEW_DEFAULT_TITLE');
		
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', $head);
		}

		$title = $this->params->def('page_title', $head);
		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) 
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) 
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
	}
}
