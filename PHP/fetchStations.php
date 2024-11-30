<?php
  require 'db.php';

  // Fetch shop data
  $sql = "SELECT * FROM stations";

  $result = $conn->query($sql);

  $stations = array();

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $stations[] = $row;
    }
  }

  echo json_encode($stations);

  $conn->close();



?>