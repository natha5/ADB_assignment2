<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST'); 

$con = new PDO("mysql:host=localhost;dbname=assignment", 'root', '');

$postdata = file_get_contents("php://input");
if(isset($postdata) && !empty($postdata)){
    $json = json_decode($postdata);
    if($json->action == 'resetDB'){
        $sql = "
        DROP TABLE IF EXISTS Department;
        CREATE TABLE Department (
            deptno int PRIMARY KEY,
            dname varchar(100),
            loc varchar(100)
        );

        DROP TABLE IF EXISTS Employee;
        CREATE TABLE Employee (
            empno int NOT NULL PRIMARY KEY,
            ename varchar(100),
            job varchar(100),
            mgr int REFERENCES Employee(empno),
            hiredate date,
            sal decimal(8,2),
            comm decimal(8,2),
            deptno int NOT NULL REFERENCES Department(deptno)
        );

        INSERT INTO Department (deptno,dname,loc) VALUES (10,'Accounting','New-York');
        INSERT INTO Department (deptno,dname,loc) VALUES (20,'Research','Dallas');
        INSERT INTO Department (deptno,dname,loc) VALUES (30,'Sales','Chicago');
        INSERT INTO Department (deptno,dname,loc) VALUES (40,'Operations','Boston');

        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7369,'Smith','Clerk',7902,'1980-12-17',800,null,20);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7499,'Allen','Salesman',7698,'1981-02-20',1600,300,30);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7521,'Ward','Salesman',7698,'1981-02-22',1250,500,30);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7566,'Jones','Manager',7839,'1981-04-02',2975,null,20);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7654,'Martin','Salesman',7698,'1981-09-28',1250,1400,30);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7698,'Blake','Manager',7839,'1981-05-01',2850,null,30);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7782,'Clark','Manager',7839,'1981-06-09',2450,null,10);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7839,'King','President',null,'981-11-17',5000,null,10);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7844,'Turner','Salesman',7698,'1981-09-08',1500,0,30);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7876,'Adams','Clerk',7788,'1987-09-23',1100,null,20);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7900,'James','Clerk',7698,'1981-12-03',950,null,30);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7902,'Ford','Analyst',7566,'1981-12-03',3000,null,20);
        INSERT INTO Employee (empno,ename,job,mgr,hiredate,sal,comm,deptno) VALUES (7934,'Miller','Clerk',7782,'1982-01-23',1300,null,10);
        ";

        $resultCreate = $con->prepare($sql);
        if($resultCreate->execute()){
            http_response_code(201);
            echo json_encode(["message"=>"Database updated successfully."]);
        }
        else{
            http_response_code(400);
            echo json_encode(["message"=>"Error with resetting the database."]);
        }        
    }    
}

$con = null;




?>