<select size="30" style="float:left;display:block;border:none;width:40%;height:390px;">
	<?php if(count($areas) > 0): ?>
		<?php foreach($areas as $parent): ?>
		<option value="<?php echo $parent->areaSlug; ?>">
			<?php echo $parent->areaName; ?>
		</option>
		<?php if(isset($parent->children)): ?>
			<?php foreach($parent->children as $child): ?>
			<option value="<?php echo $child->areaSlug; ?>">
				-- <?php echo $child->areaName; ?>
			</option>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<option>No areas of contribution yet</option>
	<?php endif; ?>
</select>

<!-- forms -->
<div style="display:block;width:50%;height:358px;float:right;">
	<!-- tabs to switch between forms -->
	<div id="skill-form-tabs" class="tabs">
		<ul>
			<li rel="area-form-add" class="active">add</li>
			<li rel="area-form-edit">edit</li>
			<li rel="area-form-delete">delete</li>
		</ul>
	</div>
	
	<!-- add a area -->
	<form action="<?php echo $this->site_url('manage/area'); ?>" method="post" id="area-form-add" style="max-height:330px;overflow-y:auto;">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<label for="name">Name</label>
		<input type="text" name="name" id="area-add-name">
		<label for="slug">Slug</label>
		<input type="text" name="slug" id="area-add-slug">
		<label for="url">URL</label>
		<input type="text" name="url" id="area-add-url">
		<label for="description">Description</label>
		<textarea rows="10" cols="40" name="description" id="area-add-desciption"></textarea>
		
		<label for="parent">Parent</label>
		<!-- select parent -->
		<select name="parent" id="area-add-parent">
			<option value="root">No Parent</option>
			<?php if($parents->num_rows > 0): foreach($parents->results as $parent): ?>
			<option value="<?php echo $parent->areaSlug; ?>"><?php echo $parent->areaName; ?></option>
			<?php endforeach; endif;?>
		</select>
		
		<label for="tags">Tags</label>
		<input type="text" name="tags" id="area-add-tags">
		
		<label for="time">Minimum Time Requirement</label>
		<!-- select min time requirement -->
		<select name="time" id="area-add-time">
			<option value="null">---</option>
			<?php if($timeRequirements->num_rows > 0): foreach($timeRequirements->results as $timeRequirement): ?>
			<option value="<?php echo $timeRequirement->timeRequirementID; ?>"><?php echo $timeRequirement->timeRequirementShortDescription; ?></option>
			<?php endforeach; endif;?>
		</select>
		<div class="controls">
			<button type="submit" name="add" value="true" id="area-add">add</button>
		</div>
	</form>
	
	<!-- edit an existing area -->
	<form action="<?php echo $this->site_url('manage/area'); ?>" method="post" id="area-form-edit" style="display:none;max-height:330px;overflow-y:auto;">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<label for="name">Name</label>
		<input type="text" name="name" id="area-edit-name">
		<label for="slug">Slug</label>
		<input type="text" name="slug" id="area-edit-slug">
		<label for="url">URL</label>
		<input type="text" name="url" id="area-edit-url">
		<label for="description">Description</label>
		<textarea rows="10" cols="40" name="description" id="area-edit-desciption"></textarea>
		
		<label for="parent">Parent</label>
		<!-- select parent -->
		<select name="parent" id="area-edit-parent">
			<option value="root">No Parent</option>
			<?php if($parents->num_rows > 0): foreach($parents->results as $parent): ?>
			<option value="<?php echo $parent->areaSlug; ?>"><?php echo $parent->areaName; ?></option>
			<?php endforeach; endif;?>
		</select>
		
		<label for="tags">Tags</label>
		<input type="text" name="tags" id="area-edit-tags">
		
		<label for="time">Minimum Time Requirement</label>
		<!-- select min time requirement -->
		<select name="time" id="area-edit-time">
			<option value="null">---</option>
			<?php if($timeRequirements->num_rows > 0): foreach($timeRequirements->results as $timeRequirement): ?>
			<option value="<?php echo $timeRequirement->timeRequirementID; ?>"><?php echo $timeRequirement->timeRequirementShortDescription; ?></option>
			<?php endforeach; endif;?>
		</select>
		<div class="controls">
			<button type="submit" name="edit" value="true" id="area-edit">save</button>
		</div>
	</form>
	
	<!-- delete an existing skill -->
	<form style="display:none;" action="<?php echo $this->site_url('manage/tag'); ?>" method="post" id="area-form-delete">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<p>You are about to delete the skill "<strong id="area-delete-name">none selected</strong>"</p>
		<label for="password">Confirm Password</label>
		<input type="password" name="password" id="area-delete-password">
		<div class="controls">
			<button type="submit" name="delete" value="true" id="area-delete">delete</button>
		</div>
	</form>
</div>
<div class="clear">&nbsp;</div>