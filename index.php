<!DOCTYPE html>
<html lang="en">

	<head>
		<title>Database interface</title>
		<link rel="stylesheet" href="style.css?<?php echo time(); /*this is done so that css reloads and is not cached*/ ?>" type="text/css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

		<script>
			function resetDB(){
				fetch("http://localhost/ADB_assignment2/resetDB.php", {
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

			function runQuery(){
				this.window.location.href = getBaseURL() + '?id=0&q=' + document.getElementById("query-input").value;
			}

			function editQuery(){
				this.window.location.href = getBaseURL() + '?id=custom&q=' + document.getElementById("sql-text").innerHTML;
			}
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
				<p class="mb-0"><a href="?id=1&q=SELECT d.loc as 'Location', AVG(e.sal) as 'Average Salary' FROM department d LEFT JOIN employee e ON d.deptno = e.deptno GROUP BY d.loc ORDER BY AVG(e.sal) DESC">Query 1</a></p>
				<p class="mb-0"><a href="?id=2&q=SELECT e.ename as 'Name', e.job 'Job', e.hiredate as 'Hire date', d.dname as 'Department', ee.ename as 'Manager' FROM employee e INNER JOIN employee ee ON e.mgr = ee.empno INNER JOIN department d ON d.deptno = e.deptno WHERE e.hiredate > '1981-04-30'">Query 2</a></p>
				<p class="mb-0"><a href="?id=3&q=SELECT e.job as 'Job', COUNT(e.ename) as 'Number of Workers', MAX(e.sal) as 'Max Salary' FROM employee e GROUP BY e.job HAVING COUNT(e.ename) = COUNT(DISTINCT e.ename)">Query 3</a></p>
				<p class="mb-0"><a href="?id=custom">Custom</a></p>

			</div>
			<div class="col-7 px-3 pt-4">
				<?php
					require 'connect.php';
					if($con == null){
						echo "<h4 class='alert alert-danger'>Could not connect to the database.</h4>";
					}
					else if(isset($_GET['select'])){
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
						if ($_GET['id'] == 'custom'){
							drawQueryInput($_GET['q']);
						}
						else{
							if(isset($_GET['id']) && intval($_GET['id']) > 0){
								echo "<h4>Query ".$_GET['id'].":</h4>";
								if($_GET['id'] == '1'){
									echo "<p>List ALL locations and average salary for each location sorted from largest salary to smallest.</p>";
								}
								else if($_GET['id'] == '2'){
									echo "<p>Employees that have been hired later than 30/04/1981. Also display the hire date, their department name and who is their manager. </p>";
								}
								else if($_GET['id'] == '3'){
									echo "<p>List all jobs, how many people work that job, maximum salary and only display those jobs whose employees have all different names. </p>";
								}
							}
							else{
								echo "<h4>Query:</h4>";
							}
							

							$sql = $_GET['q'];
							echo "<div class='border border-danger border-2 rounded pt-4 mb-4'>
								<p class='px-2' id='sql-text'>".$sql."</p>
								<button class='btn btn-outline-danger rounded ml-auto' onclick='editQuery()'>Edit query</button>
								</div>";

							try{
								//grab the first word of the query in lowercase
								$statement = strtolower(explode(' ',$sql)[0]);
								
								if($statement == "select"){
									$result = $con->prepare($sql);
									$result->execute();
									if($result){
										drawTable($result, []);
									}
								}
								else if($statement == "insert" || $statement == "update" || $statement == "delete"){
									$result = $con->prepare($sql);
									$result->execute();
									if($result){
										echo "<h4 class='alert alert-success'>".strtoupper($statement)." successful</h4>";
									}
								}
								else{
									echo "<h4 class='alert alert-danger'>Unsupported query. Supported queries are SELECT, INSERT, UPDATE and DELETE</h4>";
								}
								
							}
							catch(Exception $e){
								echo "<h4 class='alert alert-danger'>".$e->getMessage()."</h4>";
							}
							

							
						}						
					}
					else if(isset($_GET['id']) && $_GET['id']=="custom"){
						drawQueryInput("");
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
							//write # with the name of the columns marked as primary keys
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
								// write null inside a table cell instead of leaving it empty
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

					function drawQueryInput($value){
						echo "<h4>Enter query:</h4>";
						echo "
							<textarea id='query-input' class='form-control mt-3' placeholder='SELECT * FROM employee'>".$value."</textarea>
							<button class='btn btn-secondary mt-4' onclick='runQuery()'>Run</button>
						";
					}
				?>

				
			</div>
			<div class="full-height col-3 bg-grey">
				<?php
				if(isset($_GET['select'])){
					require 'connect.php';
					if($_GET['select'] == 'employee'){
					
						echo "
							<div class='mt-5'>
							<form method ='post' class='container'>
								<label for='ename'>Employee surname:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='ename' name='ename' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='empno'>Employee number:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='empno' name='empno' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='job'>Job:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='job' name='job' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='mgr'>Manager:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='mgr' name='mgr'>
									<span class='error' style='color:transparent'>*</span>
								</div>
	
								<label for='hiredate'>Hiredate:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='hiredate' name='hiredate' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='sal'>Salary:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='sal' name='sal' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='comm'>Commission:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='comm' name='comm'>
									<span class='error' style='color:transparent'>*</span>
								</div>
	
								<label for='deptno'>Department Number:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='deptno' name='deptno' required>
									<span class='error'>*</span><br><br>
								</div>
							
								<button class='btn btn-secondary mb-5' name='submit'>+ Add Row</button>
								</form>
							</div>
						";
						if(isset($_POST['submit']))
						{
							$sql = "
							INSERT INTO Employee (empno, ename, job, mgr, hiredate, sal, comm, deptno)
							VALUES ('".$_POST['empno']."', '".$_POST['ename']."', '".$_POST['job']."', '".$_POST['mgr']."', '".$_POST['hiredate']."', '".$_POST['sal']."', '".$_POST['comm']."', '".$_POST['deptno']."')
							";
							
							$sentData = $con->prepare($sql);
							$sentData->execute();
						}
						
					}else if($_GET['select'] == 'department'){
						
						echo "
							<div class='mt-5'>
							<form method ='post' class='container'>
								<label for='deptnoInput'>Department number:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='deptnoInput' name='deptnoInput' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='dnameInput'>Department name:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='dnameInput' name='dnameInput' required>
									<span class='error'>*</span><br>
								</div>
	
								<label for='locInput'>Location:</label><br>
								<div class='d-flex justify-content-left mb-3'>
									<input class='form-control' type='text' id='locInput' name='locInput' required>
									<span class='error'>*</span><br>
								</div>
	
								<button class='btn btn-secondary mb-5' name='submit' type='submit'>+ Add Row</button>
								</form>
							</div>
						";
						if(isset($_POST['submit']))
						{							
							$sql = "
							INSERT INTO Department (deptno,dname,loc) 
							VALUES ('".$_POST['deptnoInput']."','".$_POST['dnameInput']."','".$_POST['locInput']."');
							";

							$sentData = $con->prepare($sql);
							$sentData->execute();
						}
					}
				}
				?>
			</div>
		<div>
	</body>
</html>