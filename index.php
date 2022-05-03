<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Database interface</title>
		<link rel="stylesheet" href="style.css?<?php echo time(); /*this is done so that css reloads and is not cached*/ ?>" type="text/css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<!--jQuery -->
		<!--
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		-->


		<script>
			const queryString = window.location.search;
			const urlParams = new URLSearchParams(queryString);

			// if want to use the query parameters in javascript
			const isReset = urlParams.get('reset');

			function resetDB(){
				fetch(getBaseURL()+"resetDB.php", {
					method: 'POST',
					headers: {'Content-Type': 'application/json'},
					body: JSON.stringify({action:'resetDB'})
				})
				.then(response => response.json())
				.then(result => {
					console.log(result);
					alert(result.message);
				})
				.catch(error => {
					console.log(error);
					alert(error.message);
				});
			}

			function getBaseURL(){
				return window.location.href.split('?')[0];
			}

			
			//this uses jQuery
			/*
			$(document).ready(function(){
				$('#resetButton').click(function(){
					data =  {'reset': isReset == 'true' ? true : false};
					$.post(getBaseURL()+"resetDB.php", data).done(function (response) {
						// Response div goes here.
						alert("Database has been reset.");
					});
				});
			});
			*/
			
		</script>
	</head>

	<body class="full-height">
		<div class="row h-100 full-height">
			<div class="col-2 bg-grey ps-4 pt-4 full-height">
				<button type="button" class="btn btn-danger" id="resetButton" onclick="resetDB()">Reset Database</button>
				<hr>
				<h3>Select:</h3>
				<p class="mb-0"><a href="?select=employee">Employee</a></p>
				<p class="mb-0"><a href="?select=department">Department</a></p>
				<hr class="mt-4">
				<h3>Queries:</h3>
				<p class="mb-0"><a href="?q=SELECT d.loc as 'Location', AVG(e.sal) as 'Average Salary' FROM department d LEFT JOIN employee e ON d.deptno = e.deptno GROUP BY d.loc ORDER BY AVG(e.sal) DESC">Query 1</a></p>
				<p class="mb-0"><a href="?q=SELECT e.ename as 'Name', e.job 'Job', e.hiredate as 'Hire date', d.dname as 'Department', ee.ename as 'Manager' FROM employee e INNER JOIN employee ee ON e.mgr = ee.empno INNER JOIN department d ON d.deptno = e.deptno WHERE e.hiredate > '1981-04-30'">Query 2</a></p>
				<p class="mb-0"><a href="?q=query_3">Query 3</a></p>

			</div>
			<div class="col-8 px-3 pt-4">
				<?php
					require 'connect.php';
					if($con == null){
						echo "<h4 class='alert alert-danger'>Could not connect to the database.</h4>";
					}
					else if(isset($_GET['select'])){
						// refactored and dynamically created solution
						// get column names for the selected table
						$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$_GET['select']."'";
						$colsResult = $con->prepare($sql);
						$colsResult->execute();
						$colNames = [];
						if($colsResult){
							while($row = $colsResult->fetch(PDO::FETCH_ASSOC)){
								array_push($colNames, $row['COLUMN_NAME']);
							}
						}

						// set page title
						if($_GET['select'] == 'employee'){
							echo "<h1 class='mb-4'>Employee Table</h1>";
						}
						else if($_GET['select'] == 'department'){
							echo "<h1 class='mb-4'>Department Table</h1>";
						}

						//draw respective table
						try{
							$sql = "SELECT * FROM ".$_GET['select'];
							$result = $con->prepare($sql);
							$result->execute();
							if($result){
								drawTable($result, [0]);							
							}
						}
						catch(PDOException $e){
							echo "<h4 class='alert alert-danger'>".$e->getMessage()."</h4>";
						}
					}
					else if (isset($_GET['q'])){
						$sql = $_GET['q'];
						
						echo "<h4>Query:</h4>";
						echo "<div class='border border-danger border-2 rounded px-2 py-4 mb-4'>".$sql."</div>";

						try{
							$result = $con->prepare($sql);
							$result->execute();
							if($result){
								drawTable($result, []);
							}
						}
						catch(PDOException $e){
							echo "<h4 class='alert alert-danger'>".$e->getMessage()."</h4>";
						}
						
					}

					$con = null;

					// FUNCTION that draws a table and is compatible with any SQL SELECT query
					// $result = result object from an SQL SELECT query
					// $primaryKeys = indexes of columns that should have # in their table header
					function drawTable($result, $primaryKeys){
						$table = $result->fetchAll();
						$colNames = array_keys(array_filter($table[0], function($key){
							return !is_numeric($key);
						},ARRAY_FILTER_USE_KEY));

						echo "<table class='table table-striped'>";
						echo "<thead><tr class='table-danger'>";
						$i = 0;
						foreach($colNames as $col){
							//write # with the name of the first column (primary key)
							if(in_array($i,$primaryKeys)){
								echo "<th scope='col'>#".$col."</th>";
							}
							else{
								echo "<th scope='col'>".$col."</th>";
							}
							$i++;
						}						
								
						echo "</tr></thead>";
						echo "<tbody>";
						foreach($table as $row){
							echo "<tr>";
							foreach($colNames as $col){
								//IDK if it is better to write NULL or leave entry empty
								if(!isset($row[$col])){
									echo "<td class='fst-italic fw-lighter text-muted'>null</td>";
								}
								else{
									echo "<td>".$row[$col]."</td>";
								}
							}
							
							echo "</tr>";
						}
						echo "</tbody></table>";
					}
				?>

				<div class="mt-5">
					<button class="btn btn-secondary mb-5">+ Add Row</button>
				</div>
			</div>
		<div>
		

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