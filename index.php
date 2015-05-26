<?php
//open a sqlite3 database.
$db = new sqlite3('database.db');

//Get the user's name or fail trying. 
$statement = $db -> prepare('select * from users where id = :id;');
$statement -> bindValue(':id', $_GET["id"]);
$result = $statement -> execute();
$info = $result -> fetchArray(); 

$name = $info["name"]; 
$taken = $info["taken"]; 
$pass = $info["pass"];

if($taken == 0) { 

  //Read in and parse the config from the config.json file.
  $config = file_get_contents("./config.json");
  $json = json_decode($config, true);

  ?>

  <html>
    <head>
      <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
      <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      <script>
      var id="<?php echo $_GET["id"];?>";
      $(function() {
        $("#quiz-form").on("submit", function() { 
          var answers = [];
          $('input:radio:checked').each(function(){ answers.push(this.id) })
          $.post("answer.php?id=" + id , JSON.stringify({id: id, answers: answers}), function(data) { 
            console.log(data);
          });
          return false; 
        });
      });
      
      </script>
      <title><?php echo $json["title"];?></title>
    </head>
    <body>
      <h1><?php echo $json["title"];?></h1>
      <form id="quiz-form" action="none">
        <?php foreach ($json["questions"] as $question) { 
          echo $question["question"]; 
          echo "<ul>";
          foreach ($question["answers"] as $answer_num => $answer) {
            echo '<li><input type="radio" id="'.$question["serial"].'_'.$answer_num.'" name="'.$question["serial"].'" value='.$answer_num.'><label for="'.$question["serial"].'_'.$answer_num.'">'.$answer."</label></li>";
          }
          echo "</ul>";
        }
        ?>
        <input type="submit">
      </form>
    </body>
  </html> 
<?php } else { ?>

<h1>Quiz already taken. You 
<?php if ($pass == 0) { 
  echo "did not";
} else { 
  echo "did";
}?> pass</h1>
<?php } ?>