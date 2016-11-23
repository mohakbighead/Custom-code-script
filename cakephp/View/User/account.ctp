
	<div class="container">
     	<div class="col-md-8 col-sm-8 col-xs-12">
     		<h1>My Account:</h1><br>
			<?php echo $this->Form->create('User', array('id'=>'account', 'role' => 'form', 'method'=> 'post', 'class' => 'form-horizontal myaccount') ); ?>
				<div class="form-group">
					<span for="inputEmail3" class="col-sm-4 control-span">Name</span>
					<div class="col-sm-8">
						<p> <?php echo isset( $this->request->data['name']) ? $this->request->data['name'] : ''; ?> </p>
					</div>
				</div>
				<div class="form-group">
					<span for="inputPassword3" class="col-sm-4 control-span">Email</span>
					<div class="col-sm-8">
						<p> <?php echo $this->request->data['email']; ?> </p>
					</div>
				</div>
				<hr>
				<div class="form-group">
					<span for="inputPassword3" class="col-sm-4 control-span">Current Password</span>
					<div class="col-sm-8">
						<?php echo $this->Form->input('old_password', array( 'id' => 'old_password', 'type'=> 'password', 'placeholder' => 'Current Password', 'div'=> false, 'label'=> false) ); ?>
						<span class="help-block"></span>
					</div>
				</div>
				
				<div class="form-group">
					<span for="inputPassword3" class="col-sm-4 control-span"> New Password</span>
					<div class="col-sm-8">
						<?php echo $this->Form->input('password', array( 'id' => 'password', 'type'=> 'password', 'placeholder' => 'New Password', 'div'=> false, 'label'=> false) ); ?>
						<span class="help-block"></span>
					</div>
				</div>
				<div class="form-group">
					<span for="inputPassword3" class="col-sm-4 control-span"> Confirm Password</span>
					<div class="col-sm-8">
						<?php echo $this->Form->input('confirm_password', array( 'id' => 'confirm_password', 'type'=> 'password', 'placeholder' => 'Confirm New Password', 'div'=> false, 'label'=> false) ); ?>
						<span class="help-block"></span>
					</div>
				</div>
				<?php echo $this->Form->input('id', array('value'=> $this->request->data['id'], 'id' => 'id', 'type'=> 'hidden')); ?>
				<?php echo $this->Form->input('email', array('value'=> $this->request->data['email'], 'id' => 'email', 'type'=> 'hidden')); ?>
				
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-default">Change Password</button>
					</div>
				</div>
			<?php echo $this->Form->end(); ?>
		</div>
		<?php echo $this->element('sidebar'); ?>
	</div>
<?php echo $this->Html->script( array( 'account' )); ?>