<?php
    session_start();
    require_once("./classes/class.user.php");
    require_once("./classes/class.header.php");
    require_once("./classes/class.widgets.php");
    $userHeader = new HEADER();
    $user = new USER();
    $widgets = new WIDGETS();

    if ( $user->is_loggedin("session_tourism_user") ) {
        if ( $user->checkTimeout() ) {
            $touristID = $user->sessionUser("session_tourism_user");
            $touristArr = $user->fetchAll(array("name", "profile_pic", "country"), array("tourists"), array("id"=>$touristID))[0];
            $touristName = ($touristArr["name"] == NULL) ? "" : $touristArr["name"];
            $touristCountry = $touristArr["country"];
            $touristProfilePic = ( $touristArr["profile_pic"] == NULL ) ? "" : "src='".$widgets->createCachelessImage("./img/profile-pics/".$touristArr["profile_pic"])."'";
            if ( isset($_POST['addTestimonialSubmit']) || isset($_POST['updateTestimonialSubmit']) ) {
                $inputTouristName = htmlspecialchars(isset($_POST['inputTouristName']) ? $_POST['inputTouristName'] : "");
                $inputTouristCountry = htmlspecialchars(isset($_POST['inputTouristCountry']) ? $_POST['inputTouristCountry'] : "");
                $starRating = htmlspecialchars(isset($_POST['starRating']) ? $_POST['starRating'] : "");
                $inputOneWord = htmlspecialchars(isset($_POST['inputOneWord']) ? $_POST['inputOneWord'] : "");
                $inputReview = strip_tags(isset($_POST['inputReview']) ? $_POST['inputReview'] : "", "<br>");
                if ( !empty(pathinfo($_FILES["inputProfilePic"]["name"], PATHINFO_EXTENSION) ) ) {
                    if ( $touristArr["profile_pic"] != NULL ) {
                        unlink("./img/profile-pics/".$touristArr["profile_pic"]);
                    }
                    $inputProfilePic =$touristID.".".pathinfo($_FILES["inputProfilePic"]["name"], PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES["inputProfilePic"]["tmp_name"], "./img/profile-pics/" . $inputProfilePic);
                    $user->updateTable("tourists", array("profile_pic"=>$inputProfilePic), array("id"=>$touristID));
                } 
                $user->updateTable("tourists", array("name"=>$inputTouristName, "country"=>$inputTouristCountry), array("id"=>$touristID));
                if ( isset($_POST['addTestimonialSubmit']) ) {
                    $testimonialID = $user->insertTable("testimonials", array(
                        "user_id"=>$touristID,
                        "ratings"=>$starRating,
                        "one_word"=>$inputOneWord,
                        "review"=>$inputReview,
                    ), true);
                    for ( $i=1; $i<=6; $i++ ) {
                        if ( !empty(pathinfo($_FILES["inputTestimonialImage$i"]["name"], PATHINFO_EXTENSION) ) ) {
                            $inputTestimonialImage =$testimonialID."-$i.".pathinfo($_FILES["inputTestimonialImage$i"]["name"], PATHINFO_EXTENSION);
                            move_uploaded_file($_FILES["inputTestimonialImage$i"]["tmp_name"], "./img/testimonials/" . $inputTestimonialImage);
                            $user->insertTable("testimonials_images", array("testimonial_id"=>$testimonialID, "image"=>$inputTestimonialImage));
                        }
                    }
                    echo "<script>alert('Successfully added a new Testimonial'); location.href='./account'</script>";
                } else if ( isset($_POST['updateTestimonialSubmit']) ) {
                    $testimonialID = (int)$_POST['hiddenTestimonialID'];
                    $user->updateTable("testimonials", array("ratings"=>$starRating,"one_word"=>$inputOneWord,"review"=>$inputReview,"status"=>0), array("id"=>$testimonialID, "user_id"=>$touristID));
                    $sessionImageArr = $_SESSION['sessionImageArr'];
                    for ( $i=1; $i<=6; $i++ ) {
                        if ( $sessionImageArr[$i-1][1] == -1 ) {
                            if ( $sessionImageArr[$i-1][0] != "" ) {
                                unlink("./img/testimonials/".$sessionImageArr[$i-1][0]);
                                $user->deleteTableRow("testimonials_images", array("testimonial_id"=>$testimonialID, "image"=>$sessionImageArr[$i-1][0]));
                            }
                        }
                        if ( !empty(pathinfo($_FILES["inputTestimonialImage$i"]["name"], PATHINFO_EXTENSION) ) ) {
                            $inputTestimonialImage =$testimonialID."-$i.".pathinfo($_FILES["inputTestimonialImage$i"]["name"], PATHINFO_EXTENSION);
                            move_uploaded_file($_FILES["inputTestimonialImage$i"]["tmp_name"], "./img/testimonials/" . $inputTestimonialImage);
                            $user->insertTable("testimonials_images", array("testimonial_id"=>$testimonialID, "image"=>$inputTestimonialImage));
                        }
                    }
                    unset($_SESSION['sessionImageArr']);
                    echo "<script>alert('Successfully updated the Testimonial'); //location.href='./account'</script>";
                }
            } else if ( isset($_POST['confirmDeleteTestimonial']) ) {
                $deleteTestimonialID = (int)$_POST['deleteTestimonialID'];
                if ( $user->CountRows("testimonials", array("id"=>$deleteTestimonialID, "user_id"=>$user->sessionUser("session_tourism_user"))) == 1 ) {
                    foreach ( $user->fetchAll(array("image"), array("testimonials_images"), array("testimonial_id"=>$deleteTestimonialID)) as $row ) {
                        unlink("./img/testimonials/".$row['image']);
                    }
                    $user->deleteTableRow("testimonials_images", array("testimonial_id"=>$deleteTestimonialID));
                    $user->deleteTableRow("testimonials", array("id"=>$deleteTestimonialID));
                    echo "<script>alert('Successfully deleted the Testimonial'); location.href='./account'</script>";
                } else {
                    echo "<script>alert('No records found!'); location.href='./account'</script>";
                }
            }
        } else {
            $user->doLogout2("./login", "session_tourism_user");
        }
    } else {
        $user->doLogout2("./login", "session_tourism_user");
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta property='og:title' content='Traveylo | Sri Lanka Tour Packages | Travel Agent in Sri Lanka'/>
    <meta name='description' content='“Ayubowan!” Traveylo.com provides tour packages covering the most beautiful places 
in Sri Lanka, and you can travel in luxury with your own vehicle around 
our beautiful country. So reserve your tour with us.' />
    <meta name='keywords' content='Travel Agents In Sri Lanka / Sri Lanka Tourism / Sri Lanka Tourist Destinations / Places To Visit In Sri Lanka With Family / How To Travel In Sri Lanka / Sri Lanka Tours & Travels / Tour Packages In Sri Lanka / Sri Lanka Itinerary / Sri Lanka Travel Guide /Sri Lanka HotelsSri Lanka Tour Operators /Sri Lanka Budgets Tours /Small Group Tour In Sri Lanka / Sri Lanka Holiday Packages /Sri Lanka Tour Packages For Couple / Sri Lanka Tour Packages For Family /Sri Lanka Tour Packages Price / What To Do In Sri Lanka /Popular Destinations In Sri Lanka' />
    <!-- Mobiscroll JS and CSS Includes -->
    <link rel="stylesheet" href="./admin-area/assets/css/mobiscroll.javascript.min.css">
    <script src="./admin-area/assets/js/mobiscroll.javascript.min.js"></script>
    <?php echo $userHeader->printUserHeader("Account") ?>
    <style>
        .md-country-picker-item {
            position: relative;
            line-height: 20px;
            padding: 10px 0 10px 40px;
        }
        .md-country-picker-flag {
            position: absolute;
            left: 0;
            height: 20px;
        }
        .mbsc-scroller-wheel-item-2d .md-country-picker-item {
            transform: scale(1.1);
        }
        .inputRatingStar, td{
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php
        echo $userHeader->printUserNav(true);        //Topbar
        
    ?>

    <!-- About Start -->
    <div class="container-fluid py-4" style="margin-top: 70px !important;">
        <div class="container">
            <i class="fa fa-home pt-1 pr-2 text-primary"></i><a href="./">Home</a><i class="fa fa-angle-right pt-1 px-2 text-primary"></i>Account
            <h4 class="text-warning mt-2">Account</h4>
        </div>
    </div>
    <!-- About End -->

    <div class="container-fluid pt-3 pb-5">
        <div class="container">
            <?php if ( $user->CountRows("testimonials", array("user_id"=>$user->sessionUser("session_tourism_user"))) > 0 ) { ?>
            <div class="row mb-4">
                <div class="card col-12">
                    <div class="card-body">
                        <h5 class="font-weight-bold">Your Testimonials</h5>
                        <div class="table-responsive">
                            <table class="table align-items-center" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class='text-center'>Short Review</th>
                                        <th class='text-center'>Ratings</th>
                                        <th class='text-center'>Status</th>
                                        <th class='text-center'>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ( $user->fetchAll(array("id","one_word","ratings","status","timestamp"), array("testimonials"), array("user_id"=>$user->sessionUser("session_tourism_user")), "timestamp DESC") as $tableRow ) {
                                            $testimonialID = $tableRow['id'];
                                            $testimonialStatus = $tableRow['status'];
                                            $testimonialStatus = ($testimonialStatus==1) ? "Approved" : (($testimonialStatus==-1) ? "Rejected" : "-");
                                            echo "
                                                <tr>
                                                    <td class='text-center' onclick='editTestimonial($testimonialID)'>".$tableRow['one_word']."</td>
                                                    <td class='text-center' onclick='editTestimonial($testimonialID)'>";
                                                        for ( $i=1; $i<=5; $i++ ) {
                                                            $starColor = ($i<=$tableRow['ratings']) ? "text-warning" : "";
                                                            echo "<span class='fa fa-star $starColor'></span>";
                                                        }
                                            echo "</td>
                                                    <td class='text-center' onclick='editTestimonial($testimonialID)'>$testimonialStatus</td>
                                                    <td class='text-center' onclick='editTestimonial($testimonialID)'>".$tableRow['timestamp']."</td>
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
            <?php } ?>
            <div class="row">
                <div class="card col-12">
                    <div class="card-body">
                        <h5 class="font-weight-bold" id="addEditTestimonialHeading">Add Testimonial</h5>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        Name<input class='form-control' type='text' name='inputTouristName' value='<?php echo $touristName; ?>' required>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            Country
                                            <input mbsc-input id="demo-country-picker" name="inputTouristCountry" data-dropdown="true" data-input-style="box" data-label-style="stacked" placeholder="Please select..." required/>
                                        </label>
                                    </div>
                                    Rating &nbsp;
                                    <span class="fa fa-star inputRatingStar text-warning" id="star1"></span>
                                    <span class="fa fa-star inputRatingStar" id="star2"></span>
                                    <span class="fa fa-star inputRatingStar" id="star3"></span>
                                    <span class="fa fa-star inputRatingStar" id="star4"></span>
                                    <span class="fa fa-star inputRatingStar" id="star5"></span>
                                    <input type="hidden" name="starRating" value="1">
                                    <div class="form-group mt-2">
                                        Say your review in one word<input class='form-control' type='text' name='inputOneWord' maxlength="50" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class='col-12 border m-1'>
                                        <div class='form-group'>
                                            <label for='example-text-input' class='form-control-label'>Profile Picture</label>
                                            <input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event)' name='inputProfilePic'>
                                            <p class='text-center my-1'><img id='outputProfilePic' <?php echo $touristProfilePic; ?> style='max-height: 200px; max-width:100%' /></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    Leave a Review<textarea name="inputReview" class="form-control" rows="5" maxlength="500"></textarea>
                                </div>
                            </div>
                            <div class="row justify-content-center mb-2">
                                <?php
                                    for ( $i=1; $i<=6; $i++ ) {
                                        echo "
                                            <div class='col-md-3 border m-1'>
                                                <div class='form-group'>
                                                    <label for='example-text-input' class='form-control-label'>Image 0$i</label>
                                                    <input class='form-control' type='file' accept='image/*' onchange='loadImageFile(event, 1)' name='inputTestimonialImage$i'>
                                                    <button type='button' id='testimonialImageRemoveDiv$i' onclick='removeTestimonialImage($i)' class='btn btn-sm btn-outline-danger float-right'>remove</button>
                                                    <p class='text-center my-1'><img id='outputTestimonialImage$i' style='max-height: 100px; max-width:100%' /></p>
                                                </div>
                                            </div>
                                        ";
                                    }
                                ?>
                            </div>
                            <input type="hidden" name="hiddenTestimonialID" value="0">
                            <input type="submit" class="btn btn-primary px-4" value="Add Testimonial" name="addTestimonialSubmit">
                            <input type="submit" class="btn btn-info px-4" value="Update" name="updateTestimonialSubmit" disabled>
                            <input type="button" class="btn btn-danger px-4" value="Delete" name="deleteTestimonialSubmit" onclick="deleteTestimonial()" disabled>
                            <input type="button" class="btn btn-secondary px-4" value="Cancel" onclick="location.reload()">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="editTestimonial"></div>
    <div id="deleteTestimonial"></div>
    <div id="removeTestimonialImage"></div>
    <!-- Footer Start -->
    <?php 
        echo $userHeader->printUserFooter(); 
        echo "
        <script>
            $( document ).ready(function() {
                $('input[name=inputTouristCountry]').val('$touristCountry')
            }); 
        </script>";
    ?>
    <!-- Footer End -->
    <script>
        mobiscroll.setOptions({
            theme: 'ios',
            themeVariant: 'light'
        });
        var inst = mobiscroll.select('#demo-country-picker', {
            display: 'anchored',
            filter: true,
            itemHeight: 40,
            renderItem: function (item) {
                return '<div class="md-country-picker-item">' +
                    '<img class="md-country-picker-flag" src="https://img.mobiscroll.com/demos/flags/' + item.data.value + '.png" />' +
                    item.display + '</div>';
            }
        });
        mobiscroll.util.http.getJson('https://trial.mobiscroll.com/content/countries.json', function (resp) {
            var countries = [];
            for (var i = 0; i < resp.length; ++i) {
                var country = resp[i];
                countries.push({ text: country.text, value: country.value });
            }
            inst.setOptions({ data: countries });
        });
        $(".inputRatingStar").click(function (event){
            var starNumber = event['target']['id'].substr(4,1);
            $("input[name='starRating']").val(starNumber);
            for ( var i=1; i<=5; i++ ) {
                if ( starNumber >= i ) {
                    $("#star"+i).addClass("text-warning");
                } else {
                    $("#star"+i).removeClass("text-warning");
                }
            }
        });

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
                        changeTestimonialImage: imageDivIdNumber
                    },
                    success: function(html) {
                        $("#removeTestimonialImage").html(html).show();
                    }
                }); 
            }
        }

        function editTestimonial(testimonialID) {
            $("#addEditTestimonialHeading").text("Edit a Testimonial");
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {
                    editTestimonial: testimonialID
                },
                success: function(html) {
                    $("input[name='hiddenTestimonialID']").val(testimonialID);
                    $("input[name='addTestimonialSubmit']").prop("disabled",true);
                    $("input[name='updateTestimonialSubmit']").prop("disabled",false);
                    $("input[name='deleteTestimonialSubmit']").prop("disabled",false);
                    $("#editTestimonial").html(html).show();
                }
            }); 
            $('html, body').animate({
                scrollTop: $("#addEditTestimonialHeading").offset().top
            });
        }

        function deleteTestimonial() {
            var testimonialID = $("input[name='hiddenTestimonialID']").val();
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {
                    deleteTestimonial: testimonialID
                },
                success: function(html) {
                    $("#deleteTestimonial").html(html).show();
                }
            }); 
        }

        function removeTestimonialImage(imageID) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {
                    removeTestimonialImage: imageID
                },
                success: function(html) {
                    $("#removeTestimonialImage").html(html).show();
                }
            }); 
        }
    </script>
</body>

</html>