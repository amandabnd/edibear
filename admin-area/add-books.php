<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  require_once("../classes/class.widgets.php");
  $adminHeader = new HEADER("add-books");
  $user = new USER();
  $widgets = new WIDGETS();
  $editMode = false;

  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
        $currentbooksID = (int)$_GET['id'];
        if ( $user->CountRows("books_details", array("id"=>$currentbooksID)) ) {
          $editMode = true;
          $booksDetailsArr = $user->fetchAll(
            array("tag", "title", "image", "description", "video", "video_status", "pdfupload"),
            array("books_details"),
            array("id"=>$currentbooksID)
          )[0];
          $currentbooksTag = $booksDetailsArr['tag'];
          $currentbooksTitle = $booksDetailsArr['title'];
          $currentbooksMainDescription = $booksDetailsArr['description'];
          $currentbooksVideoUrl = $booksDetailsArr['video'];
          $currentbooksVideoStatus = ($booksDetailsArr['video_status']=='1') ? "checked" : "";
          $currentbooksMainImage = "src='".$widgets->createCachelessImage("../img/books/".$booksDetailsArr['image'])."'";
          $currentbookspdfupload = "src='".$widgets->createCachelessImage("../img/books/".$booksDetailsArr['pdfupload'])."'";
        } else {
          $user->redirect("./add-books");
        }
      }
      if ( isset($_POST['addNewbooksSubmit']) || isset($_POST['updatebooksSubmit']) ) {
        $inputbooksTag = htmlspecialchars((isset($_POST['inputbooksTag'])) ? $_POST['inputbooksTag'] : "");
        $inputbooksTitle = htmlspecialchars((isset($_POST['inputbooksTitle'])) ? $_POST['inputbooksTitle'] : "");
        $inputbooksMainDescription = strip_tags((isset($_POST['inputbooksMainDescription'])) ? $_POST['inputbooksMainDescription'] : "", "<br>");
        $inputbooksVideoUrl = htmlspecialchars((isset($_POST['inputbooksVideoUrl'])) ? $_POST['inputbooksVideoUrl'] : "");
        $booksVideoStatus = htmlspecialchars((isset($_POST['booksVideoStatus'])) ? $_POST['booksVideoStatus'] : "0");
        $howManyDescriptions = (int)(isset($_POST['howManyDescriptions'])) ? $_POST['howManyDescriptions'] : 1;
        if ( isset($_POST['addNewbooksSubmit']) ) {
          $booksID = $user->insertTable("books_details", array(
            "tag"=>$inputbooksTag,
            "title"=>$inputbooksTitle,
            "description"=>$inputbooksMainDescription,
            "video"=>$inputbooksVideoUrl,
            "video_status"=>$booksVideoStatus
          ), true);
          
          //main Image
          $inputbooksMainImage =$booksID.".".pathinfo($_FILES["inputbooksMainImage"]["name"], PATHINFO_EXTENSION);
          move_uploaded_file($_FILES["inputbooksMainImage"]["tmp_name"], "../img/books/" . $inputbooksMainImage);
          $user->updateTable("books_details", array("image"=>$inputbooksMainImage), array("id"=>$booksID));
         
          //main pdf
          //$inputbookspdfupload =$pdfID.".".pathinfo($_FILES["inputbookspdfupload"]["name"], PATHINFO_EXTENSION);
          $inputbookspdfupload =$_FILES["inputbookspdfupload"]["name"];
          move_uploaded_file($_FILES["inputbookspdfupload"]["tmp_name"], "../img/books/" . $inputbookspdfupload);
          $user->updateTable("books_details", array("pdfupload"=>$inputbookspdfupload), array("id"=>$booksID));


          //books decriptions
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputbooksDescription = strip_tags((isset($_POST["inputbooksDescription$i"])) ? $_POST["inputbooksDescription$i"] : "", "<br>");
            if ( !empty($inputbooksDescription) || !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
              $booksDescriptionID = $user->insertTable("books_descriptions", array("books_id"=>$booksID,"description"=>$inputbooksDescription), true);
              $inputbooksImageOne = $inputbooksImageTwo = ""; 
              if ( !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputbooksImageOne ="$booksID-$booksDescriptionID-1.".pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputbooksImageOne$i"]["tmp_name"], "../img/books/" . $inputbooksImageOne);
              } 
              if ( !empty(pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputbooksImageTwo ="$booksID-$booksDescriptionID-2.".pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputbooksImageTwo$i"]["tmp_name"], "../img/books/" . $inputbooksImageTwo);
              }
              $user->updateTable("books_descriptions", array("image_01"=>$inputbooksImageOne,"image_02"=>$inputbooksImageTwo), array("id"=>$booksDescriptionID));
            }
          }
          echo "<script>alert('Successfully added a new books');location.href='./createSiteMap?redirect=books'</script>";
        } else if ( isset($_POST['updatebooksSubmit']) ) {
          $user->updateTable("books_details", array(
            "tag"=>$inputbooksTag,
            "title"=>$inputbooksTitle,
            "description"=>$inputbooksMainDescription,
            "video"=>$inputbooksVideoUrl,
            "video_status"=>$booksVideoStatus), array("id"=>$currentbooksID));
          if ( !empty( pathinfo($_FILES["inputbooksMainImage"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/books/".$booksDetailsArr['image']);
            $inputbooksMainImage =$currentbooksID.".".pathinfo($_FILES["inputbooksMainImage"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputbooksMainImage"]["tmp_name"], "../img/books/" . $inputbooksMainImage);
            $user->updateTable("books_details", array("image"=>$inputbooksMainImage), array("id"=>$currentbooksID));
          }

          if ( !empty( pathinfo($_FILES["inputbookspdfupload"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/pdf/".$booksDetailsArr['pdfupload']);
            $inputbookspdfupload =$currentbooksID.".".pathinfo($_FILES["inputbookspdfupload"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputbookspdfupload"]["tmp_name"], "../img/books/" . $inputbookspdfupload);
            $user->updateTable("books_details", array("pdfupload"=>$inputbookspdfupload), array("id"=>$currentbooksID));
          }

          echo "<script>alert('Successfully updated your book'); location.href='./createSiteMap?redirect=books' </script>";

          $sessionbooksDescImgArr = $_SESSION['sessionbooksDescImgArr'];

          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputbooksDescription = strip_tags((isset($_POST["inputbooksDescription$i"])) ? $_POST["inputbooksDescription$i"] : "", "<br>");
            if ( count($sessionbooksDescImgArr) >= $i ) {
              $inputbooksImageOne = $sessionbooksDescImgArr[$i-1][1];
              $inputbooksImageTwo = $sessionbooksDescImgArr[$i-1][3];
              if ( $sessionbooksDescImgArr[$i-1][2]==-1 ) {
                unlink("../img/books/$inputbooksImageOne");
                $inputbooksImageOne = "";
              }
              if ( $sessionbooksDescImgArr[$i-1][4]==-1 ) {
                unlink("../img/books/$inputbooksImageTwo");
                $inputbooksImageTwo = "";
              }
              $booksDescriptionID = $sessionbooksDescImgArr[$i-1][0];
              if ( !empty($inputbooksDescription) || 
                $sessionbooksDescImgArr[$i-1][2]==1 || !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION)) ||
                $sessionbooksDescImgArr[$i-1][4]==1 || !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION))  ) {
                  if ( !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputbooksImageOne ="$currentbooksID-$booksDescriptionID-1.".pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputbooksImageOne$i"]["tmp_name"], "../img/books/" . $inputbooksImageOne);
                  } 
                  if ( !empty(pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputbooksImageTwo ="$currentbooksID-$booksDescriptionID-2.".pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputbooksImageTwo$i"]["tmp_name"], "../img/books/" . $inputbooksImageTwo);
                  }
                  $user->updateTable("books_descriptions", array("description"=>$inputbooksDescription,"image_01"=>$inputbooksImageOne,"image_02"=>$inputbooksImageTwo), array("id"=>$booksDescriptionID));
              } else {
                $user->deleteTableRow("books_descriptions", array("id"=>$booksDescriptionID));
              }
            } else {
              if ( !empty($inputbooksDescription) || !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $booksDescriptionID = $user->insertTable("books_descriptions", array("books_id"=>$currentbooksID,"description"=>$inputbooksDescription), true);
                $inputbooksImageOne = $inputbooksImageTwo = ""; 
                if ( !empty(pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputbooksImageOne ="$currentbooksID-$booksDescriptionID-1.".pathinfo($_FILES["inputbooksImageOne$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputbooksImageOne$i"]["tmp_name"], "../img/books/" . $inputbooksImageOne);
                } 
                if ( !empty(pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputbooksImageTwo ="$currentbooksID-$booksDescriptionID-2.".pathinfo($_FILES["inputbooksImageTwo$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputbooksImageTwo$i"]["tmp_name"], "../img/books/" . $inputbooksImageTwo);
                }
                $user->updateTable("books_descriptions", array("image_01"=>$inputbooksImageOne,"image_02"=>$inputbooksImageTwo), array("id"=>$booksDescriptionID));
              }
            }
          }
          unset($_SESSION['sessionbooksDescImgArr']);
          echo "<script>alert('Successfully updated the books');location.href='./createSiteMap?redirect=books'</script>";
        }
      } else if ( isset($_POST['confirmDeletebooksSubmit']) ) {
        $deletebooksID = (int)$_POST['deletebooksID'];
        foreach ( $user->fetchAll(array("image_01", "image_02"), array("books_descriptions"), array("books_id"=>$deletebooksID)) as $row ) {
          unlink("../img/books/".$row['image_01']);
          unlink("../img/books/".$row['image_02']);
        }
        $user->deleteTableRow("books_descriptions", array("books_id"=>$deletebooksID));
        foreach ( $user->fetchAll(array("image"), array("books_details"), array("id"=>$deletebooksID)) as $row ) {
          unlink("../img/books/".$row['image']);
        }

        $user->deleteTableRow("books_descriptions", array("books_id"=>$deletebooksID));
        foreach ( $user->fetchAll(array("pdfupload"), array("books_details"), array("id"=>$deletebooksID)) as $row ) {
          unlink("../img/books/".$row['pdfupload']);
        }

        $user->deleteTableRow("books_details", array("id"=>$deletebooksID));
        echo "<script>alert('Successfully deleted a books'); location.href='./createSiteMap?redirect=books'</script>";
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
    <?php echo $adminHeader->printAdminNav2(($editMode) ? "Edit books" : $adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form accept="" method="post" enctype="multipart/form-data">
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("books Tag", "inputbooksTag", "col-md-6", $currentbooksTag);
                    echo $widgets->inputGroup("books Title", "inputbooksTitle", "col-md-6", $currentbooksTitle);
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
                        echo "<input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event)' name='inputbooksMainImage' $mainImageRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputbooksMainImage' <?php echo $currentbooksMainImage; ?> style='max-height: 200px; max-width:100%' /></p>
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
                        echo "<input class='form-control' type='file' accept='image/jpeg,image/gif,image/png,application/pdf,image/x-eps' onchange='loadImageFile(event)' name='inputbookspdfupload' $pdfuploadRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputbookspdfupload' <?php echo $currentbookspdfupload; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>




                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Main Description</label>
                      <textarea class='form-control' name='inputbooksMainDescription' rows="4" required><?php echo $currentbooksMainDescription;?></textarea>
                    </div>
                  </div>
                </div>
                <!-- Description -->
                <?php
                  // if ( !$editMode ) {
                  //   echo $widgets->addbooksDesctiptionDiv(1); 
                  // } else {
                  //   $i=0;
                  //   $sessionbooksDescImgArr = array();
                  //   foreach ( $user->fetchAll(array("id","description", "image_01", "image_02"), array("books_descriptions"), array("books_id"=>$currentbooksID)) as $row ) {
                  //     if ( $i>0 ) echo "<script>$('#addMorebooksDescription$i').css('display', 'none');</script>";
                  //     $sessionbooksDescImgArr[$i] = array(
                  //       $row['id'],
                  //       $row['image_01'],
                  //       ($row['image_01']!= "") ? 1 : 0,
                  //       $row['image_02'],
                  //       ($row['image_02']!= "") ? 1 : 0
                  //     );
                  //     $i++;
                  //     echo $widgets->addbooksDesctiptionDiv($i, $row); 
                  //   }
                  //   $_SESSION['sessionbooksDescImgArr'] = $sessionbooksDescImgArr;
                  //   echo "<script>
                  //   $(document).ready(function () {
                  //     $('input[name=howManyDescriptions]').val('$i');
                  //   })
                  //   </script>";
                  // }
                ?>
                <!-- <div class="row justify-content-center" id="addMorebooksDescLoadingImage" style="display: none;">
                  <img src="../img/loading.gif" alt="Loading GIF" style="width: 100px;">
                </div>
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("books Video", "inputbooksVideoUrl", "col-md-6", $currentbooksVideoUrl);
                    echo "<div class='col-md-6 float-left'>".$widgets->checkboxSwitch("", "booksVideoStatus", $currentbooksVideoStatus, "pt-5")."</div>";
                  ?>
                </div> -->
                <div class="row">
                  <div class="col-12">
                    <input type="hidden" name="howManyDescriptions" value="1">
                    <?php
                      if ( $editMode ) {
                        echo "
                        <input type='submit' class='btn btn-primary' value='Update books' name='updatebooksSubmit'>
                        <input type='button' class='btn btn-danger' value='Delete books' onclick='deletebooksSubmit()'>
                        ";
                      } else {
                        echo "<input type='submit' class='btn btn-success' value='Add books' name='addNewbooksSubmit'>";
                      }
                    ?>
                    <input type="button" class="btn btn-secondary" value="Cancel" onclick="location.href='./add-books'">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
    <div id="removebooksDescImage"></div>
  </main>
  <?php 
    echo $adminHeader->printAdminFooterJS(); 
    if ( $editMode ) {
      echo "
      <div class='modal fade' id='confirmDeletebooksModal' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='staticBackdropLabel' aria-hidden='true' style='margin-top:200px'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='staticBackdropLabel'>Confirm Delete a books</h5>
            </div>
            <div class='modal-body'>
            <form action='' class='text-center' method='post'>
                books Title : $currentbooksTitle<br>
                books Tag : $currentbooksTag<br>
                <input type='hidden' name='deletebooksID' value='$currentbooksID'>
              <br>
              <input type='submit' class='btn btn-danger btn-sm' name='confirmDeletebooksSubmit' value='Delete'>
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
              changebooksDescImage: imageDivIdNumber,
              changebooksDescImageNo: imageDivID.substr(15, 3)
          },
          success: function(html) {
              $("#removebooksDescImage").html(html).show();
          }
        }); 
      }
		}

    function updateHowManyDescriptions(val) {
      $("input[name='howManyDescriptions']").val(val);
      console.log( $("input[name='howManyDescriptions']").val() );
    }

    function addMorebooksDescriptions(index) {
      $("#addMorebooksDescription"+index).css("display", "none");
      $("#addMorebooksDescLoadingImage").css("display", "flex");
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          addMorebooksDescriptions: index + 1
        },
        success: function(html) {
          $("#addMorebooksDescriptions"+index).html(html).show();
          $("#addMorebooksDescLoadingImage").css("display", "none");
          index++;
          $("input[name='howManyDescriptions']").val(index);
        }
      }); 
    }

    function removebooksDescImage(index, imgNo) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removebooksDescImage: index,
            removebooksDescImageNo: imgNo
          },
          success: function(html) {
              $("#removebooksDescImage").html(html).show();
          }
      }); 
    }

    function deletebooksSubmit() {
      $('#confirmDeletebooksModal').modal('show');
    }
  </script>
</body>

</html>