<?php
$video_path = 'uploads/course_video/68d160c2514f3_1758552258.mp4';
echo "Testing video file: $video_path\n";
echo "File exists: " . (file_exists($video_path) ? 'YES' : 'NO') . "\n";
if(file_exists($video_path)) {
    echo "File size: " . filesize($video_path) . " bytes\n";
    echo "MIME type: " . mime_content_type($video_path) . "\n";
}

// Test the file_viewer.php URL
$encoded_path = urlencode($video_path);
echo "Encoded path: $encoded_path\n";
echo "File viewer URL: file_viewer.php?file=$encoded_path\n";
?>
