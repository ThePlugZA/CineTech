<?php
session_start(); // Start session to store user login status

// Function to make API request
function makeApiRequest($data)
{
    // Create a new cURL resource
    $ch = curl_init();

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, 'https://wheatley.cs.up.ac.za/u23535246/CINETECH/api.php');

    // Set the request method to POST
    curl_setopt($ch, CURLOPT_POST, 1);

    // Set the request data as JSON
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Set the Content-Type header
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Set basic authentication credentials
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'u23535246:Toponepercent120'); // Replace with your actual credentials

    // Return response instead of outputting it
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Close cURL resource
    curl_close($ch);

    // Decode the JSON response
    return json_decode($response, true);
}

// Function to handle adding to favorites
function addToFavorites($apiKey, $filmId, $showId)
{
    // Check if API key is available
    if (!$apiKey) {
        // Redirect to login page if API key is not available
        header("Location:../php/login.php");
        exit();
    }

    // Check if the add button is clicked
    $add = isset($_POST['addToFavorites']) ? "true" : "false";

    echo '<script>console.log("addToFavorites called with API key: ' . $apiKey . '");</script>';
    echo '<script>console.log("filmId: ' . $filmId . '");</script>';
    echo '<script>console.log("showId: ' . $showId . '");</script>';

    if (isset($_GET['name'])) {
        $data = array(
            "type" => "Favourite",
            "apikey" => $apiKey,
            "add" => $add,
            "show_id" => $showId
        );
        // echo '<script>alert("i am a show : ' . $responseData['error'] . '");</script>';
    } else {
        $data = array(
            "type" => "Favourite",
            "apikey" => $apiKey,
            "add" => $add,
            "film_id" => $filmId
        );
        echo '<script>alert("i am a film: ' . $data['film_id'] . '");</script>';
    }
    // Prepare data for adding to favorites


    // Make API request
    $responseData = makeApiRequest($data);
    var_dump($responseData); // Add this line

    // Check if the request was successful
    if ($responseData['status'] === 'success') {
        // Redirect to favourites.php after successfully adding to favorites
        header("Location: ../html/favourites.php");
        exit();
    } else {
        // Failed to add to favorites
        //  echo '<script>alert("Failed to add to My List: ' . $responseData['error'] . '");</script>';
    }
}

// Check if the user is not logged in, redirect to login page
$apiKey = isset($_SESSION['apikey']) ? $_SESSION['apikey'] : null;

// Prepare data for JSON request
if (isset($_GET['name'])) {
    $name = urldecode($_GET['name']);
    $data = array(
        "type" => "GetAllSeries",
        "limit" => 1,
        "search" => array(
            "Name" => $name
        ),
        "return" => "all"
    );
} else {
    $title = urldecode($_GET['title']);
    $data = array(
        "type" => "GetAllMovies",
        "limit" => 1,
        "search" => array(
            "Title" => $title
        ),
        "return" => "all"
    );
}

// Make API request
$responseData = makeApiRequest($data);

// Check if the request was successful
if ($responseData['status'] === 'success') {
    // Process the data
    $movies = $responseData['data'][0];
    // echo '<script>console.log("Movies data: ' . json_encode($movies) . '");</script>';
} else {
    // Handle error response
    $error = $responseData['data'];
}

