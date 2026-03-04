<?php
    session_start();
    require_once("./classes/class.user.php");
    require_once("./classes/class.header.php");
    require_once("./classes/class.widgets.php");
    $userHeader = new HEADER("home");
    $user = new USER();
    $widgets = new WIDGETS();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    

    <?php echo $userHeader->printUserHeader() ?>
    <style>
        .testimonial-bg{
            background: url("./img/Web pic/testimonial.jpg")  no-repeat left center;
            background-size: 100% 70%;
        }
        @media (max-width:768px) {
            .testimonial-bg{
                background: none;
            }
        }
        @media (max-width:650px) {
            .headerLogo, .signInText{
                display: flex;
            }
        }
    </style>

    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8382700902937604"
     crossorigin="anonymous"></script>
    
</head>

<body class="index">


<?php //include 'eeeee.php';?>



    <?php 
        echo $userHeader->printUserTopBar();        //Topbar
        //echo $userHeader->printUserNav();       //Navbar
        //Carousel
        
        if ( !$user->CountRows("carousel", array("type"=>"video", "status"=>1)) ) {
            $carouselData = $user->fetchAll(array("text1", "text2", "src"), array("carousel"), array("type"=>"img", "status"=>1), "display_order");
            echo $userHeader->printHomeCarousel($carouselData);
        } 
    ?>

    
    <!-- Carousel End -->

    <div class="container-fluid py-4 mt-5" id="ayubowan">
        <div class="container littlebuddiescontainer py-5">
            <div class="row littlebuddiesrow justify-content-center">
                <div class="col-lg-5 littlebuddiestextcolumn col-md-6">
                    <h1 class="text-primary homemaintext">Hello,</h1>
                    <h5 class="text-warning littlebud">LITTLE BUDDIES</h5>
                    <h1 class="text-primary homemaintext2">Learning today for a better tomorrow. </h1>
                    <p class="text-justify mt-4 homeptext">
                    Hey, I'm edi. Welcome to my awesome world. 
                    Yes, I know, as a kid, you love to play and have lots of fun. 
                    But you must remember that learning and studying are very important for you to face challenges. 
                    So, I am here to support you. Let's engage in some cool activities that will help you to become more creative, 
                    wise, and the best, in the learning process. So come on, let's explore and have a blast.
                    <br> 
                   
                    </p>
                </div>
                <div class="col-lg-6 bearbox col-md-6 col-sm-6 d-flex align-items-center">
                    <img src="./img/Web pic/homebg.png" width="100%" alt="">
                </div>
            </div>
        </div>
    </div>

    <?php
        /*if ( $user->CountRows("carousel", array("type"=>"main", "status"=>1)) ) {
            $homeMainVideoURL = $user->fetchAll(array("src"), array("carousel"), array("type"=>"main", "status"=>1))[0]['src'];
           echo $widgets->displayHomeMainVideo($homeMainVideoURL);
        } */
    ?>

    <!--How it works-->
    <div class="container-fluid py-4">
        <div class="container py-5">
            <div class="text-center">
                <h1 class="text-primary">CHOOSE WHAT YOU WANT</h1>
            </div>

            <div class='row mt-3 justify-content-center'>
                <div class="col-lg-10 col-md-12 row">
                <p class="text-justify">
                In here, you can find awesome coloring pages, fun activity books, model papers, and all the information & study materials you need for your school assignments. Just click on the icon that has what you are looking for, and you are good to go!</p>
                </div>
            </div>

            <div class='row mt-5 justify-content-center'>
                <div class="col-lg-10 col-md-12 row">
                    <?php
                        echo $widgets->displayHowItWorksBlock("COLORING PAGES", "Find a variety of beautiful<br>coloring pages", "1.png");
                        echo $widgets->displayHowItWorksBlock2("BOOKS & PAPERS", "Find kids' workbooks &<br>relevant model papers", "2.png");
                        echo $widgets->displayHowItWorksBlock3("STUDY PACKS", "Find kids' school<br>homework-related items", "3.png");
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!--Search Section-->
     <!--------
    <div class="container-fluid py-4">
        <div class="container py-5">
            <div class="text-center">
                <h1 class="text-primary">FIND WHAT YOU WANT</h1>
            </div>
             <div class='row mt-3 justify-content-center'>
                <div class="col-lg-10 col-md-12 row">
                <p class="text-justify">
                In here, you can find awesome coloring pages, fun activity books, model papers, and all the information & study materials you need for your school assignments. Just click on the icon that has what you are looking for, and you are good to go!</p>
                </div>
            </div>
            
            <div class="row mt-3 justify-content-center">
                <div class="col-md-3 mb-2 px-xl- text-center">
                    <select id="language" name="language" class="green-input">
                        <option value="">Language (Required)</option>
                        <option value="1">Sinhala</option>
                        <option value="2">Tamil</option>
                        <option value="3">English</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-2 px-xl- text-center">
                    <select id="grade" name="grade" class="green-input">
                        <option value="">Grade (Required)</option>
                        <option value="1">LKG</option>
                        <option value="2">UKG</option>
                        <option value="3">Grade-1</option>
                        <option value="4">Grade-2</option>
                        <option value="5">Grade-3</option>
                        <option value="6">Grade-4</option>
                        <option value="7">Grade-5</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-2 px-xl- text-center">
                    <select id="main_category" name="main_category" class="green-input">
                        <option value="">Category (Required)</option>
                        <option value="1">Leisure Activities</option>
                        <option value="2">Books & Papers</option>
                        <option value="3">Study Packs</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-2 px-xl- text-center">
                    <select id="sub_category" name="sub_category" class="green-input">
                        <option value="">Sub Category</option>
                        <option value="1">Animals</option>
                    </select>
                </div>
            </div>
            <div class="text-center pt-5 ">
                <button class="btn btn-primary px-4 rounded search-btn" >SEARCH</button>
            </div>
         </div>
        
     </div>
    ---------->

    <?php
    $ad1Rows = $user->fetchAll(
        array("id", "tag", "title", "image", "description", "timestamp", "adlink"),
        array("ad1_details"),
        array("status" => "1"),
        "id DESC LIMIT 1"
    );
    
    if (!empty($ad1Rows)) {
        $row = $ad1Rows[0];
        $lastad1ID = "id<" . $row['id'];
        ?>
        <div style="display:flex; justify-content:center; overflow:hidden;" class="mt-5 mb-5">
            <div style="background-color: #fff; height: auto; width:80%; display:flex; align-items:center; justify-content:center;">
                <h1 class="text-center d-none"> ADD SPACE </h1>
                <div class="row">
                    <?php
                        echo $widgets->displayad1Brief($row, 600, true);
                    ?>
                </div>      
            </div>
        </div>
        <?php
    } else {
        $lastad1ID = "";
    }
    ?>

    <!---- ad space start ------->
    <div style="display:flex; justify-content:space-around;" class="mt-5 mb-5">
        <div style="background-color: #fff; border: 1px solid #8c8c8c; color:#000; height: 180px; width: 70%; display:flex; align-items:center; justify-content:space-around;">
            <h4 class="text-center" style="font-size:14px; font-weight:400 !important;"> Advertiesment </h4>
        </div>
    </div>
    <!---- ad space End------->

    <!-- Tour Packages -->
    <!--------
    <div class="container-fluid py-4">
        <div class="container py-5">
            <div class="text-center">
                <h1 class="text-primary">PERFECT TOUR PACKAGES</h1>
            </div>
            <div class="row justify-content-center">
                <p class="text-justify mt-3 col-lg-10">
                    We offer you the best tour packages covering magical and mystical places in Sri Lanka. 
                    We guarantee you will experience one of the most passionate and relaxing vacations with the best tours around the island along with our previous experiences. 
                    You are totally free to choose any of our standard packages or to customize your tour with our guidelines.
                </p>
            </div>
            <div class="row mt-4">
                <?php
                //if ( $user->CountRows("tour_details", array("status"=>"1")) > 0 ) {
                    //foreach( $user->fetchAll(array("id", "no", "title", "type", "image_name", "description", "duration"), array("tour_details"), array("status"=>"1"), "no") as $value ) {
                        echo $widgets->displayToursBriefInHome($value);
                    //}
                //}
                ?>
            </div>
            
        </div>
    </div>

    ---->

    <!-- Testimonials -->
    <div class="container-fluid pt-4">
        <div class="container pt-5 pb-4">
            <div class="text-center">
                <h1 class="text-primary">WHAT PEOPLE THINK ABOUT US</h1>
                
            </div>

            <div class='row mt-3 justify-content-center'>
                <div class="col-lg-10 col-md-12 row">
                <p class="text-justify">
                All of us spend a busy life in today’s world. So, meeting all of your child's educational and recreational needs could be a challenge for you. But don't worry, I am here to help. I love to hear your thoughts on this, so please share your comments below. </p>
                </div>
            </div>


