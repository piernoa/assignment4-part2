<?php
include "connection.php";
// check for add post
if(isset($_POST["name"]) && isset($_POST["category"]) && isset($_POST["length"])) {
  // let's add this pup to the server!
  $name=$_POST["name"];
  $category=$_POST["category"];
  $length= (int) $_POST["length"];
  if ($name == "" || $category == "" || $length <= 0 || !is_numeric($length)) {
    if ($length <= 0 || !is_numeric($length)) {
      echo '<br>' .'<p class="error"> The length of the video must be greater than zero. Otherwise there\'s nothing to watch!</p>' . '<br>';
    }
    if ($name == "") {
      echo '<br>' . '<p class="error">' . "The video must have a name!" . '</p>' . '<br>';
    }
    if ($category == "") {
      echo '<br>' . '<p class="error">' . "The video must have a category!" . '</p>' . '<br>';
    }
  } else {
    //echo "name: $name, cat: $category, Len: $length,  ";
    // prepare statement
    if (!($insert = $mysqli->prepare("INSERT INTO Videos(name, category, length) VALUES(?, ?, ?)"))) {
      echo "Uh oh. Prepare statement failed : (" . $insert->errno . ") " . $insert->error;
    }
    // bind
    if (!$insert->bind_param("ssi", $name, $category, $length)) {
      echo "Uh oh. Bind statement failed : (" . $insert->errno . ") " . $insert->error;
    }
    // execute
    if (!$insert->execute()) {
      echo "Uh oh. Execute statement failed : (" . $insert->errno . ") " . $insert->error;
    }
    $insert->close();
  }
}
// check for delete post
if(isset($_POST["dname"])) {
  $deleteMe = $_POST["dname"];
  //echo "We got ourselves a delete! " . $deleteMe ;
  // prepare statement
  if (!($deleteItem = $mysqli->prepare("DELETE FROM Videos WHERE name = ?"))) {
    echo "Uh oh. Prepare statement failed : (" . $deleteItem->errno . ") " . $deleteItem->error;
  }
  // bind
  if (!$deleteItem->bind_param("s", $deleteMe)) {
    echo "Uh oh. Bind statement failed : (" . $deleteItem->errno . ") " . $deleteItem->error;
  }
  // execute
  if (!$deleteItem->execute()) {
    echo "Uh oh. Execute statement failed : (" . $deleteItem->errno . ") " . $deleteItem->error;
  }
  $deleteItem->close();
}
// check for check out post
if(isset($_POST["cname"])) {
  $checkMe = $_POST["cname"];
  //echo "We got ourselves a delete! " . $deleteMe ;
  // prepare statement
  if (!($checkItem = $mysqli->prepare("UPDATE Videos SET rented = 1 - rented WHERE name = ?"))) {
    echo "Uh oh. Prepare statement failed : (" . $checkItem->errno . ") " . $checkItem->error;
  }
  // bind
  if (!$checkItem->bind_param("s", $checkMe)) {
    echo "Uh oh. Bind statement failed : (" . $checkItem->errno . ") " . $checkItem->error;
  }
  // execute
  if (!$checkItem->execute()) {
    echo "Uh oh. Execute statement failed : (" . $checkItem->errno . ") " . $checkItem->error;
  }
  $checkItem->close();
}
// check for delete All
if(isset($_POST["removeAll"])) {

  if (!($deleteAll = $mysqli->prepare("DELETE FROM Videos"))) {
    echo "Uh oh. Prepare statement failed : (" . $mysqli->errno . ") " . $mysqli->error;
  }
  // execute
  if (!$deleteAll->execute()) {
    echo "Uh oh. Execute statement failed : (" . $deleteAll->errno . ") " . $deleteAll->error;
  }
  $deleteAll->close();

}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Index</title>
  <link rel="stylesheet" href="style.css" type="text/css">
  <script rel="text/javascript" src="app.js"></script>
