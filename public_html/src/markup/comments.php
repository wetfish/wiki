<?php

// An array of comments and their unique IDs
global $comments;
$comments = array();
$commentDoc = false;

// Function to find and comments
function remove_comments($input)
{
    $commentDoc = $input;
    $output = preg_replace_callback('/<!--.*?-->/s', "comment_found", $input);
    return $output;
}

// Function to replace comments with unique IDs
function comment_found($match)
{
    // Generate a unique ID
    $replacementID = uuid();

    // Ensure it is unique and doesn't exist in the document
    while($comments[$replacementID] || strpos($commentDoc, $replacementID) !== false)
    {
        $replacementID = uuid();
    }

    global $comments;
    $comments[$replacementID] = $match[0];
    return $replacementID;
}

// Function to put comments back after markup has been processed
function replace_comments($input)
{
    global $comments;
    $output = $input;
    
    // Loop through all comments
    foreach($comments as $id => $comment)
    {
        $output = str_replace($id, $comment, $output);
    }

    return $output;
}

?>
