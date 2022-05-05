<?php

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




?>