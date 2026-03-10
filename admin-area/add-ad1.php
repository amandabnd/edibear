<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  require_once("../classes/class.widgets.php");
  $adminHeader = new HEADER("add-ad1");
  $user = new USER();
  $widgets = new WIDGETS();
  $editMode = false;
  $currentad1Tag ="";
  $currentad1Title = "";
  $currentad1MainDescription = "";
  $currentad1VideoUrl = "";
  $currentad1VideoStatus = "";
  $currentad1MainImage = "";
  $currentad1adlink = "";
  $currentad1ID = 0;
  

  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
        $currentad1ID = (int)$_GET['id'];
        if ( $user->CountRows("ad1_details", array("id"=>$currentad1ID)) ) {
          $editMode = true;
          $ad1DetailsArr = $user->fetchAll(
            array("tag", "title", "image", "description", "video", "video_status", "adlink"),
            array("ad1_details"),
            array("id"=>$currentad1ID)
          )[0];
          $currentad1Tag = $ad1DetailsArr['tag'];
          $currentad1Title = $ad1DetailsArr['title'];
          $currentad1MainDescription = $ad1DetailsArr['description'];
          $currentad1VideoUrl = $ad1DetailsArr['video'];
          $currentad1VideoStatus = ($ad1DetailsArr['video_status']=='1') ? "checked" : "";
          $currentad1adlink = $ad1DetailsArr['adlink'];
          $currentad1MainImage = "src='".$widgets->createCachelessImage("../img/ad1/".$ad1DetailsArr['image'])."'";

        } else {
          $user->redirect("./add-ad1");
        }
      }
      if ( isset($_POST['addNewad1Submit']) || isset($_POST['updatead1Submit']) ) {
        $inputad1Tag = htmlspecialchars((isset($_POST['inputad1Tag'])) ? $_POST['inputad1Tag'] : "");
        $inputad1Title = htmlspecialchars((isset($_POST['inputad1Title'])) ? $_POST['inputad1Title'] : "");
        $inputad1MainDescription = strip_tags((isset($_POST['inputad1MainDescription'])) ? $_POST['inputad1MainDescription'] : "", "<br>");
        $inputad1VideoUrl = htmlspecialchars((isset($_POST['inputad1VideoUrl'])) ? $_POST['inputad1VideoUrl'] : "");
        $inputad1adlink = htmlspecialchars((isset($_POST['inputad1adlink'])) ? $_POST['inputad1adlink'] : "");
        $ad1VideoStatus = htmlspecialchars((isset($_POST['ad1VideoStatus'])) ? $_POST['ad1VideoStatus'] : "0");
        $howManyDescriptions = (int)(isset($_POST['howManyDescriptions'])) ? $_POST['howManyDescriptions'] : 1;
        if ( isset($_POST['addNewad1Submit']) ) {
          $ad1ID = $user->insertTable("ad1_details", array(
            "tag"=>$inputad1Tag,
            "title"=>$inputad1Title,
            "description"=>$inputad1MainDescription,
            "video"=>$inputad1VideoUrl,
            "video_status"=>$ad1VideoStatus,
            "adlink" =>$inputad1adlink
          ), true);
          //main Image
          $inputad1MainImage =$ad1ID.".".pathinfo($_FILES["inputad1MainImage"]["name"], PATHINFO_EXTENSION);
          move_uploaded_file($_FILES["inputad1MainImage"]["tmp_name"], "../img/ad1/" . $inputad1MainImage);
          $user->updateTable("ad1_details", array("image"=>$inputad1MainImage), array("id"=>$ad1ID));
          //ad1 decriptions
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputad1Description = strip_tags((isset($_POST["inputad1Description$i"])) ? $_POST["inputad1Description$i"] : "", "<br>");
            if ( !empty($inputad1Description) || !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
              $ad1DescriptionID = $user->insertTable("ad1_descriptions", array("ad1_id"=>$ad1ID,"description"=>$inputad1Description), true);
              $inputad1ImageOne = $inputad1ImageTwo = ""; 
              if ( !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputad1ImageOne ="$ad1ID-$ad1DescriptionID-1.".pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputad1ImageOne$i"]["tmp_name"], "../img/ad1/" . $inputad1ImageOne);
              } 
              if ( !empty(pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputad1ImageTwo ="$ad1ID-$ad1DescriptionID-2.".pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputad1ImageTwo$i"]["tmp_name"], "../img/ad1/" . $inputad1ImageTwo);
              }
              $user->updateTable("ad1_descriptions", array("image_01"=>$inputad1ImageOne,"image_02"=>$inputad1ImageTwo), array("id"=>$ad1DescriptionID));
            }
          }
          echo "<script>alert('Successfully added a new ad1');location.href='./createSiteMap?redirect=ad1'</script>";
          
        } else if ( isset($_POST['updatead1Submit']) ) {
          $user->updateTable("ad1_details", array(
            "tag"=>$inputad1Tag,
            "title"=>$inputad1Title,
            "description"=>$inputad1MainDescription,
            "video"=>$inputad1VideoUrl,
            "adlink" =>$inputad1adlink,
            "video_status"=>$ad1VideoStatus), array("id"=>$currentad1ID));
            
          if ( !empty( pathinfo($_FILES["inputad1MainImage"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/ad1/".$ad1DetailsArr['image']);
            $inputad1MainImage =$currentad1ID.".".pathinfo($_FILES["inputad1MainImage"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputad1MainImage"]["tmp_name"], "../img/ad1/" . $inputad1MainImage);
            $user->updateTable("ad1_details", array("image"=>$inputad1MainImage), array("id"=>$currentad1ID));
            echo "<script>alert('Successfully updated the ad1');location.href='./createSiteMap?redirect=ad1'</script>";
          }
          $sessionad1DescImgArr = $_SESSION['sessionad1DescImgArr'];
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputad1Description = strip_tags((isset($_POST["inputad1Description$i"])) ? $_POST["inputad1Description$i"] : "", "<br>");
            if ( count($sessionad1DescImgArr) >= $i ) {
              $inputad1ImageOne = $sessionad1DescImgArr[$i-1][1];
              $inputad1ImageTwo = $sessionad1DescImgArr[$i-1][3];
              if ( $sessionad1DescImgArr[$i-1][2]==-1 ) {
                unlink("../img/ad1/$inputad1ImageOne");
                $inputad1ImageOne = "";
              }
              if ( $sessionad1DescImgArr[$i-1][4]==-1 ) {
                unlink("../img/ad1/$inputad1ImageTwo");
                $inputad1ImageTwo = "";
              }
              $ad1DescriptionID = $sessionad1DescImgArr[$i-1][0];
              if ( !empty($inputad1Description) || 
                $sessionad1DescImgArr[$i-1][2]==1 || !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION)) ||
                $sessionad1DescImgArr[$i-1][4]==1 || !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION))  ) {
                  if ( !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputad1ImageOne ="$currentad1ID-$ad1DescriptionID-1.".pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputad1ImageOne$i"]["tmp_name"], "../img/ad1/" . $inputad1ImageOne);
                  } 
                  if ( !empty(pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputad1ImageTwo ="$currentad1ID-$ad1DescriptionID-2.".pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputad1ImageTwo$i"]["tmp_name"], "../img/ad1/" . $inputad1ImageTwo);
                  }
                  $user->updateTable("ad1_descriptions", array("description"=>$inputad1Description, "adlink"=>$inputad1adlink,"image_01"=>$inputad1ImageOne,"image_02"=>$inputad1ImageTwo), array("id"=>$ad1DescriptionID));
              } else {
                $user->deleteTableRow("ad1_descriptions",  array("id"=>$ad1DescriptionID));
              }
            } else {
              
              if ( !empty($inputad1Description) || !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $ad1DescriptionID = $user->insertTable("ad1_descriptions", array("ad1_id"=>$currentad1ID,"description"=>$inputad1Description), true);
                $inputad1ImageOne = $inputad1ImageTwo = ""; 
                if ( !empty(pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputad1ImageOne ="$currentad1ID-$ad1DescriptionID-1.".pathinfo($_FILES["inputad1ImageOne$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputad1ImageOne$i"]["tmp_name"], "../img/ad1/" . $inputad1ImageOne);
                } 
                if ( !empty(pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputad1ImageTwo ="$currentad1ID-$ad1DescriptionID-2.".pathinfo($_FILES["inputad1ImageTwo$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputad1ImageTwo$i"]["tmp_name"], "../img/ad1/" . $inputad1ImageTwo);
                }
                
                $user->updateTable("ad1_descriptions", array("image_01"=>$inputad1ImageOne,"image_02"=>$inputad1ImageTwo), array("adlink"=>$inputad1adlink), array("id"=>$ad1DescriptionID));
              }
            }
          }
          unset($_SESSION['sessionad1DescImgArr']);
          // echo "<script>alert('Successfully updated the ad1');location.href='./createSiteMap?redirect=ad1'</script>";
        }
        // UPDATE OVER

        // DELETE START
      } else if ( isset($_POST['confirmDeletead1Submit']) ) {
        
        $deletead1ID = (int)$_POST['deletead1ID'];
        foreach ( $user->fetchAll(array("image_01", "image_02"), array("ad1_descriptions"), array("ad1_id"=>$deletead1ID)) as $row ) {
          unlink("../img/ad1/".$row['image_01']);
          unlink("../img/ad1/".$row['image_02']);
        }
        echo "<script>alert('Successfully deleted ad1'); location.href='./createSiteMap?redirect=ad1'</script>";
        $user->deleteTableRow("ad1_details", array("id"=>$deletead1ID));
        foreach ( $user->fetchAll(array("image"), array("adlink"), array("ad1_details"), array("id"=>$deletead1ID)) as $row ) {
          unlink("../img/ad1/".$row['image']);
        }
        
        
        // $user->deleteTableRow("ad1_details", array("id"=>$deletead1ID));
        // echo "<script>alert('Successfully deleted ad1'); location.href='./createSiteMap?redirect=ad1'</script>";

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
  <?php echo $adminHeader->printAdminNav(); ?>
  <main class="main-content position-relative border-radius-lg">
    <!-- Navbar -->
    <?php echo $adminHeader->printAdminNav2(($editMode) ? "Edit ad1" : $adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form accept="" method="post" enctype="multipart/form-data">
                <!-- <div class="row"> -->
                  <?php
                    echo $widgets->inputGroup("ad1 link", "inputad1adlink", "col-md-12", $currentad1adlink);
                    // echo $widgets->inputGroup("ad1 Title", "inputad1Title", "col-md-6", $currentad1Title);
                  ?>
                <!-- </div> -->
                <div class="row border mx-3 mb-2">
                  <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">AD 1 Image</label>
                      <?php
                        if ( $editMode ) {
                          $mainImageRequired = "";
                        } else {
                          $mainImageRequired = "required";
                        }
                        echo "<input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event)' name='inputad1MainImage' $mainImageRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputad1MainImage' <?php echo $currentad1MainImage; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Main Description</label>
                      <textarea class='form-control' name='inputad1MainDescription' rows="4" required><?php echo $currentad1MainDescription;?></textarea>
                    </div>
                  </div>
                </div> -->
                <!-- Description -->
                <?php
                  // if ( !$editMode ) {
                  //   echo $widgets->addad1DesctiptionDiv(1); 
                  // } else {
                  //   $i=0;
                  //   $sessionad1DescImgArr = array();
                  //   foreach ( $user->fetchAll(array("id","description", "image_01", "image_02"), array("ad1_descriptions"), array("ad1_id"=>$currentad1ID)) as $row ) {
                  //     if ( $i>0 ) echo "<script>$('#addMoread1Description$i').css('display', 'none');</script>";
                  //     $sessionad1DescImgArr[$i] = array(
                  //       $row['id'],
                  //       $row['image_01'],
                  //       ($row['image_01']!= "") ? 1 : 0,
                  //       $row['image_02'],
                  //       ($row['image_02']!= "") ? 1 : 0
                  //     );
                  //     $i++;
                  //     echo $widgets->addad1DesctiptionDiv($i, $row); 
                  //   }
                  //   $_SESSION['sessionad1DescImgArr'] = $sessionad1DescImgArr;
                  //   echo "<script>
                  //   $(document).ready(function () {
                  //     $('input[name=howManyDescriptions]').val('$i');
                  //   })
                  //   </script>";
                  // }
                ?>
                <!-- <div class="row justify-content-center" id="addMoread1DescLoadingImage" style="display: none;">
                  <img src="../img/loading.gif" alt="Loading GIF" style="width: 100px;">
                </div>
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("ad1 Video", "inputad1VideoUrl", "col-md-6", $currentad1VideoUrl);
                    echo "<div class='col-md-6 float-left'>".$widgets->checkboxSwitch("", "ad1VideoStatus", $currentad1VideoStatus, "pt-5")."</div>";
                  ?>
                </div> -->
                <div class="row">
                  <div class="col-12">
                    <input type="hidden" name="howManyDescriptions" value="1">
                    <?php
                      if ( $editMode ) {
                        echo "
                        <input type='submit' class='btn btn-primary' value='Update ad1' name='updatead1Submit'>
                        <input type='button' class='btn btn-danger' value='Delete ad1' onclick='deletead1Submit()'>
                        ";
                      } else {
                        echo "<input type='submit' class='btn btn-success' value='Add ad1' name='addNewad1Submit'>";
                      }
                    ?>
                    <input type="button" class="btn btn-secondary" value="Cancel" onclick="location.href='./add-ad1'">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
    <div id="removead1DescImage"></div>
  </main>
  <?php 
    echo $adminHeader->printAdminFooterJS(); 
    if ( $editMode ) {
      echo "
      <div class='modal fade' id='confirmDeletead1Modal' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='staticBackdropLabel' aria-hidden='true' style='margin-top:200px'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='staticBackdropLabel'>Confirm Delete ad1</h5>
            </div>
            <div class='modal-body'>
            <form action='' class='text-center' method='post'>
                ad1 Title : $currentad1Title<br>
                ad1 Tag : $currentad1Tag<br>
                <input type='hidden' name='deletead1ID' value='$currentad1ID'>
              <br>
              <input type='submit' class='btn btn-danger btn-sm' name='confirmDeletead1Submit' value='Delete'>
              <button class='btn btn-sm btn-secondary' type='button' onclick='location.reload()'>Cancel</button>
            </form>
            </div>
          </div>
        </div>
      </div>
      ";
    }
  ?>
  <script>
    function loadImageFile(event, sessionTF=0) { 
      var imageDivID = event.target.name.replace("input", "output");
      var imageDivIdNumber = imageDivID.substr(-1);
      $("#"+imageDivID).addClass("border");
			var image = document.getElementById(imageDivID);
			image.src = URL.createObjectURL(event.target.files[0]);
      if (sessionTF) {
        $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
              changead1DescImage: imageDivIdNumber,
              changead1DescImageNo: imageDivID.substr(15, 3)
          },
          success: function(html) {
              $("#removead1DescImage").html(html).show();
          }
        }); 
      }
		}

    function updateHowManyDescriptions(val) {
      $("input[name='howManyDescriptions']").val(val);
      console.log( $("input[name='howManyDescriptions']").val() );
    }

    function addMoread1Descriptions(index) {
      $("#addMoread1Description"+index).css("display", "none");
      $("#addMoread1DescLoadingImage").css("display", "flex");
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          addMoread1Descriptions: index + 1
        },
        success: function(html) {
          $("#addMoread1Descriptions"+index).html(html).show();
          $("#addMoread1DescLoadingImage").css("display", "none");
          index++;
          $("input[name='howManyDescriptions']").val(index);
        }
      }); 
    }

    function removead1DescImage(index, imgNo) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removead1DescImage: index,
            removead1DescImageNo: imgNo
          },
          success: function(html) {
              $("#removead1DescImage").html(html).show();
          }
      }); 
    }

    function deletead1Submit() {
      $('#confirmDeletead1Modal').modal('show');
    }
  </script>
</body>

</html>