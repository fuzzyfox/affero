<select size="30" style="float:left;display:block;border:none;width:40%;height:390px;">
	<?php if(count($skills) > 0): ?>
		<?php foreach($skills as $skill): ?>
		<option value="<?php echo $skill->skillTag; ?>">
			<?php echo $skill->skillName; ?>
		</option>
		<?php endforeach; ?>
	<?php else: ?>
		<option>No skills yet</option>
	<?php endif; ?>
</select>

<!-- forms -->
<div style="display:block;width:50%;height:358px;float:right;">
	<!-- tabs to switch between forms -->
	<div id="skill-form-tabs" class="tabs">
		<ul>
			<li rel="skill-form-add" class="active">add</li>
			<li rel="skill-form-edit">edit</li>
			<li rel="skill-form-delete">delete</li>
		</ul>
	</div>
	
	<!-- add a skill -->
	<form action="<?php echo $this->site_url('manage/tag'); ?>" method="post" id="skill-form-add">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<label for="name">Name</label>
		<input type="text" name="name" id="skill-add-name">
		<label for="slug">Slug</label>
		<input type="text" name="slug" id="skill-add-slug">
		<div class="controls">
			<button type="submit" name="add" value="true" id="skill-add">add</button>
		</div>
	</form>
	
	<!-- edit an existing skill -->
	<form style="display:none;" action="<?php echo $this->site_url('manage/tag'); ?>" method="post" id="skill-form-edit">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<label for="name">Name</label>
		<input type="text" name="name" id="skill-edit-name">
		<label for="slug">Slug</label>
		<input type="text" name="slug" id="skill-edit-slug">
		<div class="controls">
			<button type="submit" name="edit" value="true" id="skill-edit">save</button>
		</div>
	</form>
	
	<!-- delete an existing skill -->
	<form style="display:none;" action="<?php echo $this->site_url('manage/tag'); ?>" method="post" id="skill-form-delete">
		<input type="hidden" name="token" value="<?php echo $_SESSION['user']['token']; ?>">
		<p>You are about to delete the skill "<strong id="skill-delete-name">none selected</strong>"</p>
		<label for="password">Confirm Password</label>
		<input type="password" name="password" id="skill-delete-password">
		<div class="controls">
			<button type="submit" name="delete" value="true" id="skill-delete">delete</button>
		</div>
	</form>
</div>
<div class="clear">&nbsp;</div>