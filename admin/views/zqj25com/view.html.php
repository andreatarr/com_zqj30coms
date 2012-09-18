<?php
defined('_JEXEC') or die;

/**
 * View to edit a contact.
 * 
 * @package	Joomla.Administrator
 * @subpackage	com_zqj30coms
 * @since 1.6
 */
class Zqj30comsViewZqj30com extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialize variables
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
		$this->state		= $this->get('State');
		
		// Check for errors
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		
		$user			= JFactory::getUser();
		$userId			= $user->get('id');
		$isNew			= ($this->item->id == 0);
		$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		// If you don't track these assets at the item level, use the category id.
		$canDo			= Zqj30comsHelper::getActions(
			$this->state->get('filter.category_id'), $this->item->id
		); 
		
		JToolBarHelper::title(JText::_('COM_ZQJ30COMS_MANAGER_ZQJ30COMS'), 'newsfeeds.png');
		
		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_zqj30coms', 'core.create')))) {
			JToolBarHelper::apply('zqj30com.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('zqj30com.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && (count($user->getAuthorisedCategories('com_zqj30coms', 'core.create')))) {
			JToolBarHelper::save2new('zqj30com.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_zqj30coms', 'core.create')) >0 )) {
			JToolBarHelper::save2copy('zqj30com.save2copy');
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('zqj30com.cancel', 'JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('zqj30com.cancel', 'JTOOLBAR_CLOSE');
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::help('','', JText::_('COM_ZQJ30COMS_SUBSCRIPTION_HELP_LINK'));
	}	
	
}