</head>
<body>
  <h1>Awesome Video Database !</h1>
  <div class="dottedBox">
    <h4>Add Video</h4>
    <div class="formBox">
      <form action=
      <?php
      $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
      $filePath = implode('/', $filePath);
      $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
      echo $redirect . "/index.php";
      ?>
      method="post">
      <div class="inputItem">
        <p class="label"> Name:&nbsp; </p>  <input type="text" name="name" required>
      </div>
      <div class="inputItem">
        <p class="label"> Category:&nbsp; </p> <input type="text" name="category" required>
      </div>
      <div class="inputItem">
        <p class="label"> Length: &nbsp;</p> <input type="number" name="length" required>
      </div>
      <div class="inputItem">
        <input type="submit" value="Add">
      </div>
    </form>
  </div>
</div>
<div class="dottedBox">
  <h4>Filter Videos</h4>
  <form action=
  <?php
  $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
  $filePath = implode('/', $filePath);
  $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
  echo $redirect . "/index.php";
  ?>
  method="post">
  <select name="filters">
    <option value="*"> All </option>
    <?php
    // query database for unique categories
    // prepare statement
    if (!($filter = $mysqli->prepare("SELECT DISTINCT category FROM Videos"))) {
      echo "Uh oh. Prepare statement failed : (" . $mysqli->errno . ") " . $mysqli->error;
    }
    // execute
    if (!$filter->execute()) {
      echo "Uh oh. Execute statement failed : (" . $filter->errno . ") " . $filter->error;
    }
    if (!$filter->bind_result($categories)) {
      echo "Binding output parameters failed: (" . $filter->errno . ") " . $filter->error;
    }
    while ($filter->fetch()) {
      echo '<option value="'. $categories . '">' . $categories .'</option>';
    }
    ?>
  </select>
  <input type="submit" value="Filter">
</form>
</div>
<div class="dottedBox">
  <h4>Videos!</h4>
  <!-- Table of videos. list name, category, length and checked out or available w delete button -->
  <?php
  // get the data
  // prepare
  if (isset($_POST["filters"]) && $_POST["filters"] != "*") {
    $category = $_POST["filters"];
    //echo $category;
    if (!($getData = $mysqli->prepare("SELECT name, category, length, rented FROM Videos WHERE category = ?"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    // bind
    if (!$getData->bind_param("s", $category)) {
      echo "Uh oh. Bind statement failed : (" . $insert->errno . ") " . $insert->error;
    }
  } else {
    if (!($getData = $mysqli->prepare("SELECT name, category, length, rented FROM Videos"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
  }

  //execute
  if (!$getData->execute()) {
    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  // bind results
  $name= NULL;
  $category= NULL;
  $length = NULL;
  $rented = NULL;

  echo '<table style="width:100%">
  <tr>
  <td>Name</td>
  <td>Category</td>
  <td>Length</td>
  <td>Status</td>
  <td>Check Out</td>
  <td>Remove</td>
  </tr>';


  if (!$getData->bind_result($name, $category, $length, $rented)) {
    echo "Binding output parameters failed: (" . $getData->errno . ") " . $getData->error;
  }
  while ($getData->fetch()) {
    echo '<tr> <td>' . $name . '</td>' . '<td>' . $category . '</td>' . '<td>' . $length . '</td>' . '<td>';
    if ($rented == 0) {
      echo "Available" ;
    }  else  {
      echo "Checked Out";
    }
    echo '</td> <td>' ;
    // check out button
    echo '<form action=';


    $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    echo $redirect . "/index.php";

    echo ' method="post">
    <input type="text" name="cname" value="'.$name .'" class="hidden"><br>
    <input type="submit" value="Check Out">
    </form>'
    . '</td> <td>';

    // remove button
    echo '<form action=';


    $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    echo $redirect . "/index.php";

    echo ' method="post">
    <input type="text" name="dname" value="'.$name .'" style="display:none"><br>
    <input type="submit" value="Delete">
    </form>'
    . '</td> </tr>';
    //printf("name = %s , category = %s, length = %i , rented = %i   \n", $name, $category, $length, $rented);
  }

  echo '</table>';

  // loop through and create a table
  ?>
</div>
<div class="dottedBox">
  <!-- Delete everything! -->
  <h4>Delete Everything!</h4>
  <form action=
  <?php
  $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
  $filePath = implode('/', $filePath);
  $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
  echo $redirect . "/index.php";
  ?>
  method="post">
  <input type="text" name="removeAll" class="hidden"><br>
  <input type="submit" value="DELETE ALL VIDEOS!">
</form>
</div>
</body>
</html>
