<?php
defined('_JEXEC') or die;

/**
 * Methods supporting a list of records.
 */
class Zqj30comsModelZqj30coms extends JModelList
{
	
	/**
	 * Constructor.
	 * 
	 * @param	array	An optional associative array of configuration settings
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{	// @TODO Fill in fieldnames
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}
		
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 * 
	 * Note. Calling getState in this method will result in recursion.
	 * 
	 * @since 1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialize variables.
		$app = JFactory::getApplication('administrator');
		
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);
		
		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);
		
		// Load the parameters
		$params = JComponentHelper::getParams('com_zqj30coms');
		$this->setState('params', $params);
		
		// List state information.
		parent::populateState('a.title', 'asc');
	}
	
	/**
	 * Method to get a store id based on model connfiguration state.
	 * 
	 * This is necessary because the model is used by the component and 
	 * a different modules that might need different sets of data or
	 * different ordering requirements.
	 * 
	 * @param	string	$id		A prefix for the store id.
	 * @return	string			A store id
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.access');
		$id.= ':' . $this->getState('filter.state');
		$id.= ':' . $this->getState('filter.category_id');
		
		return parent::getStoreId($id);
		
	}
	
	/**
	 * Build an SQL query to load the list data.
	 * 
	 * @return	JDatabaseQuery
	 * @since 	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	=	$db->getQuery(true);
		$user	= JFactory::getUser();
		
		// Select the required fields from the table. @TODO Fill in field names
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.description, a.checked_out, a.checked_out_time,' .
				' a.catid, a.state, a.access, a.ordering,' .
				'a.publish_up, a.publish_down'
			)
		);
		$query->from($db->quoteName('#__zqj30_zqj30coms').' AS a');
		
		// Join over the users for the checked out user
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');	
		
		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' .(int) $access);
		}
		
		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}
		
		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid ='.(int) $categoryId);
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id ='.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}
		
		// Add the list_ordering clause
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
		
	}
}