<?php
session_start();
$className = 'index';
if(!empty($_GET)) {
	$className = key($_GET);
} 

$obj = new $className();

abstract class mongo_data {
	
	protected $db;
	protected $collection;
	protected $cursor;
	protected $record_id;
	protected $temp;
	protected $record;
	
	protected function mconnect() {
		$username = 'kwilliams';
		$password = 'mongo1234';
		$this->connection = new Mongo("mongodb://${username}:${password}@localhost/test",array("persist" => "x"));
		$this->setDb();
	}
	protected function setDb($db = 'default1') {
		$this->db = $this->connection->$db;
	}
	protected function setCollection($collection) {
		$this->collection = $this->db->$collection;
		
	}
	protected function findRecords($query = null) {
		if($query == null) {
			$this->cursor = $this->collection->find();
		} else {
			$this->cursor = $this->collection->find($query);
		}
		return $this->cursor;
	}
	
	protected function findRecord($query = null) {
		if($query == null) {
			$this->record = $this->collection->findOne();
		} else {
			$this->record = $this->collection->findOne($query);
		}
		return $this->record;
	}
	
	protected function add($query) {
		$this->collection->insert($query);
		$this->record_id = $query;
		$this->cursor = $this->collection->find();
		
	}
	
	protected function getRecord() {
		foreach($this->record as $key => $value) {
				
				$this->temp .= $key . ': ' . $value . "<br>\n";
				
			}		
			$this->temp .= '<hr>';
		return $this->temp;
	}

	protected function update($query) {
		$this->collection->update($query);
	}
	protected function delete($query) {
		
	}
	protected function getRecords() {
			
		foreach($this->cursor as $record) {
			foreach($record as $key => $value) {
				
				$this->temp .= $key . ': ' . $value . "<br>\n";
				
			}		
			$this->temp .= '<hr>';
		}
		return $this->temp;
	}
 	protected function getRecordID() {
 		return $this->record_id;
 	}
}
abstract class data extends mongo_data {
	protected $query;
	protected $connection;
}
abstract class request extends data {
	protected $data;
	protected $form;
	 function __construct() {
	 	
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->get();

		} else {
			
			$this->post();
		}
		$this->display();
	}
	protected function get() {
		// gets the first value of the $_GET array, so that the correct form function is called.
		$function = array_shift($_GET) . '_get';
		$this->$function();
	}
	protected function post() {
		// gets the first value of the $_GET array, so that the correct form function is called.
		$function = array_shift($_GET) . '_post';
		$this->$function();
	}
}
//this is the class for the homepage

abstract class page extends request {
	protected $header;
	protected $content;
	protected $footer;
	
	protected function display() {
		echo $this->setHeader();
		echo $this->content;
		echo $this->setFooter();
	}

	protected function setHeader() {
		$this->header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
						 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
						 <html xmlns="http://www.w3.org/1999/xhtml">
						<head>
							<title>Conforming XHTML 1.1 Template</title>
						</head>
						<body>';
		return $this->header;
	}

	protected function setFooter() {
		$this->footer = '</body>
					     </html>';
		return $this->footer;
	
	}


}

class index extends page {
	function __construct() {
		parent::__construct();
	}

	protected function get() {

		$this->content = '<h1>Welcome To The App</h1>';
		$this->content .= '<a href="index.php?people=login">Click Here To Login</a><br>';
		$this->content .= '<a href="index.php?people=signup">Click Here To Signup</a><br>';
		$this->content .= '<a href="index.php?people=directory">Click Here To View Users</a><br>';
		$this->content .= '<a href="index.php?people=user">Click Here To View Your Account</a><br>';
		$this->content .= '<a href="index.php?people=reset">Click Here To Reset Your Password</a><br>';
	
$this->content .= '<a href="index.php?service=city">Click Here To List Cities</a><br>';	
	
	}
}
//this will handle logins

class people extends page {
	function __construct() {
		$this->mconnect();
		$this->setCollection('people');
		parent::__construct();
	}

