<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  $adminHeader = new HEADER("manage-admins");
  $user = new USER();
  $deleteAdminSubmit = "";

  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_POST['newAdminSubmit']) || isset($_POST['editAdminSubmit']) || isset($_POST['deleteAdminSubmit']) ){
        $inputUserFirstName = htmlspecialchars(isset($_POST['inputUserFirstName']) ? $_POST['inputUserFirstName'] : "");
        $inputUserLastName = htmlspecialchars(isset($_POST['inputUserLastName']) ? $_POST['inputUserLastName'] : "");
        $inputUserEmail = htmlspecialchars(isset($_POST['inputUserEmail']) ? $_POST['inputUserEmail'] : "");
        $inputUserMobile = htmlspecialchars(isset($_POST['inputUserMobile']) ? $_POST['inputUserMobile'] : "");
        $inputUserPassword = htmlspecialchars(isset($_POST['inputUserPassword']) ? $_POST['inputUserPassword'] : "");
        $inputUserConfirmPassword = htmlspecialchars(isset($_POST['inputUserConfirmPassword']) ? $_POST['inputUserConfirmPassword'] : "");
        if ( isset($_POST['newAdminSubmit']) || isset($_POST['editAdminSubmit']) ){
          if ( $inputUserPassword == $inputUserConfirmPassword ) {
            if ( isset($_POST['newAdminSubmit']) ) {
              $returnString = $user->adminRegister($inputUserFirstName, $inputUserLastName, $inputUserEmail, $inputUserPassword, $inputUserMobile);
            } else if ( isset($_POST['editAdminSubmit']) ) {
              $UserHiddenID = (int)isset($_POST['UserHiddenID']) ? $_POST['UserHiddenID'] : "";
              $returnString = $user->adminRegister($inputUserFirstName, $inputUserLastName, $inputUserEmail, $inputUserPassword, $inputUserMobile, $UserHiddenID);
            }
            echo "<script>alert('$returnString');location.href='./manage-admins'</script>";
          } else {
            echo "<script>alert('Passwords are not matching');location.href='./manage-admins'</script>";
          }
        } else if ( isset($_POST['deleteAdminSubmit']) ) {
          $UserHiddenID = (int)isset($_POST['UserHiddenID']) ? $_POST['UserHiddenID'] : "";
          $deleteAdminSubmit = $user->confirmDeleteModal($UserHiddenID, $inputUserFirstName." ".$inputUserLastName, $inputUserEmail, "Confrim Delete an Admin", "manage-admins");
        } 
      } else if ( isset($_POST['confirmDeleteSubmit']) ) {
        $deleteNameID = (int)isset($_POST['deleteNameID']) ? $_POST['deleteNameID'] : "";
        $user->updateTable("user_table", array("delete_status"=>1), array("id"=>$deleteNameID));
        echo "<script>alert('Successfully Deleted an Admin');location.href='./manage-admins';</script>";
      }
    } else {
      $user->doLogout($adminHeader->getActivePage());
    }
  } else {
    $user->doLogout();
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php echo $adminHeader->printAdminHeader(); ?>
  <meta property='og:title' content='Traveylo | Sri Lanka Tour Packages | Travel Agent in Sri Lanka'/>
  <meta name='description' content='“Ayubowan!” Traveylo.com provides tour packages covering the most beautiful places 
in Sri Lanka, and you can travel in luxury with your own vehicle around 
our beautiful country. So reserve your tour with us.' />
<meta name='keywords' content='Travel Agents In Sri Lanka / Sri Lanka Tourism / Sri Lanka Tourist Destinations / Places To Visit In Sri Lanka With Family / How To Travel In Sri Lanka / Sri Lanka Tours & Travels / Tour Packages In Sri Lanka / Sri Lanka Itinerary / Sri Lanka Travel Guide /Sri Lanka HotelsSri Lanka Tour Operators /Sri Lanka Budgets Tours /Small Group Tour In Sri Lanka / Sri Lanka Holiday Packages /Sri Lanka Tour Packages For Couple / Sri Lanka Tour Packages For Family /Sri Lanka Tour Packages Price / What To Do In Sri Lanka /Popular Destinations In Sri Lanka' />
</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <?php echo $adminHeader->printAdminNav();?>
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <?php echo $adminHeader->printAdminNav2($adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <h4>Admins</h4>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">#</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">First Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Last Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Email</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Mobile Number</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $rowNumber = 0;
                      foreach ( $user->fetchAll("", array("user_table"), array("delete_status"=>"0")) as $rowFetchUsers ) {
                        $rowNumber++;
                        $userID = $rowFetchUsers['id'];
                        $userFirstName = $rowFetchUsers['first_name'];
                        $userLastName = $rowFetchUsers['last_name'];
                        $userEmail = $rowFetchUsers['login_email'];
                        $userMobile = $rowFetchUsers['mobile_number'];
                        if ( $userID != $user->sessionUser()) {
                          $onclickEvent = "onclick='editUser($userID)'";
                          $cursorPointer = "cursorPointer";
                          $tableRowColor = "";
                        } else {
                          $onclickEvent = $cursorPointer = "";
                          $tableRowColor = "style='background-color:#e8f4f8 '";
                        }
                        echo "
                        <tr $tableRowColor>
                          <td class='align-middle text-center $cursorPointer' $onclickEvent>
                            <span class='text-secondary text-xs font-weight-bold'>$rowNumber</span>
                          </td>
                          <td class='align-middle text-center'>
                            <span class='text-secondary text-xs font-weight-bold' id='firstName$userID'>$userFirstName</span>
                          </td>
                          <td class='align-middle text-center'>
                            <span class='text-secondary text-xs font-weight-bold' id='lastName$userID'>$userLastName</span>
                          </td>
                          <td class='align-middle text-center'>
                            <span class='text-secondary text-xs font-weight-bold' id='email$userID'>$userEmail</span>
                          </td>
                          <td class='align-middle text-center'>
                            <span class='text-secondary text-xs font-weight-bold' id='mobile$userID'>$userMobile</span>
                          </td>
                        </tr>
                        ";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <h5 id="addNewUserCardHeader">Add a new Admin</h5>
            </div>
            <div class="card-body p-3">
              <form action="" method="post">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">First Name</label>
                      <input class="form-control" type="text" name="inputUserFirstName" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Last Name</label>
                      <input class="form-control" type="text" name="inputUserLastName" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Email Address</label>
                      <input class="form-control" type="email" name="inputUserEmail" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Phone Number</label>
                      <input class="form-control" type="tel" name="inputUserMobile" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Password</label>
                      <input class="form-control" type="password" name="inputUserPassword" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Confirm Password</label>
                      <input class="form-control" type="password" name="inputUserConfirmPassword" required>
                      <div style="color:red" id="passwordMissMatchErr"></div>
                    </div>
                  </div>
                </div>
                <div style="float: right;">
                  <input type="hidden" value="" name="UserHiddenID">
                  <input type="submit" class="btn btn-success btn-sm ms-auto" name="newAdminSubmit" value="Add">
                  <input type="submit" class="btn btn-primary btn-sm ms-auto" name="editAdminSubmit" value="Edit" disabled>
                  <input type="submit" class="btn btn-danger btn-sm ms-auto" name="deleteAdminSubmit" value="Delete" disabled>
                  <input type="button" class="btn btn-secondary btn-sm ms-auto" value="Cancel" onclick="location.reload()">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php 
        echo $adminHeader->printAdminFooter();
        if ($deleteAdminSubmit != "") {
          echo $deleteAdminSubmit;
        }
        if ( isset($_POST['confirmDeleteUserSubmit']) ) {
          $UserNameID = isset($_POST['UserNameID']) ? $_POST['UserNameID'] : "0";
          $UserNameID = (int)$UserNameID;
          $stmtDeleteUser = $connection->prepare("UPDATE `user_table` SET `delete_status`='1' WHERE `id`=? ");
          $stmtDeleteUser->bind_param("i", $UserNameID);
          $stmtDeleteUser->execute();
          $stmtDeleteUser->close();
          echo "<script>alert('Successfully Deleted a User');location.href='./manage-admins';</script>";
        }
      ?>
    </div>
  </main>
  <?php echo $adminHeader->printAdminFooterJS(); ?>
  <script>
    $("input[type='password']").on("keyup", function(){
      if ( $("input[name='inputUserPassword']").val() == $("input[name='inputUserConfirmPassword']").val() ) {
        if ( $("#addNewUserCardHeader").text()=="Add a new Admin" ) {
          $("input[name='newAdminSubmit']").prop("disabled",false);
        }
        $("#passwordMissMatchErr").text("");
      } else {
        $("#passwordMissMatchErr").text("Not Matching");
        $("input[name='newAdminSubmit']").prop("disabled",true);
      }
    });
    function editUser(userID) {
      $("#addNewUserCardHeader").text("Edit a User");
      $("input[name='inputUserFirstName']").val($("#firstName"+userID).text());
      $("input[name='inputUserLastName']").val($("#lastName"+userID).text());
      $("input[name='inputUserEmail']").val($("#email"+userID).text());
      $("input[name='inputUserMobile']").val($("#mobile"+userID).text());
      $("input[name='UserHiddenID']").val(userID);
      $("input[name='newAdminSubmit']").prop("disabled",true);
      $("input[name='editAdminSubmit']").prop("disabled",false);
      $("input[name='deleteAdminSubmit']").prop("disabled",false);
    }
  </script>
</body>

</html>