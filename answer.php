<?php
//open a sqlite3 database.
$db = new SQLite3('database.db');
//todo check to make sure the user hasn't taken the quiz before. 
$results = Array();
$results["pass"] = false;
$results["error"]=false; 
$post_data= json_decode(file_get_contents('php://input'), true);
$config = json_decode(file_get_contents('config.json'), true);

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
$pass = -1
//Update the database. 
if ($results["pass"]) { 
  $pass = 1;
} else { 
  $pass = 0; 
}
$statement = $db -> prepare('update users set taken=1, pass=:pass where id=:id;'); 
$statement -> bindValue(':id', $_GET["id"]); 
$statement -> bindValue(':pass', $pass); 
$statement -> execute(); 

echo json_encode($results);
?>
