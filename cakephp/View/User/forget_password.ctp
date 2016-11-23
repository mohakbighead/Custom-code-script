
<?php echo $this->Form->create('User', array('id'=>'forgetpassword', 'role' => 'form', 'method'=> 'post', 'class' => 'form-register') ); ?>
	<div>
		<?php echo $this->Form->input('email', array( 'id' => 'email', 'type'=> 'email', 'class' =>'form-control', 'placeholder' => 'Email address', 'div'=> false, 'label'=> false) ); ?>  
		<span class="help-block"></span>
	</div>
	<?php echo $this->Form->button('Reset Password', array( 'id' => 'password_btn', 'type'=> 'submit', 'class' =>'btn btn-block bt-login') ); ?>
<?php echo $this->Form->end(); ?>
<div class="form-footer">
	<div class="row">
		<div class="col-xs-6 col-sm-6 col-md-6">
			<i class="fa fa-lock"></i>
			<a href="<?php echo BASE_PATH.'login'; ?>"> Sign In </a>
		
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6">
			<i class="fa fa-check"></i>
			<a href="<?php echo BASE_PATH.'register'; ?>"> Sign Up </a>
		</div>
	</div>
</div>

<?php echo $this->Html->script( array( 'forgetpassword' )); ?>