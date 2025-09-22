<?php
$original_path = '../uploads/course_video/68d160c2514f3_1758552258.mp4';
$fixed_path = 'uploads/course_video/68d160c2514f3_1758552258.mp4';

echo "Original path: $original_path\n";
echo "Fixed path: $fixed_path\n";
echo "Original exists: " . (file_exists($original_path) ? 'YES' : 'NO') . "\n";
echo "Fixed exists: " . (file_exists($fixed_path) ? 'YES' : 'NO') . "\n";

// Test different path variations
$paths_to_test = [
    '../uploads/course_video/68d160c2514f3_1758552258.mp4',
    'uploads/course_video/68d160c2514f3_1758552258.mp4',
    './uploads/course_video/68d160c2514f3_1758552258.mp4',
    'C:\\xampp2\\htdocs\\SkillSpark\\uploads\\course_video\\68d160c2514f3_1758552258.mp4'
];

echo "\nTesting different path variations:\n";
foreach($paths_to_test as $path) {
    echo "Path: $path\n";
    echo "Exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    echo "---\n";
}
?>
