
<?php echo $this->Form->create('User', array('id'=>'register', 'role' => 'form', 'method'=> 'post', 'class' => 'form-register') ); ?>
	<div>
		<?php echo $this->Form->input('name', array( 'id' => 'name', 'type'=> 'text', 'class' =>'form-control', 'placeholder' => 'Name', 'div'=> false, 'label'=> false) ); ?>
		<span class="help-block"></span>
	</div>
	<div>
		<?php echo $this->Form->input('email', array( 'id' => 'email', 'type'=> 'email', 'class' =>'form-control', 'placeholder' => 'Email address', 'div'=> false, 'label'=> false) ); ?> 
		<span class="help-block"></span>
	</div>
	<div>
		<?php echo $this->Form->input('password', array( 'id' => 'password', 'type'=> 'password', 'class' =>'form-control', 'placeholder' => 'Password', 'div'=> false, 'label'=> false) ); ?> 
		<span class="help-block"></span>
	</div>
	<div>
		<?php echo $this->Form->input('confirm_password', array( 'id' => 'confirm_password', 'type'=> 'password', 'class' =>'form-control', 'placeholder' => 'Confirm Password', 'div'=> false, 'label'=> false) ); ?> 
		<span class="help-block"></span>
	</div>
	<?php echo $this->Form->button('Sign Up', array( 'id' => 'register_btn', 'type'=> 'submit', 'class' =>'btn btn-block bt-login') ); ?>

<?php echo $this->Form->end(); ?>
<div class="form-footer">
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<i class="fa fa-lock"></i>
			<a href="<?php echo BASE_PATH.'forget_password'; ?>"> Forgot password? </a>
		
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6">
			<i class="fa fa-check"></i>
			<a href="<?php echo BASE_PATH.'login'; ?>"> Sign In </a>
		</div>
	</div>
</div>

<?php echo $this->Html->script( array( 'register' )); ?>