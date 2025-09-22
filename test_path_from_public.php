<?php
// Simulate being in the public directory
chdir('public');

$original_path = '../uploads/course_video/68d160c2514f3_1758552258.mp4';
$video_path = $original_path;

echo "Working directory: " . getcwd() . "\n";
echo "Original path: $original_path\n";
echo "Video path: $video_path\n";
echo "File exists: " . (file_exists($video_path) ? 'YES' : 'NO') . "\n";

if (file_exists($video_path)) {
    echo "File size: " . filesize($video_path) . " bytes\n";
    echo "MIME type: " . mime_content_type($video_path) . "\n";
}
?>
