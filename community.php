<!-- PHP -->
<!-- PHP -->
<?php
// Include the database connection
require 'database.php'; 

if (!isset($_SESSION['username'])) {
    // Redirect if not logged in
    header('Location: login.php'); 
    exit;
}

// Fetch the logged-in user's username
$username = $_SESSION['username']; 
$userid = $_SESSION['user_id']; 

// Handle post deletion
if(isset($_POST['delete'])){
    $id = $_POST['pid'];

    $sql = "DELETE FROM posts WHERE post_id='$id'";

    if(mysqli_query($conn, $sql)){
        echo "<script>
                alert('Successfully deleted the post!');
                window.location.href= 'community.php';
              </script>";
    } else {
        echo "<script>
                alert('Failed to delete the post!');
                window.location.href= 'community.php';
              </script>";
    }
}

// Handle search functionality using GET method
$search_keyword = "";
if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $search_keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $sql = "SELECT * FROM posts WHERE post_title LIKE '%$search_keyword%'"; // Modify query to search posts
} else {
    $sql = "SELECT * FROM posts"; // Default query to display all posts
}

$result = mysqli_query($conn, $sql);
?>


<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Add Post</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            padding: 20px;
        }

        /* Title Header */
        .title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 20px 25px;
        }

        /* Form Submission */
        .skills-form {
            background: white;
            padding: 30px 30px;
            border-radius: 8px;
            margin: 0 25px 10px 25px;
            display: flex;
            cursor: pointer;
        }

        .skills-form .left-box{
            width: 60%;
        }
        .skills-form .right-box{
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: end;
        }
        .skills-form .right-box .inner{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .inner img{
            width: 25px;
        }

        

        input[type="checkbox"], input[type="text"] {
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgba(0, 136, 169, .8);
        }

        .sub-title {
            font-size: 18px;
        }

        /* Output Buddies */
        .sub-title-page {
            color: #7AA3CC;
            font-family: "Poppins", sans-serif;
            margin: 0 0 10px 0;
        }

        .buddies-list {
            margin: 20px 25px 10px 25px;
        }

        .buddies-list ul {
            list-style-type: none;
            padding: 0;
        }
        
        .buddies-list li {
            background: #fff;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .buddy-username {
            color: black;
            font-weight: bold;
            font-size: 20px;
        }

        .buddy-strength {
            color: black;
        }

        .buddy-weakness {
            color: black;
        }

        .buddy-none {
            color: black;
        }

        /* Extra Skills */
        .extra-skills-placeholder {
            width: 250px;
            padding: 12px 8px 12px 8px;
            margin: 5px 0 5px 0;
            border-radius: 5px;
            background: #ffffff;
            color: var(--text);
            font-size: 14px;
        }

        .extra-skills-button {
            width: 150px;
            padding: 7px;
            margin: 4px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-profile-button {
            width: 150px;
            padding: 10px;
            margin: 10px 0 10px 0;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* General Toggle Button Styling */
        .toggle-button {
            display: inline-block;
            width: 200px;
            padding: 10px;
            margin: 0 8px 0 0;
            background-color: rgba(0, 136, 169, 1);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .toggle-button:hover {
            background-color: rgba(0, 136, 169, .8);
        }

        .active-button {
            background-color: rgba(0, 136, 169, .5);
        }

        /* Button Container */
        .button-container {
            margin: 0 0 15px 25px;
        }

        /* Adding flexbox to the strength container */
        .strengths-container {
            display: flex;
            justify-content: space-between; /* Ensures spacing between columns */
        }
         
        .strength-column {
            flex: 1; /* Each column takes equal space */
            padding: 10px; /* Adds padding around the content of each column */
        }

        .bubble-num{
            display: flex;
            align-items: center;
            gap: 5px;
            color: #000000;
        }

        .title {
            margin: 10px 0 5px 0;
            display: block;
            font-weight: bold;
            font-size: 20px;
        }

        .time {
            margin: 10px 0 5px 0;
            display: block;
            font-size: 14px;
            color: #5c5b5b;
        }

        .shortdesc {
            margin: 10px 0 5px 0;
            display: block;
            font-size: 16px;
            color: #3e3e3e;
        }

        .time-row{
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time-row img{
            width: 15px;
            padding-top: 3px;
        }

        .btn-delete, .btn-delete:hover{
            background-color: transparent;
            padding: 0;
            border: none;
        }
        .btn-delete:focus{
            outline: none;
        }

        .top-header{
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .top-search{
            padding-right: 25px;
        }


    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

     <!-- Navigation Buttons -->
    <div class="button-container">
        <a href="community_add.php" class="toggle-button">Add Post</a>
    </div>

    <!-- Search Form -->
    <div class="top-header">
        <h2 class="title-page">All Posts</h2>
        <div class="top-search">
            <form method="GET" action="community.php">
                <input name="keyword" placeholder="Search Title..." class="extra-skills-placeholder" value="<?php echo htmlspecialchars($search_keyword); ?>" />
                <button type="submit" class="btn-search">Search</button>
            </form>
        </div>
    </div>

    <?php
    // Display search results or all posts
    if(mysqli_num_rows($result) > 0){
        while($rows = mysqli_fetch_assoc($result)){
            $post_id = $rows['post_id'];
            $uid = $rows['user_id'];
            $sqlP = "SELECT count(*) as total FROM comments WHERE post_id = '$post_id'";
            $resultP = mysqli_query($conn, $sqlP);
            $rowsP = mysqli_fetch_assoc($resultP);
    ?>
            <div class="skills-form" onclick="toDetail(<?php echo $post_id; ?>)">
                <div class="left-box">
                    <div>
                        <label class="title"><?php echo htmlspecialchars($rows['post_title'], ENT_QUOTES, 'UTF-8'); ?></label>
                    </div>
                    <div class="time-row">
                        <img src="icons/clock.png" />
                        <label class="time"> 
                            <?php
                            $created_at = new DateTime($rows['created_at']);
                            echo $created_at->format('F j, Y, g:i a'); // Example format: "March 10, 2024, 5:16 pm"
                            ?>
                        </label>
                    </div>
                    <div>
                        <label class="shortdesc"><?php echo htmlspecialchars($rows['short_desc'], ENT_QUOTES, 'UTF-8'); ?></label>
                    </div>
                </div>
                <div class="right-box">
                    <div class="inner">
                        <div class="bubble-num">
                            <img src="icons/speech-bubble.png" />
                            <span><?php echo $rowsP['total']; ?></span>
                        </div>
                        <?php if($uid == $userid){ ?>
                        <a href="community_edit.php?id=<?php echo $post_id;  ?>">
                            <img src="icons/edit.png" />
                        </a>
                        <form method="POST" action="community.php" onsubmit="return confirm('Confirm to delete post?')">
                            <input value="<?php echo  $post_id; ?>" name="pid" type="hidden" />
                            <button type="submit" name="delete" class="btn-delete"><img src="icons/delete.png" /></button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
    <?php 
        }
    } else {
    ?>
        <div class="skills-form">
            <span>No Posts Found</span>
        </div>
    <?php
    }
    ?>


    <script type="text/javascript">
        
        function toDetail(id){
            var url = `community_comment.php?id=${id}`;
            window.location.href = url;
        }

    </script>

</body>
</html>
