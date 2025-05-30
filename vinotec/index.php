<?php
	session_start();
  include("app/app.php");
  check_database();
?>

<!DOCTYPE html>
<html lang="de" data-theme="light">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vinotec</title>

  	<link rel="icon" href="image/logo.png">
  	<link rel="apple-touch-icon" href="image/logo.png">
  	<link rel="icon" type="image/x-icon" href="image/logo.ico">

    <link rel="stylesheet" href="lib/bulma/css/bulma.min.css">
    <link rel="stylesheet" href="lib/fontsawesome/css/all.min.css">

		<link rel="stylesheet" href="style/style.css?id<?php echo uniqid(); ?>">
  	<!-- <link rel="stylesheet" media="only screen and (max-device-width: 680px)" href="style/mobile.css?id<?php echo uniqid(); ?>"> -->



  	<meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Vinotec">

    <!-- <link rel="manifest" href="manifest.json"> -->

    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, minimal-ui">
    <meta name="format-detection" content="telephone=no">
  	<meta charset="utf-8">

  	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  	<meta http-equiv="Pragma" content="no-cache" />
  	<meta http-equiv="Expires" content="0" />

    <script src="app/source.js?id<?php echo uniqid(); ?>"></script>

  </head>
  <body>
    <div id="app">
      <div id="header">
        <div id="header_box">
          <span></span>
          <h2 class="title is-2 has-text-centered has-text-primary">Vinotec</h2>
          <div>
            <label for="create_checkbox" class="button icon has-background-link has-text-white" id="create_button"><i class="fa-solid fa-plus"></i></label>

          </div>
        </div>
        <div class="border"></div>
      </div>
      <div id="main">

        <div id="menu" class="box has-background-link">
          <form method="post" autocomplete="off" id="search_form">
            <input type="text" name="search" class="input" placeholder="Wine, Winemaker, Year ...">
            <div class="control has-icons-left">
              <div class="select">
                <select name="sort">
                  <option value="name_a_z" selected>Name A-Z</option>
                  <option value="name_z_a">Name Z-A</option>
                  <option value="winemaker_a_z">Winemaker A-Z</option>
                  <option value="winemaker_z_a">Winemaker Z-A</option>
                  <option value="location_a_z">Location A-Z</option>
                  <option value="location_z_a">Location Z-A</option>
                  <option value="year_up">Year Upper</option>
                  <option value="year_low">Year Lower</option>
                  <option value="price_up">Price Upper</option>
                  <option value="price_low">Price Lower</option>
                </select>
              </div>
              <div class="icon is-small is-left">
                <i class="fa-solid fa-arrow-up-wide-short"></i>
              </div>
            </div>
            <input type="submit" name="search_button" value="Go" class="button">
          </form>
          <?php
            if(isset($_POST['search_button'])){
              $search = $_POST['search'];
              $sort_option = $_POST['sort']; // Auswahl aus dem Dropdown
              $order = "ASC"; // Standardrichtung
              $sort = "wine_name"; // Standardspalte

              if ($sort_option == "name_a_z") {
                  $sort = "wine_name";
                  $order = "ASC";
              } elseif ($sort_option == "name_z_a") {
                  $sort = "wine_name";
                  $order = "DESC";
              } elseif ($sort_option == "winemaker_a_z") {
                  $sort = "winemaker";
                  $order = "ASC";
              } elseif ($sort_option == "winemaker_z_a") {
                  $sort = "winemaker";
                  $order = "DESC";
              } elseif ($sort_option == "location_a_z") {
                  $sort = "location";
                  $order = "ASC";
              } elseif ($sort_option == "location_z_a") {
                  $sort = "location";
                  $order = "DESC";
              } elseif ($sort_option == "year_up") {
                  $sort = "year";
                  $order = "ASC";
              } elseif ($sort_option == "year_low") {
                  $sort = "year";
                  $order = "DESC";
              } elseif ($sort_option == "price_up") {
                  $sort = "price";
                  $order = "ASC";
              } elseif ($sort_option == "price_low") {
                  $sort = "price";
                  $order = "DESC";
              }
              echo '<meta http-equiv="refresh" content="0;url=?search='.$search.'&sort='.$sort.'&order='.$order.'">';
            }
          ?>
        </div>
        <div id="inventory">
          <?php inventory(); ?>
        </div>
      </div>
      <input type="checkbox" id="create_checkbox">
      <div id="create_box" class="box">
        <div id="create_box_header">
          <h4 class="title is-4 has-text-primary">Create Item</h4>
          <label for="create_checkbox" class="delete"></label>
        </div>
        <form method="post" autocomplete="off" enctype="multipart/form-data">
          <img id="image_output" width="200" />
          <label class="label">Image*</label>
          <input type="file" id="wine_image" name="wine_image" onchange="loadFile(event)" accept=”.jpg, .jpeg, .png” class="input" required>
          <label for="wine_image" class="input">Upload Image</label>
          <label class="label">Wine Name*</label>
          <input type="text" name="wine_name" class="input" placeholder="Tante Emma´s Eck e.G." required>
          <label class="label">Location</label>
          <input type="text" name="location" class="input" placeholder="Lower Saxony, Hannover e.G.">
          <label class="label">Year</label>
          <input type="text" name="year" class="input" placeholder="2025 e.G.">
          <label class="label">Type</label>
          <input type="text" name="type" class="input" placeholder="Chardonnay e.G.">
          <label class="label">Winemaker</label>
          <input type="text" name="winemaker" class="input" placeholder="Emma e.G.">
          <label class="label">Size*</label>
          <input type="text" name="size" class="input" placeholder="0,75 e.G." required>
          <label class="label">Price</label>
          <input type="number" name="price" class="input" placeholder="15 e.G.">
          <label class="label">Description</label>
          <input type="text" name="description" class="input" placeholder="Nice Wine e.G.">
          <label class="label">Shelf</label>
          <input type="text" name="shelf" class="input" placeholder="15 e.G.">
          <label class="label">Compartment</label>
          <input type="text" name="compartment" class="input" placeholder="3 e.G.">
          <label class="label">Evaluation*</label>
          <input type="number" name="evaluation" min="0" step="1" max="5" class="input" placeholder="0-5" required>
          <label class="label">Number of bottles*</label>
          <input type="number" name="count" min="1" step="1" max="any" class="input" placeholder="15 e.G." required>
          <br>
          <br>
          <input type="submit" name="create_wine" class="button has-background-link" value="Create">
          <?php  insertWine(); ?>
        </form>
      </div>
      <?php
        edit();
        delete();
      ?>
    </div>
  </body>
</html>
