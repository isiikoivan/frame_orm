
<?php
class Crud

{
  // function to connect to the database
  function dbconnect($host, $dbname, $user, $password)
  {
    try {
      $dns = "mysql:host=$host; dbname=$dbname";
      $GLOBALS['pdo'] = new PDO($dns, $user, $password);
      $pdo = $GLOBALS['pdo'];

      echo 'connected';
    } catch (PDOException $e) {
      echo $e;
      //.getMessage() 
    }

    return $pdo;
  }
  //  $GLOBALS['image_name'] = $image_naming;
  // function to insert universally//
  function universal_insert($pdo, $c = array(), $page_location)
  {
    // if (isset($_POST[$btn_name])) {
    //index 1 holds the table name
    $tables = $c[0];
    echo "\n\t\ttable name\n\t\t";
    echo $tables;

    //index >1 and <length($c/2) hold the columns in the table
    $columns = array();
    for ($n = 1; $n <= ((count($c) - 1) / 2); $n++) {
      //echo $c[$n];
      array_push($columns, $c[$n]);
    }
    echo "\n\t\tcolumns in the db\t\t \n";
    print_r($columns);

    // echo $columns;

    //index >length($c/2) and <length($c) hold the values in the table //loops thru the values
    $values = array(); //array is created once

    $target_dir = "../files/";
    for ($n = ((count($c) + 1) / 2); $n <= (count($c) - 1); $n++) {
      $check_type = is_array($c[$n]);
      if ($check_type) {
        //$t_dir=mkdir("assests\files");


        $target_file = basename($_FILES['image']['name']);
        $target_folder = $target_dir . $target_file;
        $temp = $_FILES['image']['tmp_name'];

        // uploading files into file
        move_uploaded_file($temp, $target_folder);

        $c[$n] = $target_file;
      }
      array_push($values, $c[$n]);
    }
    echo "\n\t\tvalues to be input to db\t\t\n";
    print_r($values);


    //derivation of placeholders eg :name from column names concatination keys with :
    $placeholders = array();
    for ($v = 1; $v <= ((count($c) - 1) / 2); $v++) {
      $concatenate = ':' . $c[$v];
      array_push($placeholders, $concatenate);
    }
    echo "\n\t\tplace holders\t\t\n";
    print_r($placeholders);


    //converting keys into strings
    $keys = array();
    for ($v = 1; $v <= ((count($c) - 1) / 2); $v++) { //establishing a single array that is to be converted to strings the excute function
      $singlearr = array();
      $conc_keys = ':' . $c[$v];
      array_push($singlearr, $conc_keys);
      //  print_r($singlearr);
      $string_keys = implode($singlearr);

      array_push($keys, $string_keys);
    }
    echo "\n\t\tkeys\n\t\t\t";
    // echo $keys[0];
    print_r($keys);

    //pairing up keys and values for the excute function
    //dealing with $keys array and $values array
    $exc_array = array();
    for ($n = 0; $n <= (count($values) - 1); $n++) {
      $exc_array[$keys[$n]] = $values[$n];
      // array_push($exc_array,$assign);

    }
    // echo "\n \t excute implode";
    //converting into a stirng
    // print_r($exc_array[':email']);

    echo "\n \t\t\t excute array\t\t\t";
    print_r($exc_array);

    //feeds  to the sql
    $ins_columns = implode(",", $columns);
    $ins_values = implode(",", $placeholders);


    $sql = "INSERT INTO $tables ($ins_columns) VALUES ($ins_values)";
    $stmt = $pdo->prepare($sql);
    $sub = $stmt->execute($exc_array);
    if ($sub) {

      $message = 'data inserted successfully';
      header("Location:$page_location");
    } else {
      $message = 'failed to submit';
    }
    // }
  }



