<!DOCTYPE html>
<html>
<style>
input[type=text], select {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing:border-box;
}

button[type=submit] {
  width: 100%;
  background-color: #9a26b8;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button[type=submit]:hover {
  background-color: #c21ad1;
}

h2{
    text-align: center;
}

div {
  border-radius: 5px;
  background-color: #fffcfc;
  padding: 20px;

}
</style>
<body>

			<h2>Change Password</h2>

			<div>
				<form action="email-service.php" method="post">
					<input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])){echo $_GET['token'];}?>">
           
					<label for="password">New Password</label>
					<input type="text" id="password" name="password" placeholder="New Password">
					<label for="cpassword">Confirm Password</label>
					<input type="text" id="cpassword" name="cpassword" placeholder="Confirm Password">

					<button type="submit" name="password_reset">Update Password</button>
				</form>
			</div>

</body>
</html>


