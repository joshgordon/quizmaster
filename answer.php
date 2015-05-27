<?php
//open a sqlite3 database.
$db = new SQLite3('database.db');
//todo check to make sure the user hasn't taken the quiz before. 
$results = Array();
$results["pass"] = false;
$results["error"]=false; 

//read which config file to deal with out of the database.
$statement = $db -> prepare('select * from users where id = :id;');
$statement -> bindValue(':id', $_GET["id"]);
$result = $statement -> execute(); 
$info = $result -> fetchArray(); 
if (!isset($_GET["id"]) || !$info) { 
  $results["error"] = true; 
  echo json_encode($results);
  exit();
}
$configfile = $info["config"]; 
$post_data= json_decode(file_get_contents('php://input'), true);
$config = json_decode(file_get_contents($configfile), true);

$num_questions = sizeof($config["questions"]);
$num_questions_right = 0;

foreach ($post_data["answers"] as $answer) {
  $split = explode('_', $answer);
  $serial = $split[0];
  $response = $split[1];
  foreach ($config["questions"] as $question) {
    if ($question["serial"] == $serial) {
      if ($question["correct"] == $response){
        $num_questions_right++;
      }
      break;
    }
  }
}
if ($num_questions_right / $num_questions > $config["pass_threshold"]) {
  $results["pass"] = true;
}
$pass = -1;
//Update the database. 
if ($results["pass"]) { 
  $pass = 1;
} else { 
  $pass = 0; 
}
$statement = $db -> prepare('update users set taken=1, pass=:pass, responses=:responses  where id=:id;'); 
$statement -> bindValue(':id', $_GET["id"]); 
$statement -> bindValue(':pass', $pass); 
$statement -> bindValue(':responses', json_encode($post_data["answers"])); 
$statement -> execute(); 

echo json_encode($results);
?>
