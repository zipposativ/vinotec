<?php
  function check_database(){
    // Name der Datenbankdatei
    $dbFile = 'wine_collection.db';

    // Prüfen, ob Datei existiert
    $exists = file_exists($dbFile);

    // Verbindung zur SQLite-Datenbank herstellen
    $db = new SQLite3($dbFile, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

    $db->enableExceptions(true);

    // Falls die Tabelle noch nicht existiert, erstellen
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS wines (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            wine_name VARCHAR(60),
            location VARCHAR(60),
            year VARCHAR(60),
            type VARCHAR(60),
            winemaker VARCHAR(90),
            size DECIMAL(15,2),
            price DECIMAL(15,2),
            description VARCHAR(900),
            shelf VARCHAR(20),
            compartment VARCHAR(20),
            evaluation INTEGER CHECK (evaluation BETWEEN 0 AND 5),
            count INTEGER
        );
    ";

    // SQL ausführen
    if(!$exists){
      $db->exec($createTableSQL);
    }

    // Verbindung schließen (optional, wird am Skriptende automatisch geschlossen)
    $db->exec('BEGIN');


  }

  function inventory(){
    $db = new SQLite3('wine_collection.db');
    if(isset($_GET['search']) && isset($_GET['sort']) && isset($_GET['order'])){
      $search = $_GET['search'];
      $order = $_GET['order'];
      $sort = $_GET['sort'];

      // Suchbegriff absichern (einfache Maskierung)
      $search = SQLite3::escapeString($search);
      $searchTerm = "'%" . $search . "%'";



      // Query
      $query = "
          SELECT * FROM wines
          WHERE wine_name LIKE $searchTerm
             OR location LIKE $searchTerm
             OR year LIKE $searchTerm
             OR type LIKE $searchTerm
             OR winemaker LIKE $searchTerm
             OR description LIKE $searchTerm
          ORDER BY $sort $order;
      ";

      $results = $db->query($query);
    }else{
      $results = $db->query("SELECT * FROM wines");
    }
    if(empty($results)){
      echo 'nothing here';
    }
    while ($row = $results->fetchArray()) {
      ?>
        <div id="vine_box">
          <div class="card" id="#<?php echo $row['id']; ?>">
            <div class="card-image">
              <figure class="image">
                <img src="wine_images/<?php echo $row['id']; ?>.png" alt="Wine Image" />
              </figure>
            </div>
            <div class="card-content">
              <p class="title is-4"><?php echo !empty($row['wine_name']) ? $row['wine_name'] : '-'; ?></p>
              <div class="tags">
                <span class="tag is-primary icon-text">
                  <span class="icon"><i class="fa-solid fa-location-dot"></i></span>
                  <span><?php echo !empty($row['location']) ? $row['location'] : '-'; ?></span>
                </span>
                <span class="tag is-primary icon-text">
                  <span class="icon"><i class="fa-solid fa-calendar"></i></span>
                  <span><?php echo !empty($row['year']) ? $row['year'] : '-'; ?></span>
                </span>
                <span class="tag is-primary icon-text">
                  <span class="icon"><i class="fa-solid fa-wine-bottle"></i></span>
                  <span><?php echo !empty($row['type']) ? $row['type'] : '-'; ?></span>
                </span>
                <span class="tag is-primary icon-text">
                  <span class="icon"><i class="fa-solid fa-building"></i></span>
                  <span><?php echo !empty($row['winemaker']) ? $row['winemaker'] : '-'; ?></span>
                </span>
                <span class="tag is-primary icon-text">
                  <span class="icon"><i class="fa-solid fa-up-right-and-down-left-from-center"></i></span>
                  <span><?php echo !empty($row['size']) ? $row['size'] : '-'; ?> &#8467;</span>
                </span>
                <span class="tag is-primary icon-text">
                  <span class="icon"><i class="fa-solid fa-money-bill"></i></span>
                  <span><?php echo !empty($row['price']) ? number_format($row['price'], 2, ',', ' ') : '-'; ?>€</span>
                </span>
              </div>
              <hr></hr>
              <div class="content">
                <?php echo !empty($row['description']) ? $row['description'] : '-'; ?>
              </div>
              <hr></hr>
                <table class="table is-fullwidth">
                  <tr>
                    <td class="has-text-weight-bold">Shelf</td>
                    <td><?php echo !empty($row['shelf']) ? $row['shelf'] : '-'; ?></td>
                  </tr>
                  <tr>
                    <td class="has-text-weight-bold">Compartment</td>
                    <td><?php echo !empty($row['compartment']) ? $row['compartment'] : '-'; ?></td>
                  </tr>
                </table>
              <hr></hr>
              <div id="evaluation">
                <?php
                  $evaluation = $row['evaluation']; // Stelle sicher, dass es eine Zahl ist
                  $maxStars = 5; // Maximale Anzahl Sterne

                  for($i = 1; $i <= $maxStars; $i++) {
                    if($i <= $evaluation) {
                      // Gefüllter Stern (mit has-text-primary)
                      echo '<span class="icon has-text-primary">';
                    } else {
                      // Leerer Stern (ohne has-text-primary)
                      echo '<span class="icon">';
                    }
                    echo '<i class="fa-solid fa-wine-bottle"></i>';
                    echo '</span>';
                  }

                ?>
              </div>
              <hr></hr>
              <div id="controls">
                <a href="?edit=<?php echo $row['id']; ?>" class="button icon is-primary has-text-white">
                  <i class="fa-solid fa-pen"></i>
                </a>
                <div class="input" id="total_count_box">
                  <a href="?" class="icon">
                    <i class="fa-solid fa-minus"></i>
                  </a>
                  <span class="text"><?php echo !empty($row['count']) ? $row['count'] : '0'; ?></span>
                  <a href="?" class="icon">
                    <i class="fa-solid fa-plus"></i>
                  </a>
                </div>
                <a href="?delete=<?php echo $row['id']; ?>" class="button icon is-danger has-text-white">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php
    }
  }


  function insertWine(){
    if(isset($_POST['create_wine'])){
      $db = new SQLite3('wine_collection.db');
      $id = $db->querySingle("SELECT MAX(id) as last_id from wines", true);
      if($id['last_id'] == null){
        $id = 1;
      }else{
        $id = $id['last_id'] + 1;
      }


      $wine_name = $_POST['wine_name'];
      $location = $_POST['location'];
      $year = $_POST['year'];
      $type = $_POST['type'];
      $winemaker = $_POST['winemaker'];
      $size = $_POST['size'];
      $price = $_POST['price'];
      $description = $_POST['description'];
      $shelf = $_POST['shelf'];
      $compartment = $_POST['compartment'];
      $evaluation = $_POST['evaluation'];
      $count = $_POST['count'];

      $db->query("INSERT INTO wines (id, wine_name, location, year, type, winemaker, size, price, description, shelf, compartment, evaluation, count)
           VALUES ('$id', '$wine_name', '$location', '$year', '$type', '$winemaker', '$size', '$price', '$description', '$shelf', '$compartment', '$evaluation', '$count')");

      if (isset($_FILES['wine_image']) && $_FILES['wine_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'wine_images/';

        $destination = $uploadDir . $id . '.png';

        // Einfach kopieren, egal welches Format
        move_uploaded_file($_FILES['wine_image']['tmp_name'], $destination);
      }

      $db->close();
      echo '<meta http-equiv="refresh" content="0;url=#'.$id.'">';
    }
  }

  function edit(){
    if(isset($_GET['edit']) && !empty($_GET['edit'])){
      $db = new SQLite3('wine_collection.db');
      $row = $db->querySingle("SELECT * FROM wines WHERE id LIKE ".$_GET['edit'].";", true);
      ?>
        <div id="edit_box" class="box">
          <div id="edit_box_header">
            <h4 class="title is-4 has-text-primary">Edit Item</h4>
            <a href="?" class="delete"></a>
          </div>
          <form method="post" autocomplete="off" enctype="multipart/form-data">
            <label class="label">Wine Name*</label>
            <input type="text" name="wine_name" class="input" placeholder="Tante Emma´s Eck e.G." required value="<?php echo $row['wine_name']; ?>">
            <label class="label">Location</label>
            <input type="text" name="location" class="input" placeholder="Lower Saxony, Hannover e.G." value="<?php echo $row['location']; ?>">
            <label class="label">Year</label>
            <input type="text" name="year" class="input" placeholder="2025 e.G." value="<?php echo $row['year']; ?>">
            <label class="label">Type</label>
            <input type="text" name="type" class="input" placeholder="Chardonnay e.G." value="<?php echo $row['type']; ?>">
            <label class="label">Winemaker</label>
            <input type="text" name="winemaker" class="input" placeholder="Emma e.G." value="<?php echo $row['winemaker']; ?>">
            <label class="label">Size*</label>
            <input type="text" name="size" class="input" placeholder="0,75 e.G." required value="<?php echo $row['size']; ?>">
            <label class="label">Price</label>
            <input type="number" name="price" class="input" placeholder="15 e.G." value="<?php echo $row['price']; ?>">
            <label class="label">Description</label>
            <input type="text" name="description" class="input" placeholder="Nice Wine e.G." value="<?php echo $row['description']; ?>">
            <label class="label">Shelf</label>
            <input type="text" name="shelf" class="input" placeholder="15 e.G." value="<?php echo $row['shelf']; ?>">
            <label class="label">Compartment</label>
            <input type="text" name="compartment" class="input" placeholder="3 e.G." value="<?php echo $row['compartment']; ?>">
            <label class="label">Evaluation*</label>
            <input type="number" name="evaluation" min="0" step="1" max="5" class="input" placeholder="0-5" required value="<?php echo $row['evaluation']; ?>">
            <label class="label">Number of bottles*</label>
            <input type="number" name="count" min="1" step="1" max="any" class="input" placeholder="15 e.G." required value="<?php echo $row['count']; ?>">
            <br>
            <br>
            <input type="submit" name="edit_wine" class="button has-background-link" value="Save">
            <?php
              if(isset($_POST['edit_wine']) && isset($_GET['edit'])){
                $id = $_GET['edit'];

                $wine_name = $_POST['wine_name'];
                $location = $_POST['location'];
                $year = $_POST['year'];
                $type = $_POST['type'];
                $winemaker = $_POST['winemaker'];
                $size = $_POST['size'];
                $price = $_POST['price'];
                $description = $_POST['description'];
                $shelf = $_POST['shelf'];
                $compartment = $_POST['compartment'];
                $evaluation = $_POST['evaluation'];
                $count = $_POST['count'];

                $db->query("UPDATE wines SET
                    wine_name = '$wine_name',
                    location = '$location',
                    year = '$year',
                    type = '$type',
                    winemaker = '$winemaker',
                    size = '$size',
                    price = '$price',
                    description = '$description',
                    shelf = '$shelf',
                    compartment = '$compartment',
                    evaluation = '$evaluation',
                    count = '$count'
                WHERE id = '$id'");
                echo '<meta http-equiv="refresh" content="0;url=?">';
              }
            ?>
          </form>
        </div>
      <?php
    }
  }

  function delete(){
    if(isset($_GET['delete'])){
      ?>
        <div id="delete_box" class="box">
          <a href="?" class="delete"></a>
          <i class="fa-regular fa-trash-can has-text-danger has-text-centered" id="delete_icon"></i>
          <h4 class="title is-4 has-text-centered has-text-weight-light">Are you sure you want to delete?</h4>
          <p class="text has-text-centered">Deleting this content is <b>permanent</b> and <b>cannot be restored</b>.</p>
          <br>
          <form method="post" autocomplete="off">
            <input type="submit" name="delete" value="Delete" class="button is-danger has-text-white">
            <a href="?" class="button">Cancel</a>
          </form>
        </div>
      <?php
      if(isset($_POST['delete']) && isset($_GET['delete'])){
        $id = $_GET['delete'];
        $db = new SQLite3('wine_collection.db');
        $stmt = $db->query("DELETE FROM wines WHERE id = ".$id.";");
        echo '<meta http-equiv="refresh" content="0;url=?">';
      }
    }
  }
?>
