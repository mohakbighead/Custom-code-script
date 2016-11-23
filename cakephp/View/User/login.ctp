<style>
.has-error{color:#ff0000 !important;border:1px solid red !important; }
.has-success{color:#78b310 !important;border:1px solid #78b310 !important;}
</style>
<?php echo $this->Form->create('User', array('id'=>'login', 'role' => 'form', 'method'=> 'post', 'class' => 'form-signin') ); ?>
	<?php echo $this->Form->input('email', array( 'id' => 'email', 'type'=> 'text', 'class' =>'form-control', 'placeholder' => 'Email address', 'autofocus' => 'autofocus', 'div'=> false, 'label'=> false) ); ?>
	<?php echo $this->Form->input('password', array( 'id' => 'password', 'type'=> 'password', 'class' =>'form-control', 'placeholder' => 'Password', 'div'=> false, 'label'=> false) ); ?>
	<?php echo $this->Form->button('Log in', array( 'id' => 'login_btn', 'value'=> 'Log in', 'type'=> 'submit', 'class' =>'btn btn-block bt-login') ); ?>
	
<?php echo $this->Form->end(); ?>
<div class="form-footer">
	<div class="row">
		
	</div>
</div>

<?php echo $this->Html->script( array( 'login' )); ?>