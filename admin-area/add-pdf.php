<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  require_once("../classes/class.widgets.php");
  $adminHeader = new HEADER("add-pdf");
  $user = new USER();
  $widgets = new WIDGETS();
  $editMode = false;
  $currentpdfTag = "";
$currentpdfTitle = "";
$currentpdfMainDescription = "";
$currentpdfVideoUrl = "";
$currentpdfVideoStatus = "";
$currentpdfMainImage = "";
$currentpdfpdfupload = "";
$currentpdfID = 0;

  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
        $currentpdfID = (int)$_GET['id'];
        if ( $user->CountRows("pdf_details", array("id"=>$currentpdfID)) ) {
          $editMode = true;
          $pdfDetailsArr = $user->fetchAll(
            array("tag", "title", "image", "description", "video", "video_status", "pdfupload"),
            array("pdf_details"),
            array("id"=>$currentpdfID)
          )[0];
          $currentpdfTag = $pdfDetailsArr['tag'];
          $currentpdfTitle = $pdfDetailsArr['title'];
          $currentpdfMainDescription = $pdfDetailsArr['description'];
          $currentpdfVideoUrl = $pdfDetailsArr['video'];
          $currentpdfVideoStatus = ($pdfDetailsArr['video_status']=='1') ? "checked" : "";
          $currentpdfMainImage = "src='".$widgets->createCachelessImage("../img/pdf/".$pdfDetailsArr['image'])."'";
          $currentpdfpdfupload = "src='".$widgets->createCachelessImage("../img/pdf/".$pdfDetailsArr['pdfupload'])."'";
        } else {
          $user->redirect("./add-pdf");

        }
      }
      if ( isset($_POST['addNewpdfSubmit']) || isset($_POST['updatepdfSubmit']) ) {
        $inputpdfTag = htmlspecialchars((isset($_POST['inputpdfTag'])) ? $_POST['inputpdfTag'] : "");
        $inputpdfTitle = htmlspecialchars((isset($_POST['inputpdfTitle'])) ? $_POST['inputpdfTitle'] : "");
        $inputpdfMainDescription = strip_tags((isset($_POST['inputpdfMainDescription'])) ? $_POST['inputpdfMainDescription'] : "", "<br>");
        $inputpdfVideoUrl = htmlspecialchars((isset($_POST['inputpdfVideoUrl'])) ? $_POST['inputpdfVideoUrl'] : "");
        $pdfVideoStatus = htmlspecialchars((isset($_POST['pdfVideoStatus'])) ? $_POST['pdfVideoStatus'] : "0");
        $howManyDescriptions = (int)(isset($_POST['howManyDescriptions'])) ? $_POST['howManyDescriptions'] : 1;
        if ( isset($_POST['addNewpdfSubmit']) ) {
          $pdfID = $user->insertTable("pdf_details", array(
            "tag"=>$inputpdfTag,
            "title"=>$inputpdfTitle,
            "description"=>$inputpdfMainDescription,
            "video"=>$inputpdfVideoUrl,
            "video_status"=>$pdfVideoStatus
          ), true);
          
          //main Image
          $inputpdfMainImage =$pdfID.".".pathinfo($_FILES["inputpdfMainImage"]["name"], PATHINFO_EXTENSION);
          move_uploaded_file($_FILES["inputpdfMainImage"]["tmp_name"], "../img/pdf/" . $inputpdfMainImage);
          $user->updateTable("pdf_details", array("image"=>$inputpdfMainImage), array("id"=>$pdfID));

          //main pdf
         // $inputpdfpdfupload =$pdfID.".".pathinfo($_FILES["inputpdfpdfupload"]["name"], PATHINFO_EXTENSION);
       $inputpdfpdfupload =$_FILES["inputpdfpdfupload"]["name"];
          move_uploaded_file($_FILES["inputpdfpdfupload"]["tmp_name"], "../img/pdf/" . $inputpdfpdfupload);
          $user->updateTable("pdf_details", array("pdfupload"=>$inputpdfpdfupload), array("id"=>$pdfID));


          //pdf decriptions
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputpdfDescription = strip_tags((isset($_POST["inputpdfDescription$i"])) ? $_POST["inputpdfDescription$i"] : "", "<br>");
            if ( !empty($inputpdfDescription) || !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
              $pdfDescriptionID = $user->insertTable("pdf_descriptions", array("pdf_id"=>$pdfID,"description"=>$inputpdfDescription), true);
              $inputpdfImageOne = $inputpdfImageTwo = ""; 
              if ( !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputpdfImageOne ="$pdfID-$pdfDescriptionID-1.".pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputpdfImageOne$i"]["tmp_name"], "../img/pdf/" . $inputpdfImageOne);
              } 
              if ( !empty(pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputpdfImageTwo ="$pdfID-$pdfDescriptionID-2.".pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputpdfImageTwo$i"]["tmp_name"], "../img/pdf/" . $inputpdfImageTwo);
              }
              $user->updateTable("pdf_descriptions", array("image_01"=>$inputpdfImageOne,"image_02"=>$inputpdfImageTwo), array("id"=>$pdfDescriptionID));
            }
          }
          echo "<script>alert('Successfully added a new pdf');location.href='./createSiteMap?redirect=pdf'</script>";
        } else if ( isset($_POST['updatepdfSubmit']) ) {
          $user->updateTable("pdf_details", array(
            "tag"=>$inputpdfTag,
            "title"=>$inputpdfTitle,
            "description"=>$inputpdfMainDescription,
            "video"=>$inputpdfVideoUrl,
            "video_status"=>$pdfVideoStatus), array("id"=>$currentpdfID));
          if ( !empty( pathinfo($_FILES["inputpdfMainImage"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/pdf/".$pdfDetailsArr['image']);
            $inputpdfMainImage =$currentpdfID.".".pathinfo($_FILES["inputpdfMainImage"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputpdfMainImage"]["tmp_name"], "../img/pdf/" . $inputpdfMainImage);
            $user->updateTable("pdf_details", array("image"=>$inputpdfMainImage), array("id"=>$currentpdfID));
          }

          if ( !empty( pathinfo($_FILES["inputpdfpdfupload"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/pdf/".$pdfDetailsArr['pdfupload']);
            $inputpdfpdfupload =$currentpdfID.".".pathinfo($_FILES["inputpdfpdfupload"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputpdfpdfupload"]["tmp_name"], "../img/pdf/" . $inputpdfpdfupload);
            $user->updateTable("pdf_details", array("pdfupload"=>$inputpdfpdfupload), array("id"=>$currentpdfID));
          }

          $sessionpdfDescImgArr = $_SESSION['sessionpdfDescImgArr'];

          echo "<script>alert('Successfully updated your record'); location.href='./createSiteMap?redirect=pdf' </script>";

          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputpdfDescription = strip_tags((isset($_POST["inputpdfDescription$i"])) ? $_POST["inputpdfDescription$i"] : "", "<br>");
            if ( count($sessionpdfDescImgArr) >= $i ) {
              $inputpdfImageOne = $sessionpdfDescImgArr[$i-1][1];
              $inputpdfImageTwo = $sessionpdfDescImgArr[$i-1][3];
              if ( $sessionpdfDescImgArr[$i-1][2]==-1 ) {
                unlink("../img/pdf/$inputpdfImageOne");
                $inputpdfImageOne = "";
              }
              if ( $sessionpdfDescImgArr[$i-1][4]==-1 ) {
                unlink("../img/pdf/$inputpdfImageTwo");
                $inputpdfImageTwo = "";
              }
              $pdfDescriptionID = $sessionpdfDescImgArr[$i-1][0];
              if ( !empty($inputpdfDescription) || 
                $sessionpdfDescImgArr[$i-1][2]==1 || !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION)) ||
                $sessionpdfDescImgArr[$i-1][4]==1 || !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION))  ) {
                  if ( !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputpdfImageOne ="$currentpdfID-$pdfDescriptionID-1.".pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputpdfImageOne$i"]["tmp_name"], "../img/pdf/" . $inputpdfImageOne);
                  } 
                  if ( !empty(pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputpdfImageTwo ="$currentpdfID-$pdfDescriptionID-2.".pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputpdfImageTwo$i"]["tmp_name"], "../img/pdf/" . $inputpdfImageTwo);
                  }
                  $user->updateTable("pdf_descriptions", array("description"=>$inputpdfDescription,"image_01"=>$inputpdfImageOne,"image_02"=>$inputpdfImageTwo), array("id"=>$pdfDescriptionID));
              } else {
                $user->deleteTableRow("pdf_descriptions", array("id"=>$pdfDescriptionID));
              }
            } else {
              if ( !empty($inputpdfDescription) || !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $pdfDescriptionID = $user->insertTable("pdf_descriptions", array("pdf_id"=>$currentpdfID,"description"=>$inputpdfDescription), true);
                $inputpdfImageOne = $inputpdfImageTwo = ""; 
                if ( !empty(pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputpdfImageOne ="$currentpdfID-$pdfDescriptionID-1.".pathinfo($_FILES["inputpdfImageOne$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputpdfImageOne$i"]["tmp_name"], "../img/pdf/" . $inputpdfImageOne);
                } 
                if ( !empty(pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputpdfImageTwo ="$currentpdfID-$pdfDescriptionID-2.".pathinfo($_FILES["inputpdfImageTwo$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputpdfImageTwo$i"]["tmp_name"], "../img/pdf/" . $inputpdfImageTwo);
                }
                $user->updateTable("pdf_descriptions", array("image_01"=>$inputpdfImageOne,"image_02"=>$inputpdfImageTwo), array("id"=>$pdfDescriptionID));
              }
            }
          }
          unset($_SESSION['sessionpdfDescImgArr']);
          echo "<script>alert('Successfully updated the pdf');location.href='./createSiteMap?redirect=pdf'</script>";
        }
      } else if ( isset($_POST['confirmDeletepdfSubmit']) ) {
        $deletepdfID = (int)$_POST['deletepdfID'];
        foreach ( $user->fetchAll(array("image_01", "image_02"), array("pdf_descriptions"), array("pdf_id"=>$deletepdfID)) as $row ) {
          unlink("../img/pdf/".$row['image_01']);
          unlink("../img/pdf/".$row['image_02']);
        }
        $user->deleteTableRow("pdf_descriptions", array("pdf_id"=>$deletepdfID));
        foreach ( $user->fetchAll(array("image"), array("pdf_details"), array("id"=>$deletepdfID)) as $row ) {
          unlink("../img/pdf/".$row['image']);
        }

        $user->deleteTableRow("pdf_descriptions", array("pdf_id"=>$deletepdfID));
        foreach ( $user->fetchAll(array("pdfupload"), array("pdf_details"), array("id"=>$deletepdfID)) as $row ) {
          unlink("../img/pdf/".$row['pdfupload']);
        }

        $user->deleteTableRow("pdf_details", array("id"=>$deletepdfID));
        echo "<script>alert('Successfully deleted a pdf'); location.href='./createSiteMap?redirect=pdf'</script>";

        
        
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
    <?php echo $adminHeader->printAdminNav2(($editMode) ? "Edit pdf" : $adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form accept="" method="post" enctype="multipart/form-data">
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("pdf Tag", "inputpdfTag", "col-md-6", $currentpdfTag);
                    echo $widgets->inputGroup("pdf Title", "inputpdfTitle", "col-md-6", $currentpdfTitle);
                  ?>
                </div>
                <div class="row border mx-3 mb-2">
                  <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Main Image</label>
                      <?php
                        if ( $editMode ) {
                          $mainImageRequired = "";
                        } else {
                          $mainImageRequired = "required";
                        }
                        echo "<input class='form-control' type='file' accept='image/jpeg,image/gif,image/png,application/pdf,image/x-eps' onchange='loadImageFile(event)' name='inputpdfMainImage' $mainImageRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputpdfMainImage' <?php echo $currentpdfMainImage; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>

                  <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Main PDF</label>
                      <?php
                        if ( $editMode ) {
                          $pdfuploadRequired = "";
                        } else {
                          $pdfuploadRequired = "required";
                        }
                        echo "<input class='form-control' type='file' accept='image/jpeg,image/gif,image/png,application/pdf,image/x-eps' onchange='loadImageFile(event)' name='inputpdfpdfupload' $pdfuploadRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputpdfpdfupload' <?php echo $currentpdfpdfupload; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>

                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Main Description</label>
                      <textarea class='form-control' name='inputpdfMainDescription' rows="4" required><?php echo $currentpdfMainDescription;?></textarea>
                    </div>
                  </div>
                </div>
                <!-- Description -->
                <?php
                  // if ( !$editMode ) {
                  //   echo $widgets->addpdfDesctiptionDiv(1); 
                  // } else {
                  //   $i=0;
                  //   $sessionpdfDescImgArr = array();
                  //   foreach ( $user->fetchAll(array("id","description", "image_01", "image_02"), array("pdf_descriptions"), array("pdf_id"=>$currentpdfID)) as $row ) {
                  //     if ( $i>0 ) echo "<script>$('#addMorepdfDescription$i').css('display', 'none');</script>";
                  //     $sessionpdfDescImgArr[$i] = array(
                  //       $row['id'],
                  //       $row['image_01'],
                  //       ($row['image_01']!= "") ? 1 : 0,
                  //       $row['image_02'],
                  //       ($row['image_02']!= "") ? 1 : 0
                  //     );
                  //     $i++;
                  //     echo $widgets->addpdfDesctiptionDiv($i, $row); 
                  //   }
                  //   $_SESSION['sessionpdfDescImgArr'] = $sessionpdfDescImgArr;
                  //   echo "<script>
                  //   $(document).ready(function () {
                  //     $('input[name=howManyDescriptions]').val('$i');
                  //   })
                  //   </script>";
                  // }
                ?>
                <!-- <div class="row justify-content-center" id="addMorepdfDescLoadingImage" style="display: none;">
                  <img src="../img/loading.gif" alt="Loading GIF" style="width: 100px;">
                </div>
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("pdf Video", "inputpdfVideoUrl", "col-md-6", $currentpdfVideoUrl);
                    echo "<div class='col-md-6 float-left'>".$widgets->checkboxSwitch("", "pdfVideoStatus", $currentpdfVideoStatus, "pt-5")."</div>";
                  ?>
                </div> -->
                <div class="row">
                  <div class="col-12">
                    <input type="hidden" name="howManyDescriptions" value="1">
                    <?php
                      if ( $editMode ) {
                        echo "
                        <input type='submit' class='btn btn-primary' value='Update pdf' name='updatepdfSubmit'>
                        <input type='button' class='btn btn-danger' value='Delete pdf' onclick='deletepdfSubmit()'>
                        ";
                      } else {
                        echo "<input type='submit' class='btn btn-success' value='Add pdf' name='addNewpdfSubmit'>";
                      }
                    ?>
                    <input type="button" class="btn btn-secondary" value="Cancel" onclick="location.href='./add-pdf'">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
    <div id="removepdfDescImage"></div>
  </main>
  <?php 
    echo $adminHeader->printAdminFooterJS(); 
    if ( $editMode ) {
      echo "
      <div class='modal fade' id='confirmDeletepdfModal' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='staticBackdropLabel' aria-hidden='true' style='margin-top:200px'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='staticBackdropLabel'>Confirm Delete a pdf</h5>
            </div>
            <div class='modal-body'>
            <form action='' class='text-center' method='post'>
                pdf Title : $currentpdfTitle<br>
                pdf Tag : $currentpdfTag<br>
                <input type='hidden' name='deletepdfID' value='$currentpdfID'>
              <br>
              <input type='submit' class='btn btn-danger btn-sm' name='confirmDeletepdfSubmit' value='Delete'>
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
              changepdfDescImage: imageDivIdNumber,
              changepdfDescImageNo: imageDivID.substr(15, 3)
          },
          success: function(html) {
              $("#removepdfDescImage").html(html).show();
          }
        }); 
      }
		}

    function updateHowManyDescriptions(val) {
      $("input[name='howManyDescriptions']").val(val);
      console.log( $("input[name='howManyDescriptions']").val() );
    }

    function addMorepdfDescriptions(index) {
      $("#addMorepdfDescription"+index).css("display", "none");
      $("#addMorepdfDescLoadingImage").css("display", "flex");
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          addMorepdfDescriptions: index + 1
        },
        success: function(html) {
          $("#addMorepdfDescriptions"+index).html(html).show();
          $("#addMorepdfDescLoadingImage").css("display", "none");
          index++;
          $("input[name='howManyDescriptions']").val(index);
        }
      }); 
    }

    function removepdfDescImage(index, imgNo) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removepdfDescImage: index,
            removepdfDescImageNo: imgNo
          },
          success: function(html) {
              $("#removepdfDescImage").html(html).show();
          }
      }); 
    }

    function deletepdfSubmit() {
      $('#confirmDeletepdfModal').modal('show');
    }
  </script>
</body>

</html>