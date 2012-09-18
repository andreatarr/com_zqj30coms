<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of items
 */
class Zqj30comsViewZqj30coms extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	/**
	 * Display the view
	 */
	public function display ($tpl = null) 
	{
		$this->state			= $this->get('State');
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 * 
	 * @since 1.6
	 */
	protected function addToolbar()
	{
		JLoader::register('Zqj30comsHelper', JPATH_COMPONENT.'/helpers/zqj30coms.php');
		
		$state	= $this->get('State');
		$canDo	= Zqj30comsHelper::getActions($state->get('filter.category_id'));
		$user	= JFactory::getUser();
		
		JToolBarHelper::title(JText::_('COM_ZQJ30COMS_MANAGER_ZQJ30COMS'), 'newsfeeds.png');
		
		if (count($user->getAuthorisedCategories('com_zqj30coms', 'core.create')) > 0) {
			JToolBarHelper::addNew('zqj30com.add','JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('zqj30com.edit','JTOOLBAR_EDIT');
		}
		
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('zqj30coms.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('zqj30coms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('zqj30coms.archive','JTOOLBAR_ARCHIVE');
			JToolBarHelper::checkin('zqj30coms.checkin');
		}
		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'zqj30coms.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('zqj30coms.trash');
			JToolBarHelper::divider();
		} 
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_zqj30coms');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help('', '',JText::_('COM_ZQJ30COMS_ZQJ30COMS_HELP_LINK'));
	}
}