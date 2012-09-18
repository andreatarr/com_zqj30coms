<?php
defined('_JEXEC') or die;

// Code to support edit links for zqj30coms
// Create a shortcut for params.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::core();

// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on permissions.
$canEdit = $user->authorise('core.edit', 'com_zqj30coms.category.' . $this->category->id);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$listFilter = $this->state->get('list.filter');
?>

<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_ZQJ30COMS_NO_ZQJ30COMS'); ?></p>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" 
	method="post" name="adminForm" id="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	<div class="filter-search">
		<label class="filter-search-lbl" for="filter-search"
			><?php echo JText::_('COM_ZQJ30COMS_FILTER_LABEL').'&#160;'; ?></label>
		<input type="text" name="filter-search" id="filter-search" 
			value="<?php echo $this->escape($this->state->get('list.filter')); ?>" 
			class="inputbox" onchange="document.adminForm.submit();" 
			title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
	</div>	
<div class="display-limit">
	<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
	<?php echo $this->pagination->getLimitBox(); ?>
</div>
</fieldset>

	<table class="category">
		<thead><tr>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  'COM_ZQJ30COMS_GRID_TITLE', 
					'a.title', $listDirn, $listOrder); ?>
			</th>

	</tr></thead>

	<tbody>
	<?php foreach ($this->items as $i => $item) : ?>
		<tr class="cat-list-row<?php echo $i % 2; ?>" >
		<td class="title">
				<a href="<?php echo JRoute::_('index.php?option=com_zqj30coms&amp;view=zqj30com&amp;id='.$item->id); ?>">
				<?php echo $item->title; ?></a>
		</td>

		</tr>
	<?php endforeach; ?>
</tbody>
</table>

<div class="pagination">
	<p class="counter">
	<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</div>
</form>
<?php endif; ?>


