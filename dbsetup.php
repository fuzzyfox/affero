<?php

	/*
	 import needed libraries
	*/
	include('app/config.php');
	include('app/libraries/utility.lib.php');
	include('app/libraries/database.lib.php');
	
	$lib = (object)array(
		'utility' => new Utility,
		'database' => new Database($config->db->host, $config->db->name, $config->db->user, $config->db->pass)
	);
	
	//check if ALL details have been filled in
	if(($_POST['username'] != '')&&
	   ($_POST['password'] != '')&&
	   ($_POST['password'] == $_POST['confpassword'])&&
	   ($_POST['email'] != '')&&
	   ($_POST['email'] == $_POST['confemail'])&&
	   $lib->utility->valid_email($_POST['email']))
	{
		//process form and setup db
		
		/*
		 setup db structure/insert locale data
		*/
		//read info from file
		$sql = file_get_contents('db.sql');
		//execute commands from file on db
		$cmds = explode(';', $sql);
		foreach($cmds as $command)
		{
			$lib->database->query($command);
		}
		
		/*
		 create first user
		*/
		$data = array(
			'username' => strtolower($_POST['username']),
			'userPassword' => $lib->utility->hash_string($_POST['password'], strtolower($_POST['username'])),
			'userEmail' => $_POST['email']
		);
		$lib->database->insert('user', $data);
		
		//destroy installation files
		$fhandle = fopen('db.sql', 'w');
		fwrite($fhandle, '');
		fclose($fhandle);
		
		$fhandle = fopen('dbsetup.php', 'w');
		fwrite($fhandle, '');
		fclose($fhandle);
	}
	else
	{
		//get the information needed
		?>
		<html>
			<head>
				<meta http-equiv='Content-type' content='text/html; charset=utf-8'>
				<link rel="stylesheet" href="asset/css/generic.css">
			</head>
			<body>
				<h1>Database Setup</h1>
				<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
					<label for="username">Username</label>
					<input type="text" name="username">
					<label for="password">Password</label>
					<input type="password" name="password">
					<label for="confpassword">Confirm Password</label>
					<input type="password" name="confpassword">
					<label for="email">Email</label>
					<input type="text" name="email">
					<label for="email">Confirm Email</label>
					<input type="text" name="confemail">
					<div class="controls">
						<button type="submit">setup</button>
					</div>
				</form>
			</body>
		</html>
		<?php
	}
	
?>