	protected function login_get() {
	
		$this->content = '<h1>Login Here</h1>';
		$this->content .= $this->login_form();
	
	}
	
	protected function login_form() {
		
			$this->form = '<FORM action="./index.php?people=login" method="post">
    				   <LABEL for="username">Username: </LABEL>
              		   <INPUT name="username" type="text" id="username"><BR>
    		           <LABEL for="password">Password: </LABEL>
                       <INPUT name="password" type="password" id="password"><BR>
                       <INPUT type="submit" value="Login"> <INPUT type="reset"></br>
                       <a href="./index.php?people=signup">Click To Signup</a>
 					   </FORM>';
		return $this->form;
	
	}
	protected function login_post() {
		
		$this->findRecord(array('username' => $_POST['username']));
		$_SESSION['username'] = $this->record['username'];
		echo $_SESSION['username'];
		
		$this->content .= '<a href="index.php?people=user">Click Here To View Your Account</a><br>';
		$this->content .= '<a href="index.php?bands=bandsearch">Click Here To Search For Upcoming Shows</a><br>';
	}
	protected function signup_get() {
		$this->content = '<h1>Signup Here</h1>';
		$this->content .= $this->signup_form();
		
	}
	protected function signup_form() {
		$this->form = '<FORM action="./index.php?people=signup" method="post">
    				   <LABEL for="firstname">First name: </LABEL>
              		   <INPUT type="text" name="fname" id="firstname"><BR>
    				   <LABEL for="lastname">Last name: </LABEL>
              		   <INPUT type="text" name="lname" id="lastname"><BR>
    				   <LABEL for="email">Email: </LABEL>
              		   <INPUT type="text" name="email" id="email"><BR>
              		   <LABEL for="zip">Zip Code: </LABEL>
              		   <INPUT type="text" name="zip" id="zip"><BR>
              		   <INPUT type="submit" value="Send"> <INPUT type="reset">
    				   </P>
				   	   </FORM>';
		return $this->form;			  
	}
	protected function signup_post() {
		$this->add($_POST);
		$this->getRecordID();
		$this->content .= '<a href="index.php?people=login">Click Here To Login</a><br>';
		$this->content .= '<a href="index.php?people=directory">Click Here To View Users</a><br>';
	}
	protected function directory_get() {
		$this->content = '<h1>User Accounts</h1>';
		$this->findRecords();
		$this->content .= $this->getRecords();
	}
	
	protected function user_get() {
		$this->findRecord(array('username' => $_SESSION['username']));
		$this->content = $this->getRecord();
	}
	
	protected function reset_get() {
	
		$this->content = '<h1>reset your password</h1>';
		$this->content .= $this->reset_form();
	}
	protected function reset_post() {
				
			print_r($this->findRecord(array('username' => $_POST['username'])));
	}
	protected function reset_form() {
		$this->form = '<FORM action="./index.php?people=reset" method="post">
              		   <LABEL for="email">Email Address:</LABEL>
              		   <INPUT type="text" name="username" id="email"><BR>
    				   <INPUT type="submit" value="Send My Password">
    				   </P>
				   	   </FORM>';
		return $this->form;	

	}

}

class service extends page {
	function __construct() {
		$this->mconnect();
		$this->setCollection('states');
		parent::__construct();
	}
	
	protected function city_get() {
		
		$this->content = 
		'<form action="index.php?service=city" method="post">
		State: <input type="text" name="state" /><br />
		<input type="submit" value="Submit" />
		</form>';
	
	}
	
	protected function city_post() {
		$this->add($_POST);
		$this->content .= $this->getRecords();
		
		
	
	}

}




class bands extends page
{
	public $s1;
	function __construct() 
	{
		$this->mconnect();
		$this->setCollection('bands');
		parent::__construct();
	}
	