<!-- 
            <div class="row justify-content-center">
                <p class="text-center col-lg-10 mt-3 px-lg-5">
                
                </p>
            </div> -->
            <div class="row justify-content-center mt-4 testimonial-sec">

                <?php
                    foreach ( $user->fetchAll(array("user_id","ratings","one_word","review"), array("testimonials"), array("status"=>1), "id DESC LIMIT 3") as $testimonialArr ) {
                        echo $widgets->displayTestimonialBrief(array_merge($testimonialArr, $user->fetchAll(array("name","profile_pic","country"), array("tourists"), array("id"=>$testimonialArr['user_id']))[0]));
                    }
                ?>
<!-----
            <div>
                <div class="indextestimonials">

                

                </div>
            </div>
            ---->

               

            </div>
            <div class="text-center pt-4 pb-5">
                <button class="btn btn-primary px-4 rounded" onclick="location.href='./testimonials'">SEE MORE</button>
            </div>
        </div>
    </div>

    <!-- <div class="testimonial-bg">
        <div class="row justify-content-center py-4 px-1 px-md-3 px-lg-5" style="margin: 0;">
            <?php
                // foreach ( $user->fetchAll(array("user_id","ratings","one_word","review"), array("testimonials"), array("status"=>1), "id DESC LIMIT 3") as $testimonialArr ) {
                //     echo $widgets->displayTestimonialBrief(array_merge($testimonialArr, $user->fetchAll(array("name","profile_pic","country"), array("tourists"), array("id"=>$testimonialArr['user_id']))[0]));
                // }
            ?>
        </div>
    </div>
    <div class="text-center mt-2 pb-5">
        <button class="btn btn-primary px-4 rounded" onclick="location.href='./testimonials'">SEE MORE</button>
    </div>
    <div class="pb-4"></div> -->

    <!-- Blog Start -->
    <div class="container-fluid py-4">
        <div class="container py-5">
            <div class="text-center">
                <h1 class="text-primary">FUN ACTIVITIES</h1>
            </div>

            <div class='row mt-3 justify-content-center'>
                <div class="col-lg-10 col-md-12 row">
                <p class="text-justify">
                We offer a variety of activities that promote your child's education while ensuring they have fun and enjoyable learning experience. Through these activities, children will develop a greater enthusiasm for learning. Furthermore, these activities help to address any type of challenges that may come up to the child. This is our primary goal.</p>
                </div>
            </div>



            <!-- <div class="row justify-content-center">
                <p class="text-center col-lg-10 mt-3 px-lg-5">
                
                </p>
            </div> -->
            <div class="row mt-3 justify-content-center">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <?php
                            foreach ( $user->fetchAll(array("id","tag","title","image", "description","timestamp"), array("blog_details"), array("status"=>"1"), "id DESC LIMIT 1") as $row ) {
                                echo $widgets->displayBlogBrief($row, "col-12", 600, true);
                            }
                            if ( $row['id'] ) {
                                $lastBlogID = "id<".$row['id'];
                            } else {
                                $lastBlogID = "";
                            }
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row justify-content-center">
                    <?php
                        foreach ( $user->fetchAll(array("id","tag","title","image", "description","timestamp"), array("blog_details"), array("status"=>"1"), "id DESC LIMIT 4", "$lastBlogID") as $row ) {
                            echo $widgets->displayBlogBrief($row, "col-md-6", 160, true);
                        }
                    ?>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <button class="btn btn-primary px-4 rounded" onclick="location.href='./blogs'">SEE MORE</button>
            </div>
        </div>
    </div>
    <!-- Blog End -->

    
    <!-- PDF Start -->
    <!-- <div class="container-fluid py-4">
        <div class="container py-5">
            <div class="text-center">
                <h1 class="text-primary">ARTICLE & TRAVEL GUIDINGS</h1>
            </div>
            <div class="row justify-content-center">
                <p class="text-justify col-lg-10 mt-3 px-lg-5">
                    Sri Lanka is an amazing travel destination which offers a wide range of places to visit. So here is some information to make your journey a perfect one.
                </p>
            </div>
            <div class="row mt-3 justify-content-center">
                <div class="col-md-6"> -->
                <?php
                    // foreach ( $user->fetchAll(array("id","tag","title","image", "description","timestamp"), array("pdf_details"), array("status"=>"1"), "id DESC LIMIT 1") as $row ) {
                    //     echo $widgets->displayPdfBrief($row, "col-12", 600);
                    // }
                    // if ( $row['id'] ) {
                    //     $lastpdfID = "id<".$row['id'];
                    // } else {
                    //     $lastpdfID = "";
                    // }
                ?>
                <!-- </div>
                <div class="col-md-6">
                    <div class="row justify-content-center"> -->
                    <?php
                        // foreach ( $user->fetchAll(array("id","tag","title","image", "description","timestamp"), array("pdf_details"), array("status"=>"1"), "id DESC LIMIT 4", "$lastpdfID") as $row ) {
                        //     echo $widgets->displayPdfBrief($row, "col-md-6", 160);
                        // }
                    ?>
                    <!-- </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <button class="btn btn-primary px-4 rounded" onclick="location.href='./pdf'">SEE MORE</button>
            </div>
        </div>
    </div> -->
    <!-- PDF End -->




<div style="display:flex; justify-content:space-around; overflow:hidden;" class="mt-5 mb-5">
    <div style="background-color: #fff; height: auto; width: 80%; display:flex; align-items:center; justify-content:space-around;">
        <h1 class="text-center d-none"> ADD SPACE </h1>
        <div class="row">
                <?php
                    foreach ( $user->fetchAll(array("id","tag","title","image", "description","timestamp", "adlink"), array("ad2_details"), array("status"=>"1"), "id DESC LIMIT 1") as $row ) {
                        echo $widgets->displayad2Brief($row, 600, true);
                    }
                    if ( $row['id'] ) {
                        $lastad2ID = "id<".$row['id'];
                    } else {
                        $lastad2ID = "";
                    }
                ?>
        </div>    
    </div>
</div>

    <!-- Footer Start -->
    <?php echo $userHeader->printUserFooter(); ?>
    <!-- Footer End -->
    <script>
        function goToAyubowan() {
            $('html, body').animate({
                scrollTop: $("#ayubowan").offset().top
            });
        }
    </script>
</body>

</html>