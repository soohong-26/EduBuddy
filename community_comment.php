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
$uid = $_SESSION['user_id'];

// Deleting comment function
if(isset($_POST['delete'])){
    // Post and comment id
    $pid = $_POST['pid'];
    $cid = $_POST['cid'];

    $sql = "DELETE FROM comments WHERE comment_id='$cid'";

    if(mysqli_query($conn, $sql)){
        echo "<script>
                alert('Successfully delete comment!');
                window.location.href= 'community_comment.php?id=".$pid."';
              </script>";
    } else {
        echo "<script>
                alert('Failed to delete comment!');
                window.location.href= 'community_comment.php?id=".$pid."';
              </script>";
    }

}

// Adding a comment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = mysqli_real_escape_string($conn, $_POST['pid']);
    $userid = mysqli_real_escape_string($conn, $_POST['uid']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    date_default_timezone_set("Asia/Kuala_Lumpur");
    $current = date('Y-m-d H:i:s');     

    $sql = "INSERT INTO comments (post_id, user_id, message, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $post_id, $userid, $comment, $current);
   
    // Once if the comment has been posted successfully it will prompt a message
    if ($stmt->execute()) {
        echo "<script>
                    alert('Successfully add new comment!');
                    window.location.href = 'community_comment.php?id=".$post_id."';
                </script>";
        exit;
    } else {
        // Display SQL errors if any
        echo "SQL Error: " . $stmt->error; 
    }
    $stmt->close();
}

    // Getting the posts
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $sql = "SELECT * FROM posts WHERE post_id = '$id'";
        $result = mysqli_query($conn, $sql);
        $rows = mysqli_fetch_assoc($result);
    } else {
        header('Location: community.php'); 
        exit;
    }


?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBuddy - Edit Post</title>
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
            padding: 20px;
            border-radius: 8px;
            margin: 0 25px 10px 25px;
        }

        label {
            margin: 10px 0 5px 0;
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        input[type="checkbox"], input[type="text"] {
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
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
            width: 350px;
            padding: 8px 8px 8px 8px;
            margin: 5px 0 5px 0;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
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
            justify-content: space-between;
        }
         
        .strength-column {
            flex: 1;
            padding: 10px; 
        }

        .title{
            font-size: 20px;
        }

        .subtitle{
            font-size: 16px;
        }

        .mb{
            margin-bottom: 30px;
        }

        .user-info{
            display: flex;
            justify-content: space-between;
        }

        .user-inner{
            display: flex;
            gap: 10px;
        }
        .user-info .profileimg{
            width: 40px;
            height: 40px;
            border-radius: 100%;
            object-fit: cover;
        }

        .user-info .information{
            display: block;
            font-size: 14px;
            color: #5c5b5b;
        }

        .username{
            font-size: 16px;
            margin: 0;
            color: #000000;
            font-weight: bold;
        }

        .time{
            font-size: 14px;
            font-weight: 500;
            margin: 0;
        }

        .user-messagebox{
            margin-top: 12px;
            padding: 10px;
            border-radius: 6px;
            background-color: #f5f5f5;
        }

        .user-messagebox p{
            margin: 10px;
            font-size: 12px;
        }

        .comment-box{
            margin-top: 24px;
        }

        .action-info{
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
        }

        .action-info img{
            width: 25px;
            cursor: pointer;
        }

        .action-info button, .action-info button:focus, .action-info button:hover{
            background-color: transparent;
            outline: none;
            border: none;
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'header.php'; ?>

    <!-- Navigation Buttons -->
    <div class="button-container">
        <a href="community.php" class="toggle-button">Back</a>
    </div>

    <div class="skills-form">
        <!-- Post title -->
        <h1 class="title"><?php echo htmlspecialchars(stripslashes($rows['post_title']), ENT_QUOTES, 'UTF-8'); ?></h1>
        <!-- Post short description -->
        <p><?php echo htmlspecialchars(stripslashes($rows['description']), ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

    <div class="skills-form mb">
        <h2 class="subtitle">All Comments</h2>
        <?php
            $sql = "SELECT c.comment_id, c.message, c.created_at, u.user_id, u.username, u.email, u.profile_img from comments as c INNER JOIN users as u ON c.user_id = u.user_id WHERE c.post_id = '$id'";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0){

                while($rows = mysqli_fetch_assoc($result)){
                    $usid = $rows['user_id'];
                    $comment_id = $rows['comment_id'];
        ?>

                    <div class="comment-box">

                        <div class="user-info">
                            <div class="user-inner">
                            <?php 
                                // Check if the profile image exists in the database; use a default if empty
                                $profileImagePath = !empty($rows['profile_img']) ? $rows['profile_img'] : "images/profile.png";
                            ?>
                                <img src="<?php echo htmlspecialchars($profileImagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Image" class="profileimg">

                                <div class="information">
                                    <!-- Author's username -->
                                    <p class="username"><?php echo $rows['username']; ?></p>
                                    <!-- Metadata of the post -->
                                    <label class="time"> <?php
                                        $created_at = new DateTime($rows['created_at']);
                                        echo $created_at->format('F j, Y, g:i a'); // Example format: "March 10, 2024, 5:16 pm"
                                        ?>
                                    </label>
                                </div>
                            </div>
                            <?php if($uid == $usid){ ?>

                            <div class="action-info">
                                <!-- Edit comment part -->
                                <a href="community_comment_edit.php?pid=<?php echo $id;?>&cid=<?php echo $comment_id; ?>">
                                    <img src="icons/edit.png" />
                                </a>
                                <!-- Delete comment part -->
                                <form method="POST" action="community_comment.php" onsubmit="javascript:return confirm('Confirm to delete comment?')">
                                 <input value="<?php echo $id; ?>" name="pid" type="hidden" />
                                 <input value="<?php echo $comment_id; ?>" name="cid" type="hidden" />
                                 <button type="submit" name="delete" class="btn-delete"><img src="icons/delete.png" /></button>
                                </form>
                            </div>

                            <?php } ?>

                        </div>

                        <div class="user-messagebox">
                            <p><?php echo htmlspecialchars(stripslashes($rows['message']), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        
                    </div>

        <?php
                }

            }else{
                echo '<p>No Comment Yet.</p>';
            }

        ?>
    </div>

    <!-- Title -->
    <h2 class="title-page">Leave Your Comment.</h2>
    <form action="" method="POST" class="skills-form">
        <input type="hidden" value="<?php echo $uid; ?>" name="uid" />
        <input type="hidden" value="<?php echo $id; ?>" name="pid" />
        <!-- Comment part -->
        <div>
            <label>Comment:</label>
            <textarea rows="8" type="text" name="comment" placeholder="Type your comment" class="extra-skills-placeholder" required></textarea>
        </div>
       <!-- Submit button -->
        <button type="submit" class="extra-skills-button">Submit</button>
    </form>
</body>
</html>
