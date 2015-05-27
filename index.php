<?php
//open a sqlite3 database.
$db = new sqlite3('database.db');

//Get the user's name or fail trying. 
$statement = $db -> prepare('select * from users where id = :id;');
$statement -> bindValue(':id', $_GET["id"]);
$result = $statement -> execute();
$info = $result -> fetchArray(); 
if (isset($_GET["id"]) && $info) { 

  $name = $info["name"]; 
  $taken = $info["taken"]; 
  $pass = $info["pass"];

  //Read in and parse the config from the config.json file.
  $config = file_get_contents("./config.json");
  $json = json_decode($config, true);

  ?>

  <html>
    <head>
      <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="animate.css">
      <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      <script>
      var id="<?php echo $_GET["id"];?>";
      $(function() {
        $("#quiz-form").on("submit", function() { 
          var answers = [];
          $('input:radio:checked').each(function(){ answers.push(this.id) })
          $.post("answer.php?id=" + id , JSON.stringify({id: id, answers: answers}), function(data) {
            console.log(data);
            $("#quiz").addClass("bounceOutUp");
            $('#quiz').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() { 
              $("#quiz").hide();
              console.log("DATA AGAIN", data);
              if (data.pass == true) { 
                $("#pass").show().addClass("bounceInUp");
              } else { 
                console.log("data.pass", data['"pass"']);
                $("#fail").show().addClass("bounceInUp");
              }
              });
          }, 'json');
          return false; 
        });
      });
      
      </script>
      <title><?php echo $json["title"];?></title>
    </head>
    <body>
      <div class="container">
        <?php if ($taken == 0) { ?>
          <h1><?php echo $json["title"];?></h1>
          <div class="animated" id="quiz">
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
          </div>
          <!-- pass and fail divs -->
          <div style="display: none" class="jumbotron animated" id="pass">
            <h1><?php echo $json["pass_title"];?></h1>
            <p><?php echo $json["pass_message"];?></p>
          </div>
          <div style="display: none" class="jumbotron animated" id="fail">
            <h1><?php echo $json["fail_title"];?></h1>
            <p><?php echo $json["fail_message"];?></p>
          </div>
        <?php } else { ?>
          <h1>Quiz already taken. You 
          <?php if ($pass == 0) { 
            echo "did not pass.";
          } else { 
            echo "passed";
          }?></h1>
        <?php } ?>
      </div>
    </body>
  </html> 
<?php } else { ?>
  <h1>User not found</h1>
<?php } ?>
