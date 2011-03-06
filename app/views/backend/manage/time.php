<select size="30" style="float:left;display:block;border:none;width:40%;height:390px;">
	<?php if(count($timeRequirements->results) > 0): ?>
		<?php foreach($timeRequirements->results as $time): ?>
		<option value="<?php echo $time->timeRequirementID; ?>">
			<?php echo $time->timeRequirementShortDescription; ?>
		</option>
		<?php endforeach; ?>
	<?php else: ?>
		<option>No time requirements yet</option>
	<?php endif; ?>
</select>

<!-- forms -->
<div style="display:block;width:50%;height:358px;float:right;">
	<!-- tabs to switch between forms -->
	<div id="time-form-tabs" class="tabs">
		<ul>
			<li rel="time-form-add" class="active">add</li>
			<li rel="time-form-edit">edit</li>
			<li rel="time-form-delete">delete</li>
		</ul>
	</div>
	
	<!-- add time requirement -->
	<form action="<?php echo $this->site_url('manage/timeRequirement'); ?>" method="post" id="time-form-add">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<label for="short">Short Description</label>
		<input type="text" name="short id="time-add-short">
		<label for="long">Long Description</label>
		<textarea name="long" id="time-add-long" cols="40" rows="10"></textarea>
		<div class="controls">
			<button type="submit" name="add" value="true">add</button>
		</div>
	</form>
	
	<!-- edit time requirement -->
	<form style="display:none;" action="<?php echo $this->site_url('manage/timeRequirement'); ?>" method="post" id="time-form-edit">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<label for="short">Short Description</label>
		<input type="text" name="short" id="time-edit-short">
		<label for="long">Long Description</label>
		<textarea name="long" id="time-edit-long" cols="40" rows="10"></textarea>
		<div class="controls">
			<button type="submit" name="edit" value="true" id="time-edit">save</button>
		</div>
	</form>
	
	<!-- delete time requirement -->
	<form style="display:none;" action="<?php echo $this->site_url('manage/timeRequirement'); ?>" method="post" id="time-form-delete">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<p>You are about to delete the time requirement "<strong id="time-delete-short">none selected</strong>"</p>
		<label for="password">Confrim Password</label>
		<input type="password" name="password" id="time-delete-password">
		<div class="controls">
			<button type="submit" name="delete" value="true" id="time-delete">confirm</button>
		</div>
	</form>
</div>
<div class="clear">&nbsp;</div>