	protected function bandsearch_get()
	{
		$this->content = '<style type="text/css">
						body
						{
							background-color:#a0c2de;
						}
						</style>';
		$this->content .= '<h1>Bands Search</h1>';
		$this->content .= $this->bandsearch_form();
	}
	protected function bandsearch_form()
	{
		$this->form = '<style type="text/css">
						body
						{
							background-color:#a0c2de;
						}
						</style>';
		$this->form .=  '<form action="index.php?bands=bandsearch" method="post">
						Band name: <input type="text" name="bandname"><br />
						<input type="submit" value="Search" />
						<input type="reset" value="Reset!"><br>
						</form>';
		return $this->form;
	}
	
	protected function bandsearch_post()
	{
			
			$this->content = '<style type="text/css">
						body
						{
							background-color:#a0c2de;
						}
						</style>';		
			$this->s1 = new newbandsearch($_POST['bandname']);
			if (($this->s1->data->events->event[0])=='')
			{
				echo 'No upcoming shows were found for ' . ucwords($_POST['bandname']) . '</br>';
				$this->content .= '<a href="index.php?bands=bandsearch">Click Here To Search Again</a><br>';
			}
			else
			{
				echo '</br>' . '<img src="' . $this->s1->data->events->event[0]->image[2] . '"/></br></br>';
				echo  '<span style="font-family: Arial;font-weight:bold; color:red">' . $this->s1->data->events->event[0]->name . '</span></br></br>';
				foreach($this->s1->data->events->event as $event) 
				{	
					foreach ($event as $key => $value)
					{
						if ($key == 'venue')
						{
							foreach ($value as $key => $value2)
							{
								if ($key== 'name')
								{
									echo  '<span style="font-family: Arial"> Venue Name: ' . $value2 . '</span></br>';
								}
								else if ($key== 'location')
								{
									foreach ($value2 as $key => $value3)
									{
										if ($key == 'street')
										{
											echo '<span style="font-family: Arial"> Street: ' . $value3 . '</span></br>';
										}		
									}
									foreach ($value2 as $key => $value3)
									{
										if ($key == 'city')
										{
											echo '<span style="font-family: Arial"> City: ' . $value3 . '</span></br>';
										}		
									}
								}
							}
						}
						else if ($key == 'startDate')
						{
							echo '<span style="font-family: Arial"> Date: ' . $value . '</span></br>';
						}
						else if ($key == 'tickets_url')
						{
							echo '<a href="' . $value . '"><span style="font-family: Arial"> Get Tickets </span></a></br>';
						}
					}
					echo '</br>';
				}
				$this->content .= '<a href="index.php?bands=bandsearch">Click Here To Search Again</a><br>';
		}

	}
	
	protected function bandfave_get()
	{
		$this->content = '<h1>Favorite Bands</h1>';
		$this->findRecords();
		$this->content .= $this->getRecords();
		$this->content .= $this->bandfave_form();
	}
	
	protected function bandfave_form()
	{
		$this->form =  '<form action="index.php?bands=bandfave" method="post">
						Band name: <input type="text" name="bandname"><br />
						<input type="submit" value="Add" />
						</form>';
		return $this->form;
	}
	
	protected function bandfave_post()
	{
		$this->findRecord(array('bandname' => $_POST['bandname'], 'user' => $_SESSION['_id']));
		echo 'hello' . '</br/>';
		$this->findRecords();
		$this->content .= $this->getRecords();
		echo $this->getRecords();
		
	}
}



class	search  
{
	public $data;
	public $results;
	public $band_name;
	
	protected function request($url) 
	{
		$ch  = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$this->results = curl_exec($ch);
		curl_close($ch);
		$this->data = new SimpleXMLElement($this->results);
	}
}

class newbandsearch extends search
{
	public $url = "www.5gig.com/api/request.php?api_key=899390ce1cd1e55643a5466893e698b2";
		
	function __construct($band_name)
	{
		$this->url .= '&method=artist.getEvents&artist=';
		$this->url .= urlencode($band_name);
		$this->request($this->url);
	}
}
	

?>

