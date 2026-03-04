<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  $adminHeader = new HEADER("testimonials");
  $user = new USER();

  if ( $user->is_loggedin() ) {
    if ( $user->checkTimeout() ) {
      if ( isset($_POST['changeTestimonialStatusSubmit']) ) {
        $testimonialID = (int)$_POST['testimonialID'];
        $testimonialStatus = $_POST['testimonialStatus'];
        if ( $testimonialStatus != "delete" ) {
          $testimonialStatus = (int)$_POST['testimonialStatus'];
          $user->updateTable("testimonials", array("status"=>$testimonialStatus), array("id"=>$testimonialID));
          echo "<script>alert('Successfully changed the Testimonial Status'); location.href='./testimonials';</script>";
        } else {
          foreach ( $user->fetchAll(array("image"), array("testimonials_images"), array("testimonial_id"=>$testimonialID)) as $row ) {
            unlink("../img/testimonials/".$row['image']);
          }
          $user->deleteTableRow("testimonials_images", array("testimonial_id"=>$testimonialID));
          $user->deleteTableRow("testimonials", array("id"=>$testimonialID));
          echo "<script>alert('Successfully deleted the Testimonial'); location.href='./testimonials';</script>";
        }
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
    <?php echo $adminHeader->printAdminNav2($adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <h4>Review Testimonials</h4>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-bordered" id="reviewTestimonialsTable" data-order='[[ 5, "desc" ]]' width="100%" cellspacing="0">
                  <thead>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Ratings</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                  </thead>
                  <tfoot>
                    <th>Name</th>
                    <th>Country</th>
                    <th>Ratings</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                  </tfoot>
                  <tbody>
                    <?php
                      foreach ( $user->fetchAll(array("id", "user_id", "ratings", "one_word", "status","timestamp"), array("testimonials"), "") as $rowFetchTestimonials ) {
                        $testimonialID = $rowFetchTestimonials['id'];
                        $testimonialRatings = $rowFetchTestimonials['ratings'];
                        $testimonialOneWord = $rowFetchTestimonials['one_word'];
                        $testimonialStatus = $rowFetchTestimonials['status'];
                        $testimonialStatus = ($testimonialStatus==1) ? "Approved" : (($testimonialStatus==-1) ? "Rejected" : "-");
                        $testimonialTimestamp = $rowFetchTestimonials['timestamp'];
                        $touristArr = $user->fetchAll(array("name","country"), array("tourists"), array("id"=>$rowFetchTestimonials['user_id']))[0];
                        $touristName = $touristArr['name'];
                        $touristCountry = $touristArr['country'];
                        echo "
                          <tr>
                            <td class='cursor-pointer' onclick='showTestimonial($testimonialID)'>$touristName</td>
                            <td>$touristCountry</td>
                            <td>";
                              for ( $i=1; $i<=5; $i++ ) {
                                $starColor = ($i<=$testimonialRatings) ? "text-warning" : "";
                                echo "<span class='fa fa-star $starColor'></span>";
                              }
                        echo "
                            </td>
                            <td>$touristName>/td>
                            <td>$testimonialOneWord</td>
                            <td>$testimonialStatus</td>
                            <td>$testimonialTimestamp</td>
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
      <div id="showTestimonial"></div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
  </main>
  <?php echo $adminHeader->printAdminFooterJS(); ?>
  <script src="./assets/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
  <script src="./assets/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
  <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    $(document).ready(function(){
        $('#reviewTestimonialsTable').DataTable();
    });
    function showTestimonial(testimonialID) {
      $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
          showTestimonial: testimonialID
        },
        success: function(html) {
          $("#showTestimonial").html(html).show();
        }
      }); 
      $('html, body').animate({
        scrollTop: $("#showTestimonial").offset().top
      });
    }
  </script>
</body>

</html>