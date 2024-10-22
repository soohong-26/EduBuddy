# EduBuddy: Connecting Peers, Tutors and Mentors

The EduBuddy Website is a website that allows students to work together and study together on assignments or projects, while helping each other out during the process.

By doing so it allows a mutual support among each other.

# File Structure
## Login & Register
- login.php
- register.php
- login-register.php

## Home Page
- home_buddies.php
- home_mentors.php

## Utility
- database.php
- header.php (It will switch between buddy and mentor)

## Finding Buddies
- profile.php
- submit_skills.php (Submitting skills for the system to find buddies)
- find_buddies.php (Finding matching buddies based on weaknesses)

## Community Forum
- community.php
- community_add.php
- community_edit.php
- community_comment.php
- community_comment_edit.php

## Mentor
- mentor_view.php
- students_profile.php

## Feature
### Changing Profile Pictures
- edit_profile.php
- edit_profile_picture.php

### Adding Buddies as Friends
- profile.php (To add friends, there is a button)
- profile_view_only.php (To view friends only)
- friend_list.php (Display current friends)
- adding_friend.php (Add friends)
- accept_requests.php (Manages accepting friend requests)
- decline_requests.php (Manages declining friend requests)
- pending_requests.php (Manages pending friend requests)

### Viewing Achievements
- profile.php (A button that will redirect to feedback_form.php)
- feedback_form.php (Frontend to send feedback form)
- submit_feedback.php (Backend to send feedback form)
- view_feedback.php (Viewing users feedback)

# Database
## Handling Users
- users

## Handling Buddies
- skills
- friends
- friend_requests

## Community Forum
- posts
- comments

## Achievement Feedback

# To-Do
- Achievement Page
    - Allows the user to add achievements to the other students
- In the page allows other students and mentors to come to that students profile and leave comments about their work