// Handle adding to favorites if form is submitted
if (isset($_POST['addToFavorites'])) {

    //echo '<script>alert("i am clicked: ' . $responseData['error'] . '");</script>';

    if (isset($_GET['name'])) {
        addToFavorites($apiKey, null, $movies["ID"]);
        // echo '<script>alert("i am a show: ' . $responseData['error'] . '");</script>';
    } else {
        addToFavorites($apiKey, $movies["ID"], null);
        // echo '<script>alert("i am movie: ' . $responseData['error'] . '");</script>';
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/viewMore.css" id="light-mode">
    <link rel="icon" href="../img/4.png" type="image/x-icon">
    <!-- the icons used in the website -->
    <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <title>CineTech</title>
</head>

<body>
    <!--Header-->
    <header>
        <!-- convert this image to a webm so it actually plays  -->
        <nav>
            <div class="logo_ul">
                <img src="../img/4.png" alt="">
                <ul>
                    <li>
                        <a href="../php/homePage.php">Home</a>
                    </li>
                    <li>
                        <a href="../php/movies.php">Movies</a>
                    </li>
                    <li>
                        <a href="../php/series.php">Series</a>
                    </li>
                    <li>
                        <a href="../php/recAdded.php">Recently Added</a>
                    </li>
                    <li>
                        <a href="../php/favourites.php">My List</a>
                    </li>
                </ul>
            </div>
            <!-- <div class="search_user">
                <input type="text" placeholder="Search..." id="search_input">
                <img src="../img/UserPFP.jpeg" alt="">
            </div> -->
        </nav>

        <div class="ViewDetails">
            <div class="content">
                <div class="content-image">
                    <img class="movieImg" src="<?php echo $movies['PosterURL'] ?>" alt="">
                </div>
            </div>

            <div class="content-details">
                <!-- Create a div for the description as well as the other-->
                <div class="description">
                    <h2>Description: </h2>
                    <h3><?php echo $movies['Description'] ?></h3><br>
                </div>

                <div class="Genres">
                    <h3>Genre: <?php echo $movies['Genre'] ?> </h3><br>
                </div>

                <div class="yearRelease">
                    <h3>Year Of Release: <?php echo $movies['Release_Year'] ?></h3><br>
                </div>

                <div class="movieRating">
                    <h3>CineTech Rating: <?php echo $movies['CineTech_Rating'] ?></h3><br>
                </div>

                <div class="userRating">
                    <h3>Ratings: </h3>
                    <div class="star-icon">
                        <a href="#" class="bi-star-fill"></a>
                        <a href="#" class="bi-star-fill"></a>
                        <a href="#" class="bi-star-fill"></a>
                        <a href="#" class="bi-star-fill"></a>
                        <a href="#" class="bi-star-fill"></a>
                    </div>
                </div>

                <div class="actors">
                    <h3>Actors: Gabrielle Union, Mark June and Tyler Perry </h3><br>
                </div>

                <button class="trailer">
                    <a href=" <?php echo ' ' ?> ">Trailer</a><br>
                </button>

                <button class="btn" id="shareButton">Share</button>

                <!-- The Modal -->
                <div id="myModal" class="modal">

                    <!-- Modal content -->
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <p>Send to User:</p>
                        <input type="text" id="username" placeholder="Enter username">
                        <button class="btn" id="shareButton2">Share</button>
                    </div>

                </div>

                <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET); ?>">
                    <button type="submit" class="watchList" name="addToFavorites">Add to MyList</button>
                </form>

            </div>
        </div>

        <section>
            <h4>People Also Like</h4>
            <div class="cards">
                <!-- add more of these to check scroll featur -->
                <a href="#" class="card">
                    <img src="../img/JohnWick.jpeg" alt="" class="poster">
                    <div class="rest_card">
                        <img src="../img/JohnWickVisual.jpeg" alt="">
                        <div class="cont">
                            <h4>John Wick</h4>
                            <div class="sub">
                                <p>Action, 2024</p>
                                <h3><span>CineTech</span><i class="fa fa-star" aria-hidden="true"></i>9.6</h3>
                            </div>
                        </div>
                    </div>
                </a>

            </div>

            <!-- <script src="../js/homePage.js"></script> -->

            <!--This is to make the buttons of the stars to work -->
        </section>
    </header>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var stars = document.querySelectorAll(".star-icon a");
            stars.forEach((item, index1) => {
                item.addEventListener("click", (event) => {
                    event.preventDefault(); // Prevent default anchor behavior
                    stars.forEach((star, index2) => {
                        index1 >= index2 ? star.classList.add("active") : star.classList.remove("active");
                    });
                });
            });
        });

        //  this is the part for the popup
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("shareButton");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on the button, open the modal and blur the main content
        btn.onclick = function() {
          modal.style.display = "block";
          mainContent.classList.add("blurred");
        }

        // When the user clicks on <span> (x), close the modal and remove the blur
        span.onclick = function() {
          modal.style.display = "none";
          mainContent.classList.remove("blurred");
        }

        // When the user clicks anywhere outside of the modal, close it and remove the blur
        window.onclick = function(event) {
          if (event.target == modal) {
            modal.style.display = "none";
            mainContent.classList.remove("blurred");
          }
        }
    </script>

</body>

</html>