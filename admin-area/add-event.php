<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");

  $adminHeader = new HEADER("add-event");
  $user = new USER();

  if ($user->is_loggedin()) {
    if ($user->checkTimeout()) {
      // Handle form submission
      if (isset($_POST['addNewEventSubmit'])) {
        $selectedCategory = isset($_POST['event_category']) ? $_POST['event_category'] : '';
        $newCategoryText  = trim(isset($_POST['new_category']) ? $_POST['new_category'] : '');
        $eventTitle       = htmlspecialchars(isset($_POST['event_title']) ? $_POST['event_title'] : '');
        $description      = strip_tags(isset($_POST['event_description']) ? $_POST['event_description'] : '', "<br>");
        $deadlineDate     = isset($_POST['deadline_date']) ? $_POST['deadline_date'] : null;

        // Resolve / create category
        $categoryId = null;
        if ($selectedCategory === 'other' && $newCategoryText !== '') {
          // Reuse existing category if same name already exists
          $existing = $user->fetchAll(
            array("id"),
            array("braveheart_categories"),
            array("name" => $newCategoryText)
          );
          if (!empty($existing)) {
            $categoryId = (int) $existing[0]['id'];
          } else {
            $categoryId = $user->insertTable(
              "braveheart_categories",
              array(
                "name"   => $newCategoryText,
                "status" => 1
              ),
              true
            );
          }
        } elseif (ctype_digit((string) $selectedCategory)) {
          $categoryId = (int) $selectedCategory;
        }

        // Insert event (without files first)
        $eventId = $user->insertTable(
          "braveheart_events",
          array(
            "category_id"   => $categoryId,
            "title"         => $eventTitle,
            "description"   => $description,
            "deadline_date" => $deadlineDate,
            "status"        => 1
          ),
          true
        );

        // File uploads directory
        $uploadDir = "../img/braveheart/";
        if (!is_dir($uploadDir)) {
          @mkdir($uploadDir, 0775, true);
        }

        // Main Image
        if (!empty($_FILES["main_image"]["name"])) {
          $mainImageExt  = pathinfo($_FILES["main_image"]["name"], PATHINFO_EXTENSION);
          $mainImageName = $eventId . "." . $mainImageExt;
          move_uploaded_file($_FILES["main_image"]["tmp_name"], $uploadDir . $mainImageName);
          $user->updateTable(
            "braveheart_events",
            array("main_image" => $mainImageName),
            array("id" => $eventId)
          );
        }

        // Application PDF
        if (!empty($_FILES["application_file"]["name"])) {
          $appExt  = pathinfo($_FILES["application_file"]["name"], PATHINFO_EXTENSION);
          $appName = $eventId . "-application." . $appExt;
          move_uploaded_file($_FILES["application_file"]["tmp_name"], $uploadDir . $appName);
          $user->updateTable(
            "braveheart_events",
            array("application_file" => $appName),
            array("id" => $eventId)
          );
        }

        echo "<script>alert('Successfully added Brave Heart challenge');location.href='./add-event'</script>";
        exit;
      }
    } else {
      $user->doLogout($adminHeader->getActivePage());
    }
  } else {
    $user->doLogout();
  }

  // Fetch existing Brave Heart categories
  try {
    $eventCategories = $user->fetchAll(
      array("id", "name"),
      array("braveheart_categories"),
      array(),
      "name ASC"
    );
  } catch (Exception $e) {
    $eventCategories = array();
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php echo $adminHeader->printAdminHeader(); ?>
</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 position-absolute w-100"></div>
  <?php echo $adminHeader->printAdminNav(); ?>

  <main class="main-content position-relative border-radius-lg">
    <?php echo $adminHeader->printAdminNav2("Add Brave Heart Challenge"); ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form method="post" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-control-label">Category</label>
                      <select name="event_category" id="eventCategory" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($eventCategories as $cat): ?>
                          <option value="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                          </option>
                        <?php endforeach; ?>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-control-label">Event Title</label>
                      <input type="text" name="event_title" class="form-control" required>
                    </div>
                  </div>
                </div>

                <div class="row" id="newCategoryWrapper" style="display: none;">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-control-label">New Category</label>
                      <textarea name="new_category" class="form-control" rows="2" placeholder="Enter new category name"></textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-control-label">Main Image</label>
                      <input class="form-control" type="file" accept="image/*" onchange="loadImageFile(event)" name="main_image" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3">
                      <img id="outputmain_image" style="max-height: 200px; max-width:100%" />
                    </p>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-control-label">Deadline Date</label>
                      <input type="date" name="deadline_date" class="form-control" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label class="form-control-label">Descriptions</label>
                      <textarea name="event_description" class="form-control" rows="5"></textarea>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-control-label">Application Upload</label>
                      <input class="form-control" type="file" accept="application/pdf" name="application_file">
                    </div>
                  </div>
                </div>

                <div class="row mt-3">
                  <div class="col-12">
                    <input type="submit" class="btn btn-success" name="addNewEventSubmit" value="Add Event">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
  </main>

  <?php echo $adminHeader->printAdminFooterJS(); ?>

  <script>
    document.getElementById('eventCategory').addEventListener('change', function () {
      var wrapper = document.getElementById('newCategoryWrapper');
      if (this.value === 'other') {
        wrapper.style.display = 'block';
      } else {
        wrapper.style.display = 'none';
      }
    });

    function loadImageFile(event) {
      var imageDivID = 'output' + event.target.name;
      var image = document.getElementById(imageDivID);
      if (image && event.target.files && event.target.files[0]) {
        image.src = URL.createObjectURL(event.target.files[0]);
      }
    }
  </script>
</body>
</html>

