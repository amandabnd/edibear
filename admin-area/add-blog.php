<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  require_once("../classes/class.widgets.php");
  $adminHeader = new HEADER("add-blog");
  $user = new USER();
  $widgets = new WIDGETS();
  $editMode = false;
  $currentBlogTag = "";
  $currentBlogTitle = "";
  $currentBlogMainDescription = "";
  $currentBlogVideoUrl = "";
  $currentBlogVideoStatus = "";
  $currentBlogMainImage = "";
  $currentBlogpdfupload = "";
  $currentBlogID = 0;


  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
        $currentBlogID = (int)$_GET['id'];
        if ( $user->CountRows("blog_details", array("id"=>$currentBlogID)) ) {
          $editMode = true;
          $blogDetailsArr = $user->fetchAll(
            array("tag", "title", "image", "description", "video", "video_status"),
            array("blog_details"),
            array("id"=>$currentBlogID)
          )[0];
          $currentBlogTag = $blogDetailsArr['tag'];
          $currentBlogTitle = $blogDetailsArr['title'];
          $currentBlogMainDescription = $blogDetailsArr['description'];
          $currentBlogVideoUrl = $blogDetailsArr['video'];
          $currentBlogVideoStatus = ($blogDetailsArr['video_status']=='1') ? "checked" : "";
          $currentBlogMainImage = "src='".$widgets->createCachelessImage("../img/blogs/".$blogDetailsArr['image'])."'";
        } else {
          $user->redirect("./add-blog");
        }
      }
      if ( isset($_POST['addNewBlogSubmit']) || isset($_POST['updateBlogSubmit']) ) {
        $inputBlogTag = htmlspecialchars((isset($_POST['inputBlogTag'])) ? $_POST['inputBlogTag'] : "");
        $inputBlogTitle = htmlspecialchars((isset($_POST['inputBlogTitle'])) ? $_POST['inputBlogTitle'] : "");
        $inputBlogMainDescription = strip_tags((isset($_POST['inputBlogMainDescription'])) ? $_POST['inputBlogMainDescription'] : "", "<br>");
        $inputBlogVideoUrl = htmlspecialchars((isset($_POST['inputBlogVideoUrl'])) ? $_POST['inputBlogVideoUrl'] : "");
        $blogVideoStatus = htmlspecialchars((isset($_POST['blogVideoStatus'])) ? $_POST['blogVideoStatus'] : "0");
        $howManyDescriptions = (int)(isset($_POST['howManyDescriptions'])) ? $_POST['howManyDescriptions'] : 1;
        if ( isset($_POST['addNewBlogSubmit']) ) {
          $blogID = $user->insertTable("blog_details", array(
            "tag"=>$inputBlogTag,
            "title"=>$inputBlogTitle,
            "description"=>$inputBlogMainDescription,
            "video"=>$inputBlogVideoUrl,
            "video_status"=>$blogVideoStatus
          ), true);
          //main Image
          $inputBlogMainImage =$blogID.".".pathinfo($_FILES["inputBlogMainImage"]["name"], PATHINFO_EXTENSION);
          move_uploaded_file($_FILES["inputBlogMainImage"]["tmp_name"], "../img/blogs/" . $inputBlogMainImage);
          $user->updateTable("blog_details", array("image"=>$inputBlogMainImage), array("id"=>$blogID));
          //blog decriptions
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputBlogDescription = strip_tags((isset($_POST["inputBlogDescription$i"])) ? $_POST["inputBlogDescription$i"] : "", "<br>");
            if ( !empty($inputBlogDescription) || !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
              $blogDescriptionID = $user->insertTable("blog_descriptions", array("blog_id"=>$blogID,"description"=>$inputBlogDescription), true);
              $inputBlogImageOne = $inputBlogImageTwo = ""; 
              if ( !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputBlogImageOne ="$blogID-$blogDescriptionID-1.".pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputBlogImageOne$i"]["tmp_name"], "../img/blogs/" . $inputBlogImageOne);
              } 
              if ( !empty(pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $inputBlogImageTwo ="$blogID-$blogDescriptionID-2.".pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES["inputBlogImageTwo$i"]["tmp_name"], "../img/blogs/" . $inputBlogImageTwo);
              }
              $user->updateTable("blog_descriptions", array("image_01"=>$inputBlogImageOne,"image_02"=>$inputBlogImageTwo), array("id"=>$blogDescriptionID));
            }
          }
          echo "<script>alert('Successfully added a new Blog');location.href='./createSiteMap?redirect=blogs'</script>";
        } else if ( isset($_POST['updateBlogSubmit']) ) {
          $user->updateTable("blog_details", array(
            "tag"=>$inputBlogTag,
            "title"=>$inputBlogTitle,
            "description"=>$inputBlogMainDescription,
            "video"=>$inputBlogVideoUrl,
            "video_status"=>$blogVideoStatus), array("id"=>$currentBlogID));
          if ( !empty( pathinfo($_FILES["inputBlogMainImage"]["name"], PATHINFO_EXTENSION) ) ) {
            unlink("../img/blogs/".$blogDetailsArr['image']);
            $inputBlogMainImage =$currentBlogID.".".pathinfo($_FILES["inputBlogMainImage"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputBlogMainImage"]["tmp_name"], "../img/blogs/" . $inputBlogMainImage);
            $user->updateTable("blog_details", array("image"=>$inputBlogMainImage), array("id"=>$currentBlogID));
          }
          $sessionBlogDescImgArr = $_SESSION['sessionBlogDescImgArr'];
          for ( $i=1; $i<=$howManyDescriptions; $i++ ) {
            $inputBlogDescription = strip_tags((isset($_POST["inputBlogDescription$i"])) ? $_POST["inputBlogDescription$i"] : "", "<br>");
            if ( count($sessionBlogDescImgArr) >= $i ) {
              $inputBlogImageOne = $sessionBlogDescImgArr[$i-1][1];
              $inputBlogImageTwo = $sessionBlogDescImgArr[$i-1][3];
              if ( $sessionBlogDescImgArr[$i-1][2]==-1 ) {
                unlink("../img/blogs/$inputBlogImageOne");
                $inputBlogImageOne = "";
              }
              if ( $sessionBlogDescImgArr[$i-1][4]==-1 ) {
                unlink("../img/blogs/$inputBlogImageTwo");
                $inputBlogImageTwo = "";
              }
              $blogDescriptionID = $sessionBlogDescImgArr[$i-1][0];
              if ( !empty($inputBlogDescription) || 
                $sessionBlogDescImgArr[$i-1][2]==1 || !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION)) ||
                $sessionBlogDescImgArr[$i-1][4]==1 || !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION))  ) {
                  if ( !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputBlogImageOne ="$currentBlogID-$blogDescriptionID-1.".pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputBlogImageOne$i"]["tmp_name"], "../img/blogs/" . $inputBlogImageOne);
                  } 
                  if ( !empty(pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                    $inputBlogImageTwo ="$currentBlogID-$blogDescriptionID-2.".pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputBlogImageTwo$i"]["tmp_name"], "../img/blogs/" . $inputBlogImageTwo);
                  }
                  $user->updateTable("blog_descriptions", array("description"=>$inputBlogDescription,"image_01"=>$inputBlogImageOne,"image_02"=>$inputBlogImageTwo), array("id"=>$blogDescriptionID));
              } else {
                $user->deleteTableRow("blog_descriptions", array("id"=>$blogDescriptionID));
              }
            } else {
              if ( !empty($inputBlogDescription) || !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION) ) || !empty(pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                $blogDescriptionID = $user->insertTable("blog_descriptions", array("blog_id"=>$currentBlogID,"description"=>$inputBlogDescription), true);
                $inputBlogImageOne = $inputBlogImageTwo = ""; 
                if ( !empty(pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputBlogImageOne ="$currentBlogID-$blogDescriptionID-1.".pathinfo($_FILES["inputBlogImageOne$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputBlogImageOne$i"]["tmp_name"], "../img/blogs/" . $inputBlogImageOne);
                } 
                if ( !empty(pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION) ) ) {
                  $inputBlogImageTwo ="$currentBlogID-$blogDescriptionID-2.".pathinfo($_FILES["inputBlogImageTwo$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputBlogImageTwo$i"]["tmp_name"], "../img/blogs/" . $inputBlogImageTwo);
                }
                $user->updateTable("blog_descriptions", array("image_01"=>$inputBlogImageOne,"image_02"=>$inputBlogImageTwo), array("id"=>$blogDescriptionID));
              }
            }
          }
          unset($_SESSION['sessionBlogDescImgArr']);
          echo "<script>alert('Successfully updated the Blog');location.href='./createSiteMap?redirect=blogs'</script>";
        }
      } else if ( isset($_POST['confirmDeleteBlogSubmit']) ) {
        $deleteBlogID = (int)$_POST['deleteBlogID'];
        foreach ( $user->fetchAll(array("image_01", "image_02"), array("blog_descriptions"), array("blog_id"=>$deleteBlogID)) as $row ) {
          unlink("../img/blogs/".$row['image_01']);
          unlink("../img/blogs/".$row['image_02']);
        }
        $user->deleteTableRow("blog_descriptions", array("blog_id"=>$deleteBlogID));
        foreach ( $user->fetchAll(array("image"), array("blog_details"), array("id"=>$deleteBlogID)) as $row ) {
          unlink("../img/blogs/".$row['image']);
        }
        $user->deleteTableRow("blog_details", array("id"=>$deleteBlogID));
        echo "<script>alert('Successfully deleted a Blog'); location.href='./createSiteMap?redirect=blogs'</script>";
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
    <?php echo $adminHeader->printAdminNav2(($editMode) ? "Edit Blog" : $adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form accept="" method="post" enctype="multipart/form-data">
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("Blog Tag", "inputBlogTag", "col-md-6", $currentBlogTag);
                    echo $widgets->inputGroup("Blog Title", "inputBlogTitle", "col-md-6", $currentBlogTitle);
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
                        echo "<input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event)' name='inputBlogMainImage' $mainImageRequired>";
                      ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputBlogMainImage' <?php echo $currentBlogMainImage; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Main Description</label>
                      <textarea class='form-control' name='inputBlogMainDescription' rows="4" required><?php echo $currentBlogMainDescription;?></textarea>
                    </div>
                  </div>
                </div>
                Description
                <?php
                  if ( !$editMode ) {
                    echo $widgets->addBlogDesctiptionDiv(1); 
                  } else {
                    $i=0;
                    $sessionBlogDescImgArr = array();
                    foreach ( $user->fetchAll(array("id","description", "image_01", "image_02"), array("blog_descriptions"), array("blog_id"=>$currentBlogID)) as $row ) {
                      if ( $i>0 ) echo "<script>$('#addMoreBlogDescription$i').css('display', 'none');</script>";
                      $sessionBlogDescImgArr[$i] = array(
                        $row['id'],
                        $row['image_01'],
                        ($row['image_01']!= "") ? 1 : 0,
                        $row['image_02'],
                        ($row['image_02']!= "") ? 1 : 0
                      );
                      $i++;
                      echo $widgets->addBlogDesctiptionDiv($i, $row); 
                    }
                    $_SESSION['sessionBlogDescImgArr'] = $sessionBlogDescImgArr;
                    echo "<script>
                    $(document).ready(function () {
                      $('input[name=howManyDescriptions]').val('$i');
                    })
                    </script>";
                  }
                ?>
                <div class="row justify-content-center" id="addMoreBlogDescLoadingImage" style="display: none;">
                  <img src="../img/loading.gif" alt="Loading GIF" style="width: 100px;">
                </div>
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("Blog Video", "inputBlogVideoUrl", "col-md-6", $currentBlogVideoUrl);
                    echo "<div class='col-md-6 float-left'>".$widgets->checkboxSwitch("", "blogVideoStatus", $currentBlogVideoStatus, "pt-5")."</div>";
                  ?>
                </div>
                <div class="row">
                  <div class="col-12">
                    <input type="hidden" name="howManyDescriptions" value="1">
                    <?php
                      if ( $editMode ) {
                        echo "
                        <input type='submit' class='btn btn-primary' value='Update Blog' name='updateBlogSubmit'>
                        <input type='button' class='btn btn-danger' value='Delete Blog' onclick='deleteBlogSubmit()'>
                        ";
                      } else {
                        echo "<input type='submit' class='btn btn-success' value='Add Blog' name='addNewBlogSubmit'>";
                      }
                    ?>
                    <input type="button" class="btn btn-secondary" value="Cancel" onclick="location.href='./add-blog'">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
    <div id="removeBlogDescImage"></div>
  </main>
  <?php 
    echo $adminHeader->printAdminFooterJS(); 
    if ( $editMode ) {
      echo "
      <div class='modal fade' id='confirmDeleteBlogModal' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='staticBackdropLabel' aria-hidden='true' style='margin-top:200px'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='staticBackdropLabel'>Confirm Delete a Blog</h5>
            </div>
            <div class='modal-body'>
            <form action='' class='text-center' method='post'>
                Blog Title : $currentBlogTitle<br>
                Blog Tag : $currentBlogTag<br>
                <input type='hidden' name='deleteBlogID' value='$currentBlogID'>
              <br>
              <input type='submit' class='btn btn-danger btn-sm' name='confirmDeleteBlogSubmit' value='Delete'>
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
              changeBlogDescImage: imageDivIdNumber,
              changeBlogDescImageNo: imageDivID.substr(15, 3)
          },
          success: function(html) {
              $("#removeBlogDescImage").html(html).show();
          }
        }); 
      }
		}

    function updateHowManyDescriptions(val) {
      $("input[name='howManyDescriptions']").val(val);
      console.log( $("input[name='howManyDescriptions']").val() );
    }

    function addMoreBlogDescriptions(index) {
      $("#addMoreBlogDescription"+index).css("display", "none");
      $("#addMoreBlogDescLoadingImage").css("display", "flex");
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          addMoreBlogDescriptions: index + 1
        },
        success: function(html) {
          $("#addMoreBlogDescriptions"+index).html(html).show();
          $("#addMoreBlogDescLoadingImage").css("display", "none");
          index++;
          $("input[name='howManyDescriptions']").val(index);
        }
      }); 
    }

    function removeBlogDescImage(index, imgNo) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removeBlogDescImage: index,
            removeBlogDescImageNo: imgNo
          },
          success: function(html) {
              $("#removeBlogDescImage").html(html).show();
          }
      }); 
    }

    function deleteBlogSubmit() {
      $('#confirmDeleteBlogModal').modal('show');
    }
  </script>
</body>

</html>