<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multselect.js', false, true);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_zqj30coms.category');
$saveOrder	= $listOrder == 'a.ordering';
?>
<div class="zqj30coms-list">
	<form action="<?php echo JRoute::_('index.php?option=com_zqj30coms&view=zqj30coms'); ?>" 
		method="post" name="adminForm" id="adminForm">	
		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_ZQJ30COMS_SEARCH_IN_TITLE'); ?>" />
				<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick=
	"document.id('filter_search').value='';this.form.submit();">
	<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			
			<div class="filter-select fltrt">

				<label class="selectlabel hidelabeltxt" for="filter_published">
					<?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?>
				</label>
				<select name="filter_published" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
				</select>
	
				<label class="selectlabel hidelabeltxt" for="filter_category_id">
					<?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?>
				</label>
				<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_zqj30coms'), 'value', 'text', $this->state->get('filter.category_id'));?>
				</select>
				
	            <label class="selectlabel hidelabeltxt" for="filter_access">
					<?php echo JText::_('JOPTION_SELECT_ACCESS'); ?>
				</label>			
	            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
				</select>
	
			</div>
		</fieldset>
		<div class="clr"> </div>
		
		<table class="adminlist">
			<thead>
				<tr>
					<th class="width-1">
						<input type="checkbox" name="checkall-toggle" value="" 
	onclick="checkAll(this)" />
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort',  'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th class="width-5">
						<?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
					
					<th class="width-20">
						<?php echo JHtml::_('grid.sort',  'JCATEGORY', 
	'category_title', $listDirn, $listOrder); ?>
					</th>
	
					<th class="width-20")>
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
						<?php if ($canOrder && $saveOrder) :?>
							<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'zqj30coms.saveorder'); ?>
						<?php endif; ?>
					</th>
	
					<th class="width-5">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
		
					<th class="width-1 nowrap">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		
				<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering = ($listOrder == 'a.ordering');
				$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_zqj30coms&task=edit&type=other&cid[]='. $item->catid);
				$canCreate = $user->authorise('core.create',		'com_zqj30coms.category.'.$item->catid);
				$canEdit = $user->authorise('core.edit',			'com_zqj30coms.category.'.$item->catid);
				$canCheckin = $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
				$canChange = $user->authorise('core.edit.state',	'com_zqj30coms.category.'.$item->catid) && $canCheckin;
				?>
		
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'zqj30coms.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_zqj30coms&task=zqj30com.edit&id='. (int) $item->id); ?>">
								<?php echo $this->escape($item->title); ?></a>
						<?php else : ?>
								<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
						<p class="smallsub">
							<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'zqj30coms.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->category_title); ?>
					</td>
					<td class="order">
						<?php if ($canChange) : ?>
							<?php if ($saveOrder) :?>
								<?php if ($listDirn == 'asc') : ?>
									<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'zqj30coms.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'zqj30coms.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php elseif ($listDirn == 'desc') : ?>
									<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'zqj30coms.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'zqj30coms.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php endif; ?>
							<?php endif; ?>
							<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
						<?php else : ?>
							<?php echo $item->ordering; ?>
						<?php endif; ?>
					</td>
							
					<td class="center">
						<?php echo $this->escape($item->access); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	
		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>
		
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
	
