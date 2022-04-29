<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Database interface</title>
		<link rel="stylesheet" href="style.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<!--jQuery -->

			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


		<script>
			const queryString = window.location.search;
			const urlParams = new URLSearchParams(queryString);

			// if want to use the query parameters in javascript
			const isReset = urlParams.get('reset');
			console.log(isReset);

			function resetDB(){
				// Create an XMLHttpRequest object
				/*
				const xhttp = new XMLHttpRequest();
				xhttp.onload = function() {
					alert("Database has been reset.");
				}
				xhttp.open("POST", getBaseURL(), true);
				xhttp.setRequestHeader("Content-type", "application/json");
				xhttp.send("{'reset':true}");
				*/
			}

			function getBaseURL(){
				return window.location.href.split('?')[0];
			}

			
			//this uses jQuery
			$(document).ready(function(){
				$('#resetButton').click(function(){
					data =  {'reset': isReset == 'true' ? true : false};
					$.post(getBaseURL(), data, function (response) {
						// Response div goes here.
						alert("Database has been reset");
					});
				});
			});
			
		</script>
	</head>

	<body>
		<div class="px-4">
			<div class="row mt-4">
				<div class="col-2">
					<button type="button" class="btn btn-danger" id="resetButton" onclick="resetDB()">Reset Database</button>
					<hr>
					<h3>Select:</h3>
					<p class="mb-0"><a href="?select=employee">Employee</a></p>
					<p class="mb-0"><a href="?select=department">Department</a></p>
					<hr class="mt-4">
					<h3>Queries:</h3>
					<p class="mb-0"><a href="?q=query_1">Query 1</a></p>
					<p class="mb-0"><a href="?q=query_2">Query 2</a></p>
					<p class="mb-0"><a href="?q=query_3">Query 3</a></p>

				</div>
				<div class="col-8 px-3">
					<?php
						if(isset($_GET['select'])){
							if($_GET['select'] == 'employee'){
								echo "<h1>Employee Table</h1>";
							}
							else if($_GET['select'] == 'department'){
								echo "<h1>Department Table</h1>";
							}
						}
					?>
				</div>
			<div>
			
		</div>
		

		<?php


			if(isset($_POST['reset'])){
				echo "good";
			}

		?>

			<!--
		<form>
			<h2>Add new employee<h2>
			<label for="ename">Employee surname:</label><br>
			<input type="text" id="ename" name="ename"><br>
			
			<label for="enum">Employee number:</label><br>
			<input type="text" id="enum" name="enum">
			
			<label for="job">Job:</label><br>
			<input type="text" id="job" name="job">
			
			<label for="mgr">Manager:</label><br>
			<input type="text" id="mgr" name="mgr">
			
			<label for="hiredate">Hiredate:</label><br>
			<input type="text" id="hiredate" name="hiredate">
			
			<label for="sal">Salary:</label><br>
			<input type="text" id="sal" name="sal">
			
			<label for="comm">Comm:</label><br>
			<input type="text" id="comm" name="comm">
			
			<label for="deptno">Department Number:</label><br>
			<input type="text" id="deptno" name="deptno">
			
			<button type="button">Confirm</button>
		</form>
		
		<form>
			<h2>Add new department<h2>
			<label for="dname">Department Name:</label><br>
			<input type="text" id="dname" name="dname"><br>
			
			<label for="dnum">Department number:</label><br>
			<input type="text" id="dnum" name="dnum">
			
			<label for="loc">Location:</label><br>
			<input type="text" id="loc" name="loc">
			
			<button type="button">Confirm</button>
		</form>
		-->

	</body>
</html>