<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  require_once("../classes/class.widgets.php");
  $adminHeader = new HEADER("add-ad2");
  $user = new USER();
  $widgets = new WIDGETS();
  $editMode = false;
  $currentad2Tag = "";
  $currentad2Title = "";
  $currentad2MainDescription = "";
  $currentad2VideoUrl = "";
  $currentad2VideoStatus = "";
  $currentad2MainImage = "";
  $currentad2adlink = "";
  $currentad2ID = 0;

  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
        $currentad2ID = (int)$_GET['id'];
        if ( $user->CountRows("ad2_details", array("id"=>$currentad2ID)) ) {
          $editMode = true;
          $ad2DetailsArr = $user->fetchAll(
            array("tag", "title", "image", "description", "video", "video_status", "adlink"),
            array("ad2_details"),
            array("id"=>$currentad2ID)
          )[0];
          $currentad2Tag = $ad2DetailsArr['tag'];
          $currentad2Title = $ad2DetailsArr['title'];
          $currentad2MainDescription = $ad2DetailsArr['description'];
          $currentad2VideoUrl = $ad2DetailsArr['video'];
          $currentad2VideoStatus = ($ad2DetailsArr['video_status']=='1') ? "checked" : "";
          $currentad2adlink = $ad2DetailsArr['adlink'];
          $currentad2MainImage = "src='".$widgets->createCachelessImage("../img/ad2/".$ad2DetailsArr['image'])."'";

        } else {
          $user->redirect("./add-ad2");
        }
      }
      if ( isset($_POST['addNewad2Submit']) || isset($_POST['updatead2Submit']) ) {
        $inputad2Tag = htmlspecialchars((isset($_POST['inputad2Tag'])) ? $_POST['inputad2Tag'] : "");
        $inputad2Title = htmlspecialchars((isset($_POST['inputad2Title'])) ? $_POST['inputad2Title'] : "");
        $inputad2MainDescription = strip_tags((isset($_POST['inputad2MainDescription'])) ? $_POST['inputad2MainDescription'] : "", "<br>");
        $inputad2VideoUrl = htmlspecialchars((isset($_POST['inputad2VideoUrl'])) ? $_POST['inputad2VideoUrl'] : "");
        $inputad2adlink = htmlspecialchars((isset($_POST['inputad2adlink'])) ? $_POST['inputad2adlink'] : "");
        $ad2VideoStatus = htmlspecialchars((isset($_POST['ad2VideoStatus'])) ? $_POST['ad2VideoStatus'] : "0");
        $howManyDescriptions = (int)(isset($_POST['howManyDescriptions'])) ? $_POST['howManyDescriptions'] : 1;
        if ( isset($_POST['addNewad2Submit']) ) {
          $ad2ID = $user->insertTable("ad2_details", array(
            "tag"=>$inputad2Tag,
            "title"=>$inputad2Title,
            "description"=>$inputad2MainDescription,
            "video"=>$inputad2VideoUrl,
            "video_status"=>$ad2VideoStatus,
            "adlink" =>$inputad2adlink
          ), true);
          //main Image
          $inputad2MainImage =$ad2ID.".".pathinfo($_FILES["inputad2MainImage"]["name"], PATHINFO_EXTENSION);
          move_uploaded_file($_FILES["inputad2MainImage"]["tmp_name"], "../img/ad2/" . $inputad2MainImage);
          $user->updateTable("ad2_details", array("image"=>$inputad2MainImage), array("id"=>$ad2ID));
          //ad2 decriptions
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputad2Description = strip_tags((isset($_POST["inputad2Description$i"])) ? $_POST["inputad2Description$i"] : "", "<br>");
            if ( !empty($inputad2Description) || !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
              $ad2DescriptionID = $user->insertTable("ad2_descriptions", array("ad2_id"=>$ad2ID,"description"=>$inputad2Description), true);
              $inputad2ImageOne = $inputad2ImageTwo = ""; 
              if ( !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputad2ImageOne ="$ad2ID-$ad2DescriptionID-1.".pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputad2ImageOne$i"]["tmp_name"], "../img/ad2/" . $inputad2ImageOne);
              } 
              if ( !empty(pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputad2ImageTwo ="$ad2ID-$ad2DescriptionID-2.".pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputad2ImageTwo$i"]["tmp_name"], "../img/ad2/" . $inputad2ImageTwo);
              }
              $user->updateTable("ad2_descriptions", array("image_01"=>$inputad2ImageOne,"image_02"=>$inputad2ImageTwo), array("id"=>$ad2DescriptionID));
            }
          }
          echo "<script>alert('Successfully added a new ad2');location.href='./createSiteMap?redirect=ad2'</script>";
          
        } else if ( isset($_POST['updatead2Submit']) ) {
          $user->updateTable("ad2_details", array(
            "tag"=>$inputad2Tag,
            "title"=>$inputad2Title,
            "description"=>$inputad2MainDescription,
            "video"=>$inputad2VideoUrl,
            "adlink" =>$inputad2adlink,
            "video_status"=>$ad2VideoStatus), array("id"=>$currentad2ID));
            
          if ( !empty( pathinfo($_FILES["inputad2MainImage"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/ad2/".$ad2DetailsArr['image']);
            $inputad2MainImage =$currentad2ID.".".pathinfo($_FILES["inputad2MainImage"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputad2MainImage"]["tmp_name"], "../img/ad2/" . $inputad2MainImage);
            $user->updateTable("ad2_details", array("image"=>$inputad2MainImage), array("id"=>$currentad2ID));
            echo "<script>alert('Successfully updated the ad2');location.href='./createSiteMap?redirect=ad2'</script>";
          }
          $sessionad2DescImgArr = $_SESSION['sessionad2DescImgArr'];
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputad2Description = strip_tags((isset($_POST["inputad2Description$i"])) ? $_POST["inputad2Description$i"] : "", "<br>");
            if ( count($sessionad2DescImgArr) >= $i ) {
              $inputad2ImageOne = $sessionad2DescImgArr[$i-1][1];
              $inputad2ImageTwo = $sessionad2DescImgArr[$i-1][3];
              if ( $sessionad2DescImgArr[$i-1][2]==-1 ) {
                unlink("../img/ad2/$inputad2ImageOne");
                $inputad2ImageOne = "";
              }
              if ( $sessionad2DescImgArr[$i-1][4]==-1 ) {
                unlink("../img/ad2/$inputad2ImageTwo");
                $inputad2ImageTwo = "";
              }
              $ad2DescriptionID = $sessionad2DescImgArr[$i-1][0];
              if ( !empty($inputad2Description) || 
                $sessionad2DescImgArr[$i-1][2]==1 || !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION)) ||
                $sessionad2DescImgArr[$i-1][4]==1 || !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION))  ) {
                  if ( !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputad2ImageOne ="$currentad2ID-$ad2DescriptionID-1.".pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputad2ImageOne$i"]["tmp_name"], "../img/ad2/" . $inputad2ImageOne);
                  } 
                  if ( !empty(pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputad2ImageTwo ="$currentad2ID-$ad2DescriptionID-2.".pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputad2ImageTwo$i"]["tmp_name"], "../img/ad2/" . $inputad2ImageTwo);
                  }
                  $user->updateTable("ad2_descriptions", array("description"=>$inputad2Description, "adlink"=>$inputad2adlink,"image_01"=>$inputad2ImageOne,"image_02"=>$inputad2ImageTwo), array("id"=>$ad2DescriptionID));
              } else {
                $user->deleteTableRow("ad2_descriptions",  array("id"=>$ad2DescriptionID));
              }
            } else {
              
              if ( !empty($inputad2Description) || !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $ad2DescriptionID = $user->insertTable("ad2_descriptions", array("ad2_id"=>$currentad2ID,"description"=>$inputad2Description), true);
                $inputad2ImageOne = $inputad2ImageTwo = ""; 
                if ( !empty(pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputad2ImageOne ="$currentad2ID-$ad2DescriptionID-1.".pathinfo($_FILES["inputad2ImageOne$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputad2ImageOne$i"]["tmp_name"], "../img/ad2/" . $inputad2ImageOne);
                } 
                if ( !empty(pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputad2ImageTwo ="$currentad2ID-$ad2DescriptionID-2.".pathinfo($_FILES["inputad2ImageTwo$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputad2ImageTwo$i"]["tmp_name"], "../img/ad2/" . $inputad2ImageTwo);
                }
                
                $user->updateTable("ad2_descriptions", array("image_01"=>$inputad2ImageOne,"image_02"=>$inputad2ImageTwo), array("adlink"=>$inputad2adlink), array("id"=>$ad2DescriptionID));
              }
            }
          }
          unset($_SESSION['sessionad2DescImgArr']);
          // echo "<script>alert('Successfully updated the ad2');location.href='./createSiteMap?redirect=ad2'</script>";
        }
        // UPDATE OVER

        // DELETE START
      } else if ( isset($_POST['confirmDeletead2Submit']) ) {
        
        $deletead2ID = (int)$_POST['deletead2ID'];
        foreach ( $user->fetchAll(array("image_01", "image_02"), array("ad2_descriptions"), array("ad2_id"=>$deletead2ID)) as $row ) {
          unlink("../img/ad2/".$row['image_01']);
          unlink("../img/ad2/".$row['image_02']);
        }
        echo "<script>alert('Successfully deleted ad2'); location.href='./createSiteMap?redirect=ad2'</script>";
        $user->deleteTableRow("ad2_details", array("id"=>$deletead2ID));
        foreach ( $user->fetchAll(array("image"), array("adlink"), array("ad2_details"), array("id"=>$deletead2ID)) as $row ) {
          unlink("../img/ad2/".$row['image']);
        }
        
        
        // $user->deleteTableRow("ad2_details", array("id"=>$deletead2ID));
        // echo "<script>alert('Successfully deleted ad2'); location.href='./createSiteMap?redirect=ad2'</script>";

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
    <?php echo $adminHeader->printAdminNav2(($editMode) ? "Edit ad2" : $adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form accept="" method="post" enctype="multipart/form-data">
                <!-- <div class="row"> -->
                  <?php
                    echo $widgets->inputGroup("ad2 link", "inputad2adlink", "col-md-12", $currentad2adlink);
                    // echo $widgets->inputGroup("ad2 Title", "inputad2Title", "col-md-6", $currentad2Title);
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
                        echo "<input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event)' name='inputad2MainImage' $mainImageRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputad2MainImage' <?php echo $currentad2MainImage; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Main Description</label>
                      <textarea class='form-control' name='inputad2MainDescription' rows="4" required><?php echo $currentad2MainDescription;?></textarea>
                    </div>
                  </div>
                </div> -->
                <!-- Description -->
                <?php
                  // if ( !$editMode ) {
                  //   echo $widgets->addad2DesctiptionDiv(1); 
                  // } else {
                  //   $i=0;
                  //   $sessionad2DescImgArr = array();
                  //   foreach ( $user->fetchAll(array("id","description", "image_01", "image_02"), array("ad2_descriptions"), array("ad2_id"=>$currentad2ID)) as $row ) {
                  //     if ( $i>0 ) echo "<script>$('#addMoread2Description$i').css('display', 'none');</script>";
                  //     $sessionad2DescImgArr[$i] = array(
                  //       $row['id'],
                  //       $row['image_01'],
                  //       ($row['image_01']!= "") ? 1 : 0,
                  //       $row['image_02'],
                  //       ($row['image_02']!= "") ? 1 : 0
                  //     );
                  //     $i++;
                  //     echo $widgets->addad2DesctiptionDiv($i, $row); 
                  //   }
                  //   $_SESSION['sessionad2DescImgArr'] = $sessionad2DescImgArr;
                  //   echo "<script>
                  //   $(document).ready(function () {
                  //     $('input[name=howManyDescriptions]').val('$i');
                  //   })
                  //   </script>";
                  // }
                ?>
                <!-- <div class="row justify-content-center" id="addMoread2DescLoadingImage" style="display: none;">
                  <img src="../img/loading.gif" alt="Loading GIF" style="width: 100px;">
                </div>
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("ad2 Video", "inputad2VideoUrl", "col-md-6", $currentad2VideoUrl);
                    echo "<div class='col-md-6 float-left'>".$widgets->checkboxSwitch("", "ad2VideoStatus", $currentad2VideoStatus, "pt-5")."</div>";
                  ?>
                </div> -->
                <div class="row">
                  <div class="col-12">
                    <input type="hidden" name="howManyDescriptions" value="1">
                    <?php
                      if ( $editMode ) {
                        echo "
                        <input type='submit' class='btn btn-primary' value='Update ad2' name='updatead2Submit'>
                        <input type='button' class='btn btn-danger' value='Delete ad2' onclick='deletead2Submit()'>
                        ";
                      } else {
                        echo "<input type='submit' class='btn btn-success' value='Add ad2' name='addNewad2Submit'>";
                      }
                    ?>
                    <input type="button" class="btn btn-secondary" value="Cancel" onclick="location.href='./add-ad2'">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
    <div id="removead2DescImage"></div>
  </main>
  <?php 
    echo $adminHeader->printAdminFooterJS(); 
    if ( $editMode ) {
      echo "
      <div class='modal fade' id='confirmDeletead2Modal' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='staticBackdropLabel' aria-hidden='true' style='margin-top:200px'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='staticBackdropLabel'>Confirm Delete ad2</h5>
            </div>
            <div class='modal-body'>
            <form action='' class='text-center' method='post'>
                ad2 Title : $currentad2Title<br>
                ad2 Tag : $currentad2Tag<br>
                <input type='hidden' name='deletead2ID' value='$currentad2ID'>
              <br>
              <input type='submit' class='btn btn-danger btn-sm' name='confirmDeletead2Submit' value='Delete'>
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
              changead2DescImage: imageDivIdNumber,
              changead2DescImageNo: imageDivID.substr(15, 3)
          },
          success: function(html) {
              $("#removead2DescImage").html(html).show();
          }
        }); 
      }
		}

    function updateHowManyDescriptions(val) {
      $("input[name='howManyDescriptions']").val(val);
      console.log( $("input[name='howManyDescriptions']").val() );
    }

    function addMoread2Descriptions(index) {
      $("#addMoread2Description"+index).css("display", "none");
      $("#addMoread2DescLoadingImage").css("display", "flex");
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          addMoread2Descriptions: index + 1
        },
        success: function(html) {
          $("#addMoread2Descriptions"+index).html(html).show();
          $("#addMoread2DescLoadingImage").css("display", "none");
          index++;
          $("input[name='howManyDescriptions']").val(index);
        }
      }); 
    }

    function removead2DescImage(index, imgNo) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removead2DescImage: index,
            removead2DescImageNo: imgNo
          },
          success: function(html) {
              $("#removead2DescImage").html(html).show();
          }
      }); 
    }

    function deletead2Submit() {
      $('#confirmDeletead2Modal').modal('show');
    }
  </script>
</body>

</html>