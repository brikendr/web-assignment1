<?php 
  $user1 = new User(); 
  $user1->setSalary(1000); // Works 
  print_r($user1); 
  $user1->toString(); 
  class User{   
    private $salary;   
    
    public function setSalary($s){     
      $this->salary = $s;
    } 

    public function toString(){
        echo "Salary: ". $this->salary;
    }
   }
?>
