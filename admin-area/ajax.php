<?php
    session_start();
    require_once("../classes/class.user.php");
    require_once("../classes/class.widgets.php");
    $user = new USER();
    $widgets = new WIDGETS();
    if ( $user->is_loggedin() ) {
        if ( $user->checkTimeout() ) {

            if ( isset($_POST['chngCruslImgDisOrdrAndSts']) ) {
                $carouselArr = $_POST['chngCruslImgDisOrdrAndSts'];
                $user->updateTable("carousel", array("display_order"=>(int)$carouselArr['carouselDisplayOrder'],"status"=>(int)$carouselArr['carouselStatus']), array("id"=>(int)$carouselArr['carouselImgID']));
            }

            if ( isset($_POST['chngTourSts']) ) {
                $tourArr = $_POST['chngTourSts'];
                $user->updateTable("tour_details", array("status"=>(int)$tourArr['tourStatus']), array("id"=>(int)$tourArr['tourID']));
            }

            if ( isset($_POST['deleteCarouselImage']) ) {
                $carouselImgID = (int)$_POST['deleteCarouselImage'];
                $user->deleteTableRow("carousel", array("id"=>$carouselImgID));
                echo "<script>location.reload();</script>";
            }

            if ( isset($_POST['addMoreTourDayDetails']) ) {
                $dayNumber = (int)$_POST['addMoreTourDayDetails'];
                $widgets = new WIDGETS();
                echo $widgets->addTourMainDetailsDiv($dayNumber);
            }

            if ( isset($_POST['removeTourOtherImage']) ) {
                $imgID = (int)$_POST['removeTourOtherImage'];
                echo "
                    <script>
                        $('input[name=inputTourOtherImage$imgID]').val('');
                        $('#outputTourOtherImage$imgID').removeAttr('src');
                    </script>
                ";
                if ( isset($_SESSION['sessionTourSubImgArr']) && $_SESSION['sessionTourSubImgArr'][$imgID-1][0] != "" ) {
                    $_SESSION['sessionTourSubImgArr'][$imgID-1][1] = -1;
                }
            }

            if ( isset($_POST['changeTourOtherImage']) ) {
                $imgID = (int)$_POST['changeTourOtherImage'];
                if ( isset($_SESSION['sessionTourSubImgArr']) && $_SESSION['sessionTourSubImgArr'][$imgID-1][0] != "" ) {
                    $_SESSION['sessionTourSubImgArr'][$imgID-1][1] = -1;
                }
            }

            if ( isset($_POST['removeTourDayImage']) ) {
                $imgID = (int)$_POST['removeTourDayImage'];
                echo "
                    <script>
                        $('input[name=inputTourMainDetailsDayImage$imgID]').val('');
                        $('#outputTourMainDetailsDayImage$imgID').removeAttr('src');
                    </script>
                ";
                if ( isset($_SESSION['sessionTourDayImgArr']) && count($_SESSION['sessionTourDayImgArr'])>=$imgID && $_SESSION['sessionTourDayImgArr'][$imgID-1][1] != "" ) {
                    $_SESSION['sessionTourDayImgArr'][$imgID-1][2] = -1;
                }
            }

            if ( isset($_POST['changeTourDayImage']) ) {
                $imgID = (int)$_POST['changeTourDayImage'];
                if ( isset($_SESSION['sessionTourDayImgArr']) && count($_SESSION['sessionTourDayImgArr'])>=$imgID && $_SESSION['sessionTourDayImgArr'][$imgID-1][1] != "" ) {
                    $_SESSION['sessionTourDayImgArr'][$imgID-1][2] = -1;
                }
            }

            if ( isset($_POST['addMoreBlogDescriptions']) ) {
                $index = (int)$_POST['addMoreBlogDescriptions'];
                $widgets = new WIDGETS();
                echo $widgets->addBlogDesctiptionDiv($index);
            }

            if ( isset($_POST['addMoread1Descriptions']) ) {
                $index = (int)$_POST['addMoread1Descriptions'];
                $widgets = new WIDGETS();
                echo $widgets->addad1DesctiptionDiv($index);
            }

            
            if ( isset($_POST['addMoread2Descriptions']) ) {
                $index = (int)$_POST['addMoread2Descriptions'];
                $widgets = new WIDGETS();
                echo $widgets->addad2DesctiptionDiv($index);
            }
            
            if ( isset($_POST['addMorepdfDescriptions']) ) {
                $index = (int)$_POST['addMorepdfDescriptions'];
                $widgets = new WIDGETS();
                echo $widgets->addpdfDesctiptionDiv($index);
            }

            
            if ( isset($_POST['addMorehomeworkDescriptions']) ) {
                $index = (int)$_POST['addMorehomeworkDescriptions'];
                $widgets = new WIDGETS();
                echo $widgets->addhomeworkDesctiptionDiv($index);
            }

            if ( isset($_POST['addMorebooksDescriptions']) ) {
                $index = (int)$_POST['addMorebooksDescriptions'];
                $widgets = new WIDGETS();
                echo $widgets->addbooksDesctiptionDiv($index);
            }
            
            
            if ( isset($_POST['removeBlogDescImage']) && isset($_POST['removeBlogDescImageNo']) ) {
                $blogDescID = (int)$_POST['removeBlogDescImage'];
                $blogDescImgNo = (int)$_POST['removeBlogDescImageNo'];
                var_dump($_SESSION['sessionBlogDescImgArr']);
                if ( isset($_SESSION['sessionBlogDescImgArr']) && count($_SESSION['sessionBlogDescImgArr']) >= $blogDescID ) {
                    if ( $blogDescImgNo == 1 && $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][1]!="" ) $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][2] = -1;
                    if ( $blogDescImgNo == 2 && $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][3]!="" ) $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][4] = -1;
                }
                $blogDescImgNo = ($blogDescImgNo == 1) ? "One" : "Two";
                $blogDescID = $blogDescImgNo . $blogDescID;
                echo "
                    <script>
                        $('input[name=inputBlogImage$blogDescID]').val('');
                        $('#outputBlogImage$blogDescID').removeAttr('src');
                    </script>
                ";
            }

                        
            if ( isset($_POST['removead1DescImage']) && isset($_POST['removead1DescImageNo']) ) {
                $ad1DescID = (int)$_POST['removead1DescImage'];
                $ad1DescImgNo = (int)$_POST['removead1DescImageNo'];
                var_dump($_SESSION['sessionad1DescImgArr']);
                if ( isset($_SESSION['sessionad1DescImgArr']) && count($_SESSION['sessionad1DescImgArr']) >= $ad1DescID ) {
                    if ( $ad1DescImgNo == 1 && $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][1]!="" ) $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][2] = -1;
                    if ( $ad1DescImgNo == 2 && $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][3]!="" ) $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][4] = -1;
                }
                $ad1DescImgNo = ($ad1DescImgNo == 1) ? "One" : "Two";
                $ad1DescID = $ad1DescImgNo . $ad1DescID;
                echo "
                    <script>
                        $('input[name=inputad1Image$ad1DescID]').val('');
                        $('#outputad1Image$ad1DescID').removeAttr('src');
                    </script>
                ";
            }

                     
            if ( isset($_POST['removead2DescImage']) && isset($_POST['removead2DescImageNo']) ) {
                $ad2DescID = (int)$_POST['removead2DescImage'];
                $ad2DescImgNo = (int)$_POST['removead2DescImageNo'];
                var_dump($_SESSION['sessionad2DescImgArr']);
                if ( isset($_SESSION['sessionad2DescImgArr']) && count($_SESSION['sessionad2DescImgArr']) >= $ad2DescID ) {
                    if ( $ad2DescImgNo == 1 && $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][1]!="" ) $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][2] = -1;
                    if ( $ad2DescImgNo == 2 && $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][3]!="" ) $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][4] = -1;
                }
                $ad2DescImgNo = ($ad2DescImgNo == 1) ? "One" : "Two";
                $ad2DescID = $ad2DescImgNo . $ad2DescID;
                echo "
                    <script>
                        $('input[name=inputad2Image$ad2DescID]').val('');
                        $('#outputad2Image$ad2DescID').removeAttr('src');
                    </script>
                ";
            }
            
            if ( isset($_POST['removepdfDescImage']) && isset($_POST['removepdfDescImageNo']) ) {
                $pdfDescID = (int)$_POST['removepdfDescImage'];
                $pdfDescImgNo = (int)$_POST['removepdfDescImageNo'];
                var_dump($_SESSION['sessionpdfDescImgArr']);
                if ( isset($_SESSION['sessionpdfDescImgArr']) && count($_SESSION['sessionpdfDescImgArr']) >= $pdfDescID ) {
                    if ( $pdfDescImgNo == 1 && $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][1]!="" ) $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][2] = -1;
                    if ( $pdfDescImgNo == 2 && $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][3]!="" ) $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][4] = -1;
                }
                $pdfDescImgNo = ($pdfDescImgNo == 1) ? "One" : "Two";
                $pdfDescID = $pdfDescImgNo . $pdfDescID;
                echo "
                    <script>
                        $('input[name=inputpdfImage$pdfDescID]').val('');
                        $('#outputpdfImage$pdfDescID').removeAttr('src');
                    </script>
                ";
            }

            
            if ( isset($_POST['removehomeworkDescImage']) && isset($_POST['removehomeworkDescImageNo']) ) {
                $homeworkDescID = (int)$_POST['removehomeworkDescImage'];
                $homeworkDescImgNo = (int)$_POST['removehomeworkDescImageNo'];
                var_dump($_SESSION['sessionhomeworkDescImgArr']);
                if ( isset($_SESSION['sessionhomeworkDescImgArr']) && count($_SESSION['sessionhomeworkDescImgArr']) >= $homeworkDescID ) {
                    if ( $homeworkDescImgNo == 1 && $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][1]!="" ) $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][2] = -1;
                    if ( $homeworkDescImgNo == 2 && $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][3]!="" ) $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][4] = -1;
                }
                $homeworkDescImgNo = ($homeworkDescImgNo == 1) ? "One" : "Two";
                $homeworkDescID = $homeworkDescImgNo . $homeworkDescID;
                echo "
                    <script>
                        $('input[name=inputhomeworkImage$homeworkDescID]').val('');
                        $('#outputhomeworkImage$homeworkDescID').removeAttr('src');
                    </script>
                ";
            }

                        
            if ( isset($_POST['removebooksDescImage']) && isset($_POST['removebooksDescImageNo']) ) {
                $booksDescID = (int)$_POST['removebooksDescImage'];
                $booksDescImgNo = (int)$_POST['removebooksDescImageNo'];
                var_dump($_SESSION['sessionbooksDescImgArr']);
                if ( isset($_SESSION['sessionbooksDescImgArr']) && count($_SESSION['sessionbooksDescImgArr']) >= $booksDescID ) {
                    if ( $booksDescImgNo == 1 && $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][1]!="" ) $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][2] = -1;
                    if ( $booksDescImgNo == 2 && $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][3]!="" ) $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][4] = -1;
                }
                $booksDescImgNo = ($booksDescImgNo == 1) ? "One" : "Two";
                $booksDescID = $booksDescImgNo . $booksDescID;
                echo "
                    <script>
                        $('input[name=inputbooksImage$booksDescID]').val('');
                        $('#outputbooksImage$booksDescID').removeAttr('src');
                    </script>
                ";
            }


            if ( isset($_POST['changeBlogDescImage']) && isset($_POST['changeBlogDescImageNo']) ) {
                $blogDescID = (int)$_POST['changeBlogDescImage'];
                $blogDescImgNo = (int)$_POST['changeBlogDescImageNo'];
                if ( isset($_SESSION['sessionBlogDescImgArr']) && count($_SESSION['sessionBlogDescImgArr']) >= $blogDescID ) {
                    if ( $blogDescImgNo == "One" && $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][1]!="" ) $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][2] = -1;
                    if ( $blogDescImgNo == "Two" && $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][3]!="" ) $_SESSION['sessionBlogDescImgArr'][$blogDescID-1][4] = -1;
                }
            }
                        
            if ( isset($_POST['changead1DescImage']) && isset($_POST['changead1DescImageNo']) ) {
                $ad1DescID = (int)$_POST['changead1DescImage'];
                $ad1DescImgNo = (int)$_POST['changead1DescImageNo'];
                if ( isset($_SESSION['sessionad1DescImgArr']) && count($_SESSION['sessionad1DescImgArr']) >= $ad1DescID ) {
                    if ( $ad1DescImgNo == "One" && $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][1]!="" ) $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][2] = -1;
                    if ( $ad1DescImgNo == "Two" && $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][3]!="" ) $_SESSION['sessionad1DescImgArr'][$ad1DescID-1][4] = -1;
                }
            }
                        
            if ( isset($_POST['changead2DescImage']) && isset($_POST['changead2DescImageNo']) ) {
                $ad2DescID = (int)$_POST['changead2DescImage'];
                $ad2DescImgNo = (int)$_POST['changead2DescImageNo'];
                if ( isset($_SESSION['sessionad2DescImgArr']) && count($_SESSION['sessionad2DescImgArr']) >= $ad2DescID ) {
                    if ( $ad2DescImgNo == "One" && $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][1]!="" ) $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][2] = -1;
                    if ( $ad2DescImgNo == "Two" && $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][3]!="" ) $_SESSION['sessionad2DescImgArr'][$ad2DescID-1][4] = -1;
                }
            }

            if ( isset($_POST['changepdfDescImage']) && isset($_POST['changepdfDescImageNo']) ) {
                $pdfDescID = (int)$_POST['changepdfDescImage'];
                $pdfDescImgNo = (int)$_POST['changepdfDescImageNo'];
                if ( isset($_SESSION['sessionpdfDescImgArr']) && count($_SESSION['sessionpdfDescImgArr']) >= $pdfDescID ) {
                    if ( $pdfDescImgNo == "One" && $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][1]!="" ) $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][2] = -1;
                    if ( $pdfDescImgNo == "Two" && $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][3]!="" ) $_SESSION['sessionpdfDescImgArr'][$pdfDescID-1][4] = -1;
                }
            }

            
            if ( isset($_POST['changehomeworkDescImage']) && isset($_POST['changehomeworkDescImageNo']) ) {
                $homeworkDescID = (int)$_POST['changehomeworkDescImage'];
                $homeworkDescImgNo = (int)$_POST['changehomeworkDescImageNo'];
                if ( isset($_SESSION['sessionhomeworkDescImgArr']) && count($_SESSION['sessionhomeworkDescImgArr']) >= $homeworkDescID ) {
                    if ( $homeworkDescImgNo == "One" && $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][1]!="" ) $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][2] = -1;
                    if ( $homeworkDescImgNo == "Two" && $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][3]!="" ) $_SESSION['sessionhomeworkDescImgArr'][$homeworkDescID-1][4] = -1;
                }
            }

                        
            if ( isset($_POST['changebooksDescImage']) && isset($_POST['changebooksDescImageNo']) ) {
                $booksDescID = (int)$_POST['changebooksDescImage'];
                $booksDescImgNo = (int)$_POST['changebooksDescImageNo'];
                if ( isset($_SESSION['sessionbooksDescImgArr']) && count($_SESSION['sessionbooksDescImgArr']) >= $booksDescID ) {
                    if ( $booksDescImgNo == "One" && $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][1]!="" ) $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][2] = -1;
                    if ( $booksDescImgNo == "Two" && $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][3]!="" ) $_SESSION['sessionbooksDescImgArr'][$booksDescID-1][4] = -1;
                }
            }


            if ( isset($_POST['checkTouristUsername']) && isset($_POST['username']) ) {
                $touristID = (int)$_POST['checkTouristUsername'];
                $touristUsername = htmlspecialchars($_POST['username']);
                $rowCount = 0;
                if ( $touristID == 0 ) {
                    $rowCount = $user->CountRows("tourists", array("username"=>$touristUsername));
                } else {
                    $currentUsername = $user->fetchAll(array("username"), array("tourists"), array("id"=>$touristID))[0]['username'];
                    if ( $currentUsername == $touristUsername ) {
                        $rowCount = 0;
                    } else {
                        $rowCount = $user->CountRows("tourists", array("username"=>$touristUsername));
                    }
                }
                if ( $rowCount > 0 ) {
                    echo "<script>$('#usernameAlreadyTakenErr').text('This username is already taken');usernameStatus=false;enableDisableButton();</script>";
                } else {
                    echo "<script>$('#usernameAlreadyTakenErr').text('');usernameStatus=true;enableDisableButton();</script>";
                }
            }

            if ( isset($_POST['chngTouristSts']) ) {
                $touristArr = $_POST['chngTouristSts'];
                $user->updateTable("tourists", array("status"=>(int)$touristArr['touristStatus']), array("id"=>(int)$touristArr['touristID']));
            }

            if ( isset($_POST['chngBlogSts']) ) {
                $blogArr = $_POST['chngBlogSts'];
                $user->updateTable("blog_details", array("status"=>(int)$blogArr['blogStatus']), array("id"=>(int)$blogArr['blogID']));
            }

            
            if ( isset($_POST['chngad1Sts']) ) {
                $ad1Arr = $_POST['chngad1Sts'];
                $user->updateTable("ad1_details", array("status"=>(int)$ad1Arr['ad1Status']), array("id"=>(int)$ad1Arr['ad1ID']));
            }

            
            if ( isset($_POST['chngad2Sts']) ) {
                $ad2Arr = $_POST['chngad2Sts'];
                $user->updateTable("ad2_details", array("status"=>(int)$ad2Arr['ad2Status']), array("id"=>(int)$ad2Arr['ad2ID']));
            }

            
            if ( isset($_POST['chngpdfSts']) ) {
                $pdfArr = $_POST['chngpdfSts'];
                $user->updateTable("pdf_details", array("status"=>(int)$pdfArr['pdfStatus']), array("id"=>(int)$pdfArr['pdfID']));
            }

            
            if ( isset($_POST['chnghomeworkSts']) ) {
                $homeworkArr = $_POST['chnghomeworkSts'];
                $user->updateTable("homework_details", array("status"=>(int)$homeworkArr['homeworkStatus']), array("id"=>(int)$homeworkArr['homeworkID']));
            }

                        
            if ( isset($_POST['chngbooksSts']) ) {
                $booksArr = $_POST['chngbooksSts'];
                $user->updateTable("books_details", array("status"=>(int)$booksArr['booksStatus']), array("id"=>(int)$booksArr['booksID']));
            }

            if ( isset($_POST['showTestimonial']) ) {
                $testimonialID = (int)$_POST['showTestimonial'];
                foreach ( $user->fetchAll(array("id","user_id","ratings","one_word","review","status"), array("testimonials"), array("id"=>$testimonialID)) as $testimonialArr ) {
                    echo $widgets->displayTestimonial(array_merge($testimonialArr, $user->fetchAll(array("name","profile_pic","country"), array("tourists"), array("id"=>$testimonialArr['user_id']))[0]),$user,true);
                }
                $testimonialStatus = $testimonialArr['status'];
                $testimonialStatusArr = array("1"=>"Approve", "0"=>"-", "-1"=>"Reject");
                echo "
                    <div class='row'>
                        <div class='col-12'>
                            <div class='card'>
                                <div class='card-body'>
                                    <form method='post'>
                                        <div class='row'>
                                            <div class='col-sm-6'>
                                                Testimonial Status
                                                <select class='form-control' name='testimonialStatus' required>";
                                                    for ( $i=-1; $i<=1; $i++ ) {
                                                        if ( $i == $testimonialStatus ) {
                                                            echo "<option value='$i' selected>".$testimonialStatusArr[$i]."</option>";
                                                        } else {
                                                            echo "<option value='$i'>".$testimonialStatusArr[$i]."</option>";
                                                        }
                                                    }
                    echo "                      <option value='delete'>Delete</option>
                                                </select>
                                            </div>
                                            <div class='col-sm-6 pt-4'>
                                                <input type='hidden' name='testimonialID' value='$testimonialID'>
                                                <input type='submit' class='btn btn-primary btn-sm' name='changeTestimonialStatusSubmit' value='Save Changes'>
                                                <input type='submit' class='btn btn-secondary btn-sm' onclick='location.reload()' value='Cancel'>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
            }

        } else {
            $user->doLogout();
        }
    } else {
        $user->doLogout();
    }