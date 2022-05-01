<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Database interface</title>
		<link rel="stylesheet" href="style.css">
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
						$con = new PDO("mysql:host=localhost;dbname=assignment", 'root', '');

						if(isset($_GET['select'])){
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
							$sql = "SELECT * FROM ".$_GET['select'];
							$result = $con->prepare($sql);
							$result->execute();
							if($result){
								echo "<table class='table table-striped'>";
								echo "<thead><tr class='table-danger'>";
								$i = 0;
								foreach($colNames as $col){
									//write # with the name of the first column (primary key)
									if($i == 0){
										echo "<th scope='col'>#".$col."</th>";
									}
									else{
										echo "<th scope='col'>".$col."</th>";
									}
									$i++;
								}
										
								echo "</tr></thead>";
								echo "<tbody>";
								while($row = $result->fetch(PDO::FETCH_ASSOC)){
									echo "<tr>";
									foreach($colNames as $col){
										//IDK if it is better to write NULL or leave entry empty
										if(!isset($row[$col])){
											echo "<td class='fst-italic fw-lighter text-muted'>NULL</td>";
										}
										else{
											echo "<td>".$row[$col]."</td>";
										}
									}
									//"<td>".$row["empno"]."</td><td>".$row["ename"]."</td><td>".$row["job"]."</td><td>".$row["mgr"]."</td><td>".$row["hiredate"]."</td><td>".$row["sal"]."</td><td>".$row["comm"]."</td><td>".$row["deptno"]."</td>"
									
									echo "</tr>";
								}
								echo "</tbody></table>";
							}


							//not refactored and hard coded solution
							if($_GET['select'] == 'employee'){
								echo "<h1 class='mb-4'>Employee Table</h1>";

								$sql = "SELECT * FROM employee";
								$result = $con->prepare($sql);
								$result->execute();
								if($result){
									echo "<table class='table table-striped'>";
									echo "<thead>
											<tr class='table-danger'>
												<th scope='col'>#empno</th><th scope='col'>ename</th><th scope='col'>job</th><th scope='col'>mgr</th><th scope='col'>hiredate</th><th scope='col'>sal</th><th scope='col'>comm</th><th scope='col'>deptno</th>
											</tr>
										</thead>";
									echo "<tbody>";
									while($row = $result->fetch(PDO::FETCH_ASSOC)){
										echo "<tr>
												<td>".$row["empno"]."</td><td>".$row["ename"]."</td><td>".$row["job"]."</td><td>".$row["mgr"]."</td><td>".$row["hiredate"]."</td><td>".$row["sal"]."</td><td>".$row["comm"]."</td><td>".$row["deptno"]."</td>
											</tr>";
									}
									echo "</tbody></table>";
								}
								else{
									echo "<p>Table employee does not exist or is empty.</p>";
								}
							}
							// DEPARTMENT TABLE
							else if($_GET['select'] == 'department'){
								echo "<h1 class='mb-4'>Department Table</h1>";

								$sql = "SELECT * FROM department";
								$result = $con->prepare($sql);
								$result->execute();
								if($result){
									echo "<table class='table table-striped'>";
									echo "<thead>
											<tr class='table-danger'>
												<th scope='col'>#deptno</th><th scope='col'>dname</th><th scope='col'>loc</th>
											</tr>
										</thead>";
									echo "<tbody>";
									while($row = $result->fetch(PDO::FETCH_ASSOC)){
										echo "<tr><td>".$row["deptno"]."</td><td>".$row["dname"]."</td><td>".$row["loc"]."</td></tr>";
									}
									echo "</tbody></table>";
								}
								else{
									echo "<p>Table department does not exist or is empty.</p>";
								}
							}
						}

						$con = null;
					?>

					<div class="mt-5">
						<button class="btn btn-secondary mb-5">+ Add Row</button>
					</div>
				</div>
			<div>
			
		</div>
		

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