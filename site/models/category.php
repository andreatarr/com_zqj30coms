<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.categories');

/**
 * Category Model
 * 
 * @package		Joomla.Site
 * @subpackage	com_zqj30coms
 * @since		1.5
 */
class Zqj30comsModelCategory extends JModelList
{
	/**
	 * Category items data
	 * 
	 * @var array
	 */
	protected $_item = null;
	
	/**
	 * Constructor.
	 * 
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		// list of allowed filter fields given here for security
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'g.title', 'group_title',
				'duration', 'a.duration'
			);
		}
		
		parent::__construct($config);
	}
	
	protected function getListQuery()
	{
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__zqj30_zqj30coms` AS a');
		$query->where('a.access IN ('.$groups.')');
		
		// Filter by category.
		if ($categoryId = $this->getState('category.id')) {
			$query->where('a.catid = '.(int) $categoryId);
			$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
			$query->where('c.access IN ('.$groups.')');

			//Filter by published category
			$cpublished = $this->getState('filter.c.published');
			if (is_numeric($cpublished)) {
				$query->where('c.published = '.(int) $cpublished);
			}
		}
		
		// Filter by state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.state = '.(int) $state);
		}
		
		// Filter by search
		if ($this->getState('list.filter') != '') {
			$filter = JString::strtolower($this->getState('list.filter'));
			$filter = $db->quote('%'.$filter.'%', true); // quote preps for SQL, true says to run the escape method as well
			$query->where('a.title LIKE ' . $filter);
		}

		// Filter by start and end dates. (note: quote method adds quotes around the field)
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSQL());

		if ($this->getState('filter.publish_date')){
			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
			$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.title')).
			' '.$db->getEscaped($this->getState('list.direction', 'ASC')));		
		
		return $query;
		
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();
		$params	= JComponentHelper::getParams('com_zqj30coms');

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'lim-it', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$orderCol	= JRequest::getCmd('filter_order', 'title');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);
		
		$this->setState('list.filter', JRequest::getString('filter-search'));

		$id = JRequest::getInt('id', 0);
		$this->setState('category.id', $id);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_zqj30coms')) &&  (!$user->authorise('core.edit', 'com_zqj30coms'))){
			// limit to published for people who can't edit or edit.state.
			$this->setState('filter.state',	1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}
	}
	
	public function getCategory()
	{
		if(!is_object($this->_item))
		{
			$categories = JCategories::getInstance('Zqj30coms');
			$this->_item = $categories->get($this->getState('category.id', 'root'));
		}

		return $this->_item;
	}
	

}