  function retriving($c = array())
  {
    //two values
    $pd = $c[0];
    $tab = $c[1];
    $sql = "SELECT * FROM $tab";
    $stmt = $pd->prepare($sql);
    $stmt->execute();
    $GLOBALS['dat'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = $GLOBALS['dat'];

    return $data;

    // $pd = $c[0];
    // $tab = $c[1];
    // $sql = "SELECT * FROM $tab";
    // $stmt = $pd->prepare($sql);
    // $stmt->execute();
    // $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // $_SESSION['alloutput'] = $data;
    // return $data;
  }

  //locate the value to be updated


  function locate($pdo, $value, $id, $table_name)
  {

    $sql = "SELECT * FROM $table_name WHERE $value=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    // $dc=array($data);

    return $data;
  }

  function locateValues($pdo, $id, $table_name, $value)
  {

    // $sql = "SELECT * FROM $table_name WHERE id=:$value";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([':$value' => $id]);

    // $_SESSION['loc'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // $data = $_SESSION['loc'];
    // return $data;
  }

  //update the value located
  function updating($pdo, $d = array(), $target_id, $id, $page_location)
  {

    // if (isset($_POST[$btn_name])) {
    //index 1 holds the table name
    $tables = $d[0];
    echo "\n\t\ttable name\n\t\t";
    echo $tables;

    //index >1 and <length($c/2) hold the columns in the table
    $columns = array();
    for ($n = 1; $n <= ((count($d) - 1) / 2); $n++) {
      //echo $c[$n];
      array_push($columns, $d[$n]);
    }
    echo "\n\t\tcolumns in the db\t\t \n";
    print_r($columns);

    // echo $columns;

    //index >length($c/2) and <length($c) hold the values in the table //loops thru the values
    $values = array();
    $target_dir = "../files/";
    //array is created once
    for ($n = ((count($d) + 1) / 2); $n <= (count($d) - 1); $n++) {
      $check_type = is_array($d[$n]);
      if ($check_type) {
        //$t_dir=mkdir("assests\files");

        $target_file = $target_dir . basename($_FILES['image']['name']);
        //     $target_dir="files/";
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

        $d[$n] = $target_file;

        //   }

      }
      array_push($values, $d[$n]);
    }

    array_push($values, $id);
    echo "\n\t\tvalues to be input to db \t\t\n";
    print_r($values);

    print($target_dir);
    print($target_file);


    //derivation of placeholders eg :name from column names concatination keys with :
    $placeholders = array();
    for ($v = 1; $v <= ((count($d) - 1) / 2); $v++) {
      $concatenate = ':' . $d[$v];
      array_push($placeholders, $concatenate);
    }
    echo "\n\t\tplace holders\t\t\n";
    print_r($placeholders);


    //converting keys into strings
    $keys = array();
    for ($v = 1; $v <= ((count($d) - 1) / 2); $v++) { //establishing a single array that is to be converted to strings the excute function
      $singlearr = array();
      $conc_keys = ':' . $d[$v];
      array_push($singlearr, $conc_keys);
      //  print_r($singlearr);
      $string_keys = implode($singlearr);

      array_push($keys, $string_keys);
    }

    $pointplaceholder = ':' . $target_id;
    echo $pointplaceholder;
    array_push($keys, $pointplaceholder);
    echo "\n\t\tkeys ivan\n\t\t\t";
    echo $keys[0];
    print_r($keys);

    //pairing up keys and values for the excute function
    //dealing with $keys array and $values array
    $exc_array = array();
    for ($n = 0; $n <= (count($values) - 1); $n++) {
      $exc_array[$keys[$n]] = $values[$n];
      // array_push($exc_array,$assign);

    }
    // array_push($exc_array,['id']=$id);
    echo "\n \t excute implode";
    //converting into a stirng
    //  print_r($exc_array[':emails']);

    echo "\n \t\t\t excute array  seen \t\t\t";
    print_r($exc_array);

    ////isiiko start two
    $update_func = array();
    for ($n = 0; $n <= (count($columns) - 1); $n++) {

      $s = $columns[$n] . '=' . $placeholders[$n];
      array_push($update_func, $s);
    }
    echo "\n\t\tupdating setting in the db\t\t \n";
    print_r($update_func);

    //feeds  to the sql
    $ins_update = implode(',', $update_func);

    echo $ins_update;

    $sql = "UPDATE  $tables SET $ins_update WHERE $target_id=$pointplaceholder";

    $stmt = $pdo->prepare($sql);
    // $stmt->execute([':id' => $id]);
    $sub = $stmt->execute($exc_array);
    if ($sub) {

      echo $message = 'data updated successfully';
      header("Location:$page_location");
    } else {
      echo $message = 'failed to update';
      //    header("Location:$page_location");
    }
    //}
  }

  //function to delete

  function deleting($pdo, $compvalue, $id, $table_name, $destination)
  {
    echo 'successfully deleted1';

    //    if(isset($_POST[$btn_name]))
    //    {
    $sql = "DELETE FROM $table_name WHERE $compvalue =:id";
    $stmt = $pdo->prepare($sql);
    echo 'successfully deleted2';
    $exe = $stmt->execute([':id' => $id]);
    // $exe=$stmt->fetch(PDO::FETCH_ASSOC);
    // // $dc=array($data);
    // return $exe;
    echo 'successfully deleted';
    header("Location:$destination");


    // }

  }

  function verifying($pdo, $c = array(), $page_location)
  {
    $tables = $c[0];
    echo "\n\t\ttable name\n\t\t";
    echo $tables;


    //index >1 and <length($c/2) hold the columns in the table
    $columns = array();
    for ($n = 1; $n <= ((count($c) - 1) / 2); $n++) {
      //echo $c[$n];
      array_push($columns, $c[$n]);
    }
    echo "\n\t\tcolumns in the db\t\t \n";
    print_r($columns);

    // echo $columns;

    //index >length($c/2) and <length($c) hold the values in the table //loops thru the values
    $values = array(); //array is created once

    for ($n = ((count($c) + 1) / 2); $n <= (count($c) - 1); $n++) {

      array_push($values, $c[$n]);
    }
    echo "\n\t\tvalues to be input to db\t\t\n";
    print_r($values);



    //derivation of placeholders eg :name from column names concatination keys with :
    $placeholders = array();
    for ($v = 1; $v <= ((count($c) - 1) / 2); $v++) {
      $concatenate = ':' . $c[$v];
      array_push($placeholders, $concatenate);
    }
    echo "\n\t\tplace holders\t\t\n";
    print_r($placeholders);

    echo 'start';
    $exc_array = array();
    for ($n = 0; $n <= (count($values) - 1); $n++) {
      $implodearray = array();
      echo 'peace';
      array_push($implodearray, $placeholders[$n]);
      print_r($implodearray);
      $theval = implode($implodearray);
      //echo $theval;
      //   $assign= $exc_array[$theval] '.'=>'.' $values[$n];
      array_push($exc_array, $theval);
    }
    // echo "\n \t excute implode";
    //converting into a stirng
    // print_r($exc_array[':email']);

    echo "\n \t\t\t excute array\t\t\t";
    print_r($exc_array);
    echo 'end';
    $finalarray = array();

    for ($n = 0; $n <= (count($values) - 1); $n++) {
      $fun1 = array();
      array_push($fun1, $placeholders[$n]);
      $fun = implode($fun1);
      $finalarray[$fun] = $values[$n];
      // array_push($exc_array,$assign);

    }
    echo 'trial';
    print_r($finalarray);

    // $finalarray2 = array();

    // for ($n = 0; $n <= (count($values) - 1); $n++) {
    //   $choose = $columns[$n].' = '.$placeholders[$n];
    //   array_push($finalarray2,$choose);


    // }
    // echo'hello';
    // print_r($finalarray2);

    //pairing up keys and values for the excute function
    //aligning columns to placeholders to be used in where statement
    $exc = array();
    for ($n = 0; $n <= (count($columns) - 1); $n++) {
      $exc1 = array();

      $paired_val = $columns[$n] . '=' . $placeholders[$n];
      array_push($exc1, $paired_val);

      $exc_implode = implode($exc1);
      array_push($exc, $exc_implode);
    }
    echo 'vana';
    print_r($exc);
    echo 'vana';
    //feeds  to the sql
    $ins_columns1 = $exc[0];
    $ins_columns2 = $exc[1];
    echo $ins_columns1;
    echo $ins_columns2;

    $sql = "SELECT * FROM $tables WHERE $ins_columns1  AND $ins_columns2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($finalarray);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['dean'] = $data;

    // $user = $stmt->fetch_All(PDO::FETCH_OBJ);


    $count = $stmt->rowCount();
    //$counting = $count->rowCount($user);
    // $_SESSION['amount'] = $counting;


    if ($count > 0) {
      if ($_SESSION['dean'] = $data) {
        //echo 'available';

        header("Location:$page_location");
      }
    } else {

      $_SESSION['message'] = 'username or password is wrong';
    }
  }


  function loggingout($page_location)
  {

    session_destroy();
    header("location:$page_location");
  }
  //validating text validating_onebyone
  function validating_onebyone($vt_value, $textif, $textelse)
  {
    if (empty(trim($vt_value))) {
      $message = $textif;
    } else {
      $message = $textelse;
    }
    return $message;
  }



  //counting id
  //$c=[$pdo connection,table name,value to be searched (eg id)];
  function conuntingdbvalues($c = array())
  {
    //two values
    $pd = $c[0];
    $tab = $c[1];
    $val = $c[2];
    $sql = "SELECT $val FROM $tab ORDER BY $val";
    $stmt = $pd->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['countingdbv'] =  $data;
    $count = $stmt->rowCount();
    $_SESSION['count'] = $count;
    return $count;
  }
  function conunt_val($pd, $tab, $val, $id)
  {
    //counting values for a given group

    $sql = "SELECT $val FROM $tab WHERE $val = $id ORDER BY $val";
    $stmt = $pd->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['countingdbv'] =  $data;
    $count = $stmt->rowCount();
    $_SESSION['number'] = $count;
    return $count;
  }
  function finding_value($pd, $tab, $val, $id)
  {
    //obtaining values if a given group

    $sql = "SELECT * FROM $tab WHERE $val = $id";
    $stmt = $pd->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // $_SESSION['countingdbv'] =  $data;
    // $count = $stmt->rowCount();
    $_SESSION['group_members'] = $data;
    return $data;
  }

  function conuntingwithval2($c = array())
  {
    //two values
    $pd = $c[0];
    $tab = $c[1];
    $val = $c[2];
    $value = $c[3];
    $v = ':' . $value;

    $sql = "SELECT $val FROM $tab WHERE $val = $v ORDER BY $val";

    $stmt = $pd->prepare($sql);
    //$stmt->execute();
    $exe = $stmt->execute([$v => $value]);
    $data = $exe->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['countingdbv'] =  $data;
    $count = $exe->rowCount();
    $_SESSION['count'] = $count;
    return $count;
  }

  //INCOMPLETE PIECES

  function threetab($pdo)
  {
    echo 'cow';
    $sql = "SELECT farmer.farmer_id,farmer.farmer_first_name,farmer.farmer_last_name,farmer.farmer_phonenumber,farmer_group.group_id,farmer_group.group_name
    FROM farmer 
    INNER JOIN farmer_group
  ON farmer.farmer_group_group_id = farmer_group.group_id";
    $stmt = $pdo->prepare($sql);
    //$stmt->execute();
    
    $exe = $stmt->execute();
   
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['three'] = $data;
  
    var_dump($data);
  }

  function threetabs($pdo)
  {
    echo 'cow';
    $sql = "SELECT village.village_id,village.village_name,farmer_group.group_id,farmer_group.group_name,farmer.farmer_id,farmer.farmer_first_name,farmer.farmer_last_name,farmer.farmer_last_others
    FROM farmer 
    INNER JOIN farmer_group
  ON farmer.farmer_group_group_id = farmer_group.group_id INNER JOIN village ON farmer_group.village_village_id = village.village_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['threetabs'] = $data;
  }

  function pass_on($pdo)
  { //passer

    $sql = "SELECT pass_on.pass_from,farmer.farmer_first_name
    FROM farmer  
    INNER JOIN pass_on 
   ON farmer.farmer_id  = pass_on.allocate_input_allocate_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    var_dump($data);
  }//////////////////////////////////////six tables
  function sixtab($pdo){
    $sql =  "SELECT d.*, c.*, sc.*,p.*,v.*,fg.*,f.*
    FROM district d, county c,sub_county  sc,parish p,village v,farmer_group fg,farmer f
    WHERE d.district_id=c.district_district_id AND c.county_id=sc.county_county_id AND p.parish_id=v.parish_parish_id AND fg.group_id = f.farmer_group_group_id ";

    $stmt = $pdo->prepare($sql);
    //$stmt->execute();
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['join6'] = $data;

  }
  ///////////////////////pass ontrack
  function passontrack1($pdo){

    $sql =  "SELECT fg.*,f.*,p.*
    FROM farmer_group fg,farmer f,pass_on p
    WHERE  fg.group_id = f.farmer_group_group_id AND f.farmer_id = p.pass_from";

    $stmt = $pdo->prepare($sql);
    //$stmt->execute();
   $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['track'] = $data;
     $exe = count($data);

    
     $_SESSION['c'] = $exe;
  
    

  }
  function passontrack2($pdo)
  {
    
    $sql =  "SELECT f.*,p.*
    FROM farmer f,pass_on p
  f.farmer_id=p.pass_from ";

    $stmt = $pdo->prepare($sql);
    //$stmt->execute();
   $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['tracktwo'] = $data;
     $exe = count($data);

    
     $_SESSION['co'] = $exe;
  
    

  }
  function localone($pdo){
      $sql =  "SELECT d.*, c.county_name, sc.sub_county_name
    FROM district d, county c,sub_county  sc
    WHERE d.district_id=c.district_district_id AND c.county_id=sc.county_county_id";

    $stmt = $pdo->prepare($sql);
    //$stmt->execute();
   $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($data);
  }
  function twotab($pdo)
  {
   
    $sql = "SELECT farmer.farmer_id,farmer.farmer_first_name,farmer.farmer_last_name,farmer.farmer_phonenumber,farmer.farmer_group_group_id,pass_on.allocate_input_allocate_id
    FROM pass_on 
    INNER JOIN farmer
  ON pass_on.allocate_input_allocate_id = farmer.farmer_id";
    $stmt = $pdo->prepare($sql);
    //$stmt->execute();
   // echo 'cow2';
    $exe = $stmt->execute();
   // echo 'cow21';
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['two'] = $data;
    //echo 'cow22';
    var_dump($data);
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////////
  //join trial
  /////////////////////////////////////////////////////////////////////////////////////////////////////
  //$sql = "SELECT * FROM citys LEFT JOIN comments ON comments.city=citys.city WHERE citys.id=$id";
  function trial($pdo, $id)
  {

    $sql = "SELECT * FROM farmer WHERE farmer_group_group_id = :id";
    $stmt = $pdo->prepare($sql);
    $exc = $stmt->execute([':id' => $id]);
    $data = $exc->fetchAll(PDO::FETCH_ASSOC);
    $number = $data->rowCount();
    echo $number;
    print_r($data);
    return $number;
  }
  //validating text validating all xtics
  function validating_all($vt_value, $text)
  {
  }
  //validating text validating password
  function validate_password($vt_value, $len, $feedback1, $feedback2)
  {
    //Empty
    //length



  }
  //validating text validating_email
  function validate_email($vt_value, $text)
  {
  }
  //validating text validating_phone_number
  function validating_phone($vt_value, $text)
  {
  }

  function verifycompare($value1, $value2)
  {

    if ($value1 == $value2) {
    }
  }
}
