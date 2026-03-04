<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  require_once("../classes/class.widgets.php");
  $adminHeader = new HEADER("add-tours");
  $user = new USER();
  $widgets = new WIDGETS();
  $editMode = false;
  $currentTourNo = "";
  $currentTourTitle = "";
  $currentTourType = "";
  $currentTourMainImage = "";
  $currentTourDuration ="";
  $currentTourGroup ="";
  $currentTourVehicleType ="";
  $currentTourGuide ="";
  $currentTourPickupDrop ="";
  $currentTourHotelType ="";
  $currentTourArrivalDepartureLocation ="";
  $currentTourDepatureTim ="";
  $currentTourMealPlan ="";
  $currentTourBedRoom ="";
  $currentTourMap ="";


  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
        $currentTourID = (int)$_GET['id'];
        if ( $user->CountRows("tour_details", array("id"=>$currentTourID)) ) {
          $editMode = true;
          $tourDetailsArr = $user->fetchAll(
            array("no", "title", "type", "duration", "tour_group", "vehicle_type", "guide", "pickup_drop", "hotel_type", "description", "arrival_departure_location", "depature_time", "meal_plan", "bed_room", "services_included", "services_excluded", "map", "image_name"), 
            array("tour_details"), 
            array("id"=>$currentTourID)
          )[0];
          $currentTourNo = $tourDetailsArr['no'];
          $currentTourTitle = $tourDetailsArr['title'];
          $currentTourType = $tourDetailsArr['type'];
          $currentTourDuration = $tourDetailsArr['duration'];
          $currentTourGroup = $tourDetailsArr['tour_group'];
          $currentTourVehicleType = $tourDetailsArr['vehicle_type'];
          $currentTourGuide = $tourDetailsArr['guide'];
          $currentTourPickupDrop = $tourDetailsArr['pickup_drop'];
          $currentTourHotelType = $tourDetailsArr['hotel_type'];
          $currentTourDescription = $tourDetailsArr['description'];
          $currentTourArrivalDepartureLocation = $tourDetailsArr['arrival_departure_location'];
          $currentTourDepatureTim = $tourDetailsArr['depature_time'];
          $currentTourMealPlan = $tourDetailsArr['meal_plan'];
          $currentTourBedRoom = $tourDetailsArr['bed_room'];
          $currentTourServicesIncluded = $tourDetailsArr['services_included'];
          $currentTourServicesExcluded = $tourDetailsArr['services_excluded'];
          $currentTourMap = $tourDetailsArr['map'];
          $currentTourMainImage = "src='".$widgets->createCachelessImage("../img/tours/".$tourDetailsArr['image_name'])."'";
          if ( !isset($_SESSION['sessionTourSubImgArr']) ) {
            $sessionTourSubImgArr = array(array("",0), array("",0), array("",0), array("",0), array("",0), array("",0), array("",0), array("",0), array("",0));
            foreach ( $user->fetchAll(array("image_name"), array("tour_sub_images"), array("tour_id"=>$currentTourID)) as $row ) {
              $temp = explode("-", explode(".", $row['image_name'])[0])[1]-1;
              $sessionTourSubImgArr[$temp][0] = $row['image_name'];
              $sessionTourSubImgArr[$temp][1] = 1;
            }
            $_SESSION['sessionTourSubImgArr'] = $sessionTourSubImgArr;
          }
        } else {
          $user->redirect("./tours");
          
        }
      }
      if ( isset($_POST['addNewTourSubmit']) || isset($_POST['updateTourSubmit']) ) {
        $inputTourNo = htmlspecialchars((isset($_POST['inputTourNo'])) ? $_POST['inputTourNo'] : "");
        $inputTourTitle = htmlspecialchars((isset($_POST['inputTourTitle'])) ? $_POST['inputTourTitle'] : "");
        $inputTourType = htmlspecialchars((isset($_POST['inputTourType'])) ? $_POST['inputTourType'] : "");
        $inputTourDuration = htmlspecialchars((isset($_POST['inputTourDuration'])) ? $_POST['inputTourDuration'] : "");
        $inputTourGroup = htmlspecialchars((isset($_POST['inputTourGroup'])) ? $_POST['inputTourGroup'] : "");
        $inputTourVehicleType = htmlspecialchars((isset($_POST['inputTourVehicleType'])) ? $_POST['inputTourVehicleType'] : "");
        $inputTourGuide = htmlspecialchars((isset($_POST['inputTourGuide'])) ? $_POST['inputTourGuide'] : "");
        $inputTourPickupDrop = htmlspecialchars((isset($_POST['inputTourPickupDrop'])) ? $_POST['inputTourPickupDrop'] : "");
        $inputTourHotelType = htmlspecialchars((isset($_POST['inputTourHotelType'])) ? $_POST['inputTourHotelType'] : "");
        $inputTourDescription = strip_tags((isset($_POST['inputTourDescription'])) ? $_POST['inputTourDescription'] : "", "<br>");
        $inputTourArrivalDepartureLocation = htmlspecialchars((isset($_POST['inputTourArrivalDepartureLocation'])) ? $_POST['inputTourArrivalDepartureLocation'] : "");
        $inputTourDepatureTime = htmlspecialchars((isset($_POST['inputTourDepatureTime'])) ? $_POST['inputTourDepatureTime'] : "");
        $inputTourMealPlan = htmlspecialchars((isset($_POST['inputTourMealPlan'])) ? $_POST['inputTourMealPlan'] : "");
        $inputTourBedRoom = htmlspecialchars((isset($_POST['inputTourBedRoom'])) ? $_POST['inputTourBedRoom'] : "");
        $inputTourServicesIncluded = htmlspecialchars((isset($_POST['inputTourServicesIncluded'])) ? $_POST['inputTourServicesIncluded'] : "");
        $inputTourServicesExcluded = htmlspecialchars((isset($_POST['inputTourServicesExcluded'])) ? $_POST['inputTourServicesExcluded'] : "");
        $inputTourEmbedMap = htmlspecialchars((isset($_POST['inputTourEmbedMap'])) ? $_POST['inputTourEmbedMap'] : "");
        $howManyDays = (int)(isset($_POST['howManyDays'])) ? $_POST['howManyDays'] : 1;
        if ( isset($_POST['addNewTourSubmit']) ) {
          $tourID = $user->insertTable("tour_details", array(
            "no"=>$inputTourNo,
            "title"=>$inputTourTitle,
            "type"=>$inputTourType,
            "duration"=>$inputTourDuration,
            "tour_group"=>$inputTourGroup,
            "vehicle_type"=>$inputTourVehicleType,
            "guide"=>$inputTourGuide,
            "pickup_drop"=>$inputTourPickupDrop,
            "hotel_type"=>$inputTourHotelType,
            "description"=>$inputTourDescription,
            "arrival_departure_location"=>$inputTourArrivalDepartureLocation,
            "depature_time"=>$inputTourDepatureTime,
            "meal_plan"=>$inputTourMealPlan,
            "bed_room"=>$inputTourBedRoom,
            "services_included"=>$inputTourServicesIncluded,
            "services_excluded"=>$inputTourServicesExcluded,
            "map"=>$inputTourEmbedMap
          ), true);
          //main Image
          $inputTourMainImage =$tourID.".".pathinfo($_FILES["inputTourMainImage"]["name"], PATHINFO_EXTENSION);
          move_uploaded_file($_FILES["inputTourMainImage"]["tmp_name"], "../img/tours/" . $inputTourMainImage);
          $user->updateTable("tour_details", array("image_name"=>$inputTourMainImage), array("id"=>$tourID));
          //image 01 to 09
          for ( $i=1; $i<=9; $i++ ) {
            if ( !empty(pathinfo($_FILES["inputTourOtherImage$i"]["name"], PATHINFO_EXTENSION)) ) {
              $imageName =$tourID."-$i".".".pathinfo($_FILES["inputTourOtherImage$i"]["name"], PATHINFO_EXTENSION);
              move_uploaded_file($_FILES["inputTourOtherImage$i"]["tmp_name"], "../img/tours/" . $imageName);
              $user->insertTable("tour_sub_images", array("tour_id"=>$tourID, "image_name"=>$imageName));
            }
          }
          //main details
          for ( $i=1; $i<=$howManyDays; $i++ ) {
            $inputTourDayTitle = htmlspecialchars((isset($_POST["inputTourDayTitle$i"])) ? $_POST["inputTourDayTitle$i"] : "");
            $inputTourDayDescription = strip_tags((isset($_POST["inputTourDayDescription$i"])) ? $_POST["inputTourDayDescription$i"] : "", "<br>");
            $inputTourDayAccommodation = htmlspecialchars((isset($_POST["inputTourDayAccommodation$i"])) ? $_POST["inputTourDayAccommodation$i"] : "");
            $inputTourDayRoom = htmlspecialchars((isset($_POST["inputTourDayRoom$i"])) ? $_POST["inputTourDayRoom$i"] : "");
            $inputTourDayMealPlan = htmlspecialchars((isset($_POST["inputTourDayMealPlan$i"])) ? $_POST["inputTourDayMealPlan$i"] : "");
            $inputTourDayTravelTime = htmlspecialchars((isset($_POST["inputTourDayTravelTime$i"])) ? $_POST["inputTourDayTravelTime$i"] : "");
            $tourDayID = $user->insertTable("tour_day_details", array(
              "tour_id"=>$tourID,
              "title"=>$inputTourDayTitle,
              "description"=>$inputTourDayDescription,
              "accommodation"=>$inputTourDayAccommodation,
              "room"=>$inputTourDayRoom,
              "meal_plan"=>$inputTourDayMealPlan,
              "travel_time"=>$inputTourDayTravelTime,
              "image_name"=>""
            ), true);
            if ( !empty(pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION)) ) {
              $imageName =$tourID."-day".$tourDayID.".".pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION);
              move_uploaded_file($_FILES["inputTourMainDetailsDayImage$i"]["tmp_name"], "../img/tours/" . $imageName);
              $user->updateTable("tour_day_details", array("image_name"=>$imageName), array("id"=>$tourDayID));
            }
          }
          echo "<script>alert('Successfully added a new Tour');location.href='./createSiteMap?redirect=tours'</script>";
        } else if ( isset($_POST['updateTourSubmit']) ) {
          $user->updateTable("tour_details", 
            array(
              "no"=>$inputTourNo,
              "title"=>$inputTourTitle,
              "type"=>$inputTourType,
              "duration"=>$inputTourDuration,
              "tour_group"=>$inputTourGroup,
              "vehicle_type"=>$inputTourVehicleType,
              "guide"=>$inputTourGuide,
              "pickup_drop"=>$inputTourPickupDrop,
              "hotel_type"=>$inputTourHotelType,
              "description"=>$inputTourDescription,
              "arrival_departure_location"=>$inputTourArrivalDepartureLocation,
              "depature_time"=>$inputTourDepatureTime,
              "meal_plan"=>$inputTourMealPlan,
              "bed_room"=>$inputTourBedRoom,
              "services_included"=>$inputTourServicesIncluded,
              "services_excluded"=>$inputTourServicesExcluded,
              "map"=>$inputTourEmbedMap
            ), array("id"=>$currentTourID));
          //main image
          if ( !empty(pathinfo($_FILES["inputTourMainImage"]["name"], PATHINFO_EXTENSION)) ) {
            $inputTourMainImage =$currentTourID.".".pathinfo($_FILES["inputTourMainImage"]["name"], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES["inputTourMainImage"]["tmp_name"], "../img/tours/" . $inputTourMainImage);
            $user->updateTable("tour_details", array("image_name"=>$inputTourMainImage), array("id"=>$currentTourID));
          }
          //image 01 to 09
          $sessionTourSubImgArr = $_SESSION['sessionTourSubImgArr'];
          for ( $i=1; $i<=9; $i++ ) {
            if ( $sessionTourSubImgArr[$i-1][1]==-1 && $sessionTourSubImgArr[$i-1][0]!="" ) {
              $user->deleteTableRow("tour_sub_images", array("tour_id"=>$currentTourID, "image_name"=>$sessionTourSubImgArr[$i-1][0]));
              unlink("../img/tours/".$sessionTourSubImgArr[$i-1][0]);
            }
            if ( !empty(pathinfo($_FILES["inputTourOtherImage$i"]["name"], PATHINFO_EXTENSION)) ) {
              $imageName =$currentTourID."-$i".".".pathinfo($_FILES["inputTourOtherImage$i"]["name"], PATHINFO_EXTENSION);
              move_uploaded_file($_FILES["inputTourOtherImage$i"]["tmp_name"], "../img/tours/" . $imageName);
              $user->insertTable("tour_sub_images", array("tour_id"=>$currentTourID, "image_name"=>$imageName));
            } 
          }
          //main details
          $sessionTourDayImgArr = $_SESSION['sessionTourDayImgArr'];
          for ( $i=1; $i<=$howManyDays; $i++ ) {
            $inputTourDayTitle = htmlspecialchars((isset($_POST["inputTourDayTitle$i"])) ? $_POST["inputTourDayTitle$i"] : "");
            $inputTourDayDescription = strip_tags((isset($_POST["inputTourDayDescription$i"])) ? $_POST["inputTourDayDescription$i"] : "", "<br>");
            $inputTourDayAccommodation = htmlspecialchars((isset($_POST["inputTourDayAccommodation$i"])) ? $_POST["inputTourDayAccommodation$i"] : "");
            $inputTourDayRoom = htmlspecialchars((isset($_POST["inputTourDayRoom$i"])) ? $_POST["inputTourDayRoom$i"] : "");
            $inputTourDayMealPlan = htmlspecialchars((isset($_POST["inputTourDayMealPlan$i"])) ? $_POST["inputTourDayMealPlan$i"] : "");
            $inputTourDayTravelTime = htmlspecialchars((isset($_POST["inputTourDayTravelTime$i"])) ? $_POST["inputTourDayTravelTime$i"] : "");
            if ( count($sessionTourDayImgArr) >= $i ) {
              $imageName = $sessionTourDayImgArr[$i-1][1];
              if ( $sessionTourDayImgArr[$i-1][2]==-1 ) {
                unlink("../img/tours/$imageName");
                $imageName = "";
              }
              $tourDayID = $sessionTourDayImgArr[$i-1][0];
              if ( !empty(pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION)) || $sessionTourDayImgArr[$i-1][2]==1 || 
                !empty($inputTourDayTitle) || !empty($inputTourDayDescription) || !empty($inputTourDayAccommodation) || !empty($inputTourDayRoom) || !empty($inputTourDayMealPlan) || !empty($inputTourDayTravelTime)) {
                  if ( !empty(pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION)) ) {
                    $imageName =$currentTourID."-day".$tourDayID.".".pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputTourMainDetailsDayImage$i"]["tmp_name"], "../img/tours/" . $imageName);
                  }
                  $user->updateTable("tour_day_details", array(
                    "tour_id"=>$currentTourID,
                    "title"=>$inputTourDayTitle,
                    "description"=>$inputTourDayDescription,
                    "accommodation"=>$inputTourDayAccommodation,
                    "room"=>$inputTourDayRoom,
                    "meal_plan"=>$inputTourDayMealPlan,
                    "travel_time"=>$inputTourDayTravelTime,
                    "image_name"=>$imageName
                  ), array("id"=>$tourDayID));
              } else {
                $user->deleteTableRow("tour_day_details", array("id"=>$tourDayID));
              }
            } else {
              if ( !empty(pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION)) || 
              !empty($inputTourDayTitle) || !empty($inputTourDayDescription) || !empty($inputTourDayAccommodation) || !empty($inputTourDayRoom) || !empty($inputTourDayMealPlan) || !empty($inputTourDayTravelTime)) {
                $imageName = "";
                $tourDayID = $user->insertTable("tour_day_details", array(
                  "tour_id"=>$currentTourID,
                  "title"=>$inputTourDayTitle,
                  "description"=>$inputTourDayDescription,
                  "accommodation"=>$inputTourDayAccommodation,
                  "room"=>$inputTourDayRoom,
                  "meal_plan"=>$inputTourDayMealPlan,
                  "travel_time"=>$inputTourDayTravelTime,
                  "image_name"=>$imageName
                ), true);
                if ( !empty(pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION)) ) {
                  $imageName =$currentTourID."-day".$tourDayID.".".pathinfo($_FILES["inputTourMainDetailsDayImage$i"]["name"], PATHINFO_EXTENSION);
                  move_uploaded_file($_FILES["inputTourMainDetailsDayImage$i"]["tmp_name"], "../img/tours/" . $imageName);
                  $user->updateTable("tour_day_details", array("image_name"=>$imageName), array("id"=>$tourDayID));
                }
              }
            }
          }
          unset($_SESSION['sessionTourSubImgArr']);
          unset($_SESSION['sessionTourDayImgArr']);
          echo "<script>alert('Successfully updated a Tour'); location.href='./createSiteMap?redirect=tours'</script>";
        }
      } else if ( isset($_POST['confirmDeleteTourSubmit']) ) {
        $deleteTourID = (int)$_POST['deleteTourID'];
        foreach ( $user->fetchAll(array("image_name"), array("tour_day_details"), array("tour_id"=>$deleteTourID)) as $row ) {
          unlink("../img/tours/".$row['image_name']);
        }
        $user->deleteTableRow("tour_day_details", array("tour_id"=>$deleteTourID));
        foreach ( $user->fetchAll(array("image_name"), array("tour_sub_images"), array("tour_id"=>$deleteTourID)) as $row ) {
          unlink("../img/tours/".$row['image_name']);
        }
        $user->deleteTableRow("tour_sub_images", array("tour_id"=>$deleteTourID));
        foreach ( $user->fetchAll(array("image_name"), array("tour_details"), array("id"=>$deleteTourID)) as $row ) {
          unlink("../img/tours/".$row['image_name']);
        }
        $user->deleteTableRow("tour_details", array("id"=>$deleteTourID));
        echo "<script>alert('Successfully deleted a Tour'); location.href='./createSiteMap?redirect=tours'</script>";
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
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <?php echo $adminHeader->printAdminNav2(($editMode) ? "Edit Tour" : $adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <form accept="" method="post" enctype="multipart/form-data">
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("Tour No", "inputTourNo", "col-md-6", $currentTourNo);
                  ?>
                </div>
                <div class="row justify-content-center">
                  <?php
                    echo $widgets->inputGroup("Tour Title", "inputTourTitle", "", $currentTourTitle); 
                    echo $widgets->inputGroup("Tour Type", "inputTourType", "", $currentTourType); 
                  ?>
                </div>
                <div class="row border mx-3 mb-2">
                  <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <div class="form-group">
                      <label for="example-text-input" class="form-control-label">Main Image</label>
                      <input class="form-control" type="file" accept='image/*' onchange='loadImageFile(event)' name="inputTourMainImage">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center mt-3"><img id='outputTourMainImage' <?php echo $currentTourMainImage; ?> style='max-height: 200px; max-width:100%' /></p>
                  </div>
                </div>
                <div class="row justify-content-center px-2">
                  <?php

                    $sessionTourSubImgArr = $_SESSION['sessionTourSubImgArr'];
                    for ( $i=1; $i<=9; $i++ ) {
                      $tmpImg = ( $sessionTourSubImgArr[$i-1][1] == 1 ) ? "src=".$widgets->createCachelessImage("../img/tours/".$sessionTourSubImgArr[$i-1][0]) : "";

                      



                      echo "
                        <div class='col-md-3 border m-1'>
                          <div class='form-group'>
                            <label for='example-text-input' class='form-control-label'>Image 0$i</label>
                            <input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event, 1)' name='inputTourOtherImage$i'>
                            <span onclick='removeTourOtherImage($i)' class='text-danger cursor-pointer'>remove</span>
                            <p class='text-center my-1'><img $tmpImg id='outputTourOtherImage$i' style='max-height: 100px; max-width:100%' /></p>
                          </div>
                        </div>
                      ";
                    }
                  ?>
                </div>
                Basic Details
                <div class="row border mx-3">
                  <?php
                    echo $widgets->inputGroup("Tour Duration", "inputTourDuration", "col-md-4", $currentTourDuration);
                    echo $widgets->inputGroup("Group", "inputTourGroup", "", $currentTourGroup);
                    echo $widgets->inputGroup("Vehicle Type", "inputTourVehicleType", "", $currentTourVehicleType);
                    echo $widgets->inputGroup("Guide", "inputTourGuide", "", $currentTourGuide);
                    echo $widgets->inputGroup("Pick-up & Drop", "inputTourPickupDrop", "", $currentTourPickupDrop);
                    echo $widgets->inputGroup("Hotel Type", "inputTourHotelType", "", $currentTourHotelType);
                  ?>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Description</label>
                      <textarea class='form-control' name='inputTourDescription' rows="5" required><?php echo $currentTourDescription;?></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <?php
                    echo $widgets->inputGroup("Arrival & Departure Location", "inputTourArrivalDepartureLocation", "col-lg-3 col-md-6", $currentTourArrivalDepartureLocation);
                    echo $widgets->inputGroup("Depature Time", "inputTourDepatureTime", "", $currentTourDepatureTim);
                    echo $widgets->inputGroup("Meal Plan", "inputTourMealPlan", "", $currentTourMealPlan);
                    echo $widgets->inputGroup("Bed Room", "inputTourBedRoom", "", $currentTourBedRoom);
                  ?>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Services Included in the Price</label>
                      <textarea class='form-control' name='inputTourServicesIncluded' rows="5" required><?php echo $currentTourServicesIncluded;?></textarea>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for='example-text-input' class='form-control-label'>Services Excluded in the Price</label>
                      <textarea class='form-control' name='inputTourServicesExcluded' rows="5" required><?php echo $currentTourServicesExcluded;?></textarea>
                    </div>
                  </div>
                </div>
                Main Details
                <?php 
                  if ( !$editMode ) {
                    echo $widgets->addTourMainDetailsDiv(1); 
                  } else {
                    $i=0;
                    $sessionTourDayImgArr = array();
                    foreach ( $user->fetchAll(array("id", "image_name", "title", "description", "accommodation", "room", "meal_plan", "travel_time"), array("tour_day_details"), array("tour_id"=>$currentTourID)) as $row ) {
                      if ( $i>0 ) echo "<script>$('#addMoreTourDetails$i').css('display', 'none');</script>";
                      $sessionTourDayImgArr[$i] = array(
                        $row['id'], 
                        $row['image_name'],
                        ($row['image_name']!="") ? 1 : 0
                      );
                      $i++;
                      echo $widgets->addTourMainDetailsDiv($i, $row); 
                    }
                    $_SESSION['sessionTourDayImgArr'] = $sessionTourDayImgArr;
                    echo "
                    <script>
                      $(document).ready(function () {
                        $('input[name=howManyDays]').val($i);
                      })
                    </script>";
                  }
                ?>
                <div class="row justify-content-center" id="addMoreTourDayLoadingImage" style="display: none;">
                  <img src="../img/loading.gif" alt="Loading GIF" style="width: 100px;">
                </div>
                <div class="row mt-3">
                  <?php echo $widgets->inputGroup("Map", "inputTourEmbedMap", "col-12", $currentTourMap, "url"); ?>
                </div>
                <div class="row">
                  <div class="col-12">
                    <input type='hidden' name='howManyDays' value='1'>
                    <?php
                      if ( $editMode ) {
                        echo "
                        <input type='submit' class='btn btn-primary' value='Update Tour' name='updateTourSubmit'>
                        <input type='button' class='btn btn-danger' value='Delete Tour' onclick='deleteTourSubmit()'>
                        ";
                      } else {
                        echo "<input type='submit' class='btn btn-success' value='Add Tour' name='addNewTourSubmit'>";
                      }
                    ?>
                    <input type="button" class="btn btn-secondary" value="Cancel" onclick="location.href='./add-tours'">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
    <div id="removeTourOtherImage"></div>
    <div id="removeTourDayImage"></div>
  </main>
  <?php 
    echo $adminHeader->printAdminFooterJS(); 
    if ( $editMode ) {
      echo "
			<div class='modal fade' id='confirmDeleteTourModal' data-backdrop='static' tabindex='-1' role='dialog' aria-labelledby='staticBackdropLabel' aria-hidden='true' style='margin-top:200px'>
				<div class='modal-dialog' role='document'>
					<div class='modal-content'>
						<div class='modal-header'>
							<h5 class='modal-title' id='staticBackdropLabel'>Confirm Delete a Tour</h5>
						</div>
						<div class='modal-body'>
						<form action='' class='text-center' method='post'>
								No : $currentTourNo<br>
								Title : $currentTourTitle<br>
								<input type='hidden' name='deleteTourID' value='$currentTourID'>
							<br>
							<input type='submit' class='btn btn-danger btn-sm' name='confirmDeleteTourSubmit' value='Delete'>
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
      $("#"+imageDivID).addClass("border");
			var image = document.getElementById(imageDivID);
			image.src = URL.createObjectURL(event.target.files[0]);
      if ( sessionTF==1 ) {
        $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            changeTourOtherImage: imageDivID.substr(20,1)
          },
          success: function(html) {
              $("#removeTourOtherImage").html(html).show();
          }
        }); 
      } else if ( sessionTF==2 ) {
        $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            changeTourDayImage: imageDivID.substr(29,1)
          },
          success: function(html) {
              $("#removeTourDayImage").html(html).show();
          }
        }); 
      }
		}

    function removeTourOtherImage(imageID) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removeTourOtherImage: imageID
          },
          success: function(html) {
              $("#removeTourOtherImage").html(html).show();
          }
      }); 
    }

    function removeTourDayImage(imageID) {
      $.ajax({
          type: "POST",
          url: "ajax.php",
          data: {
            removeTourDayImage: imageID
          },
          success: function(html) {
              $("#removeTourDayImage").html(html).show();
          }
      }); 
    }

    function addMoreTourDayDetails(dayNumber) {
      $("#addMoreTourDetails"+dayNumber).css("display", "none");
      $("#addMoreTourDayLoadingImage").css("display", "flex");
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          addMoreTourDayDetails: dayNumber + 1
        },
        success: function(html) {
          $("#addMoreTourDayDetails"+dayNumber).html(html).show();
          $("#addMoreTourDayLoadingImage").css("display", "none");
          dayNumber++;
          $("input[name='howManyDays']").val(dayNumber);
        }
      }); 
    }
    function deleteTourSubmit() {
      $('#confirmDeleteTourModal').modal('show');
    }
  </script>
</body>

</html>