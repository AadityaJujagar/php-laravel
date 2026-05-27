<?php

// declaring variables
$fName = 'Aaditya';
$roi = 12.4;
$balance = 28866;
$isDecent = true;
$data = null;

// functions
function calculateInterest(float $principal, float $roi, int $months): float
{
    return ($principal * $roi * ($months/12))/100;
}
echo calculateInterest($balance, $roi, 24);

// strings
$greetings = "\nHello, " . "{$fName}. How u doing?\n";
echo $greetings;

// IMP: Arrays (Associative*)
$roles = [
    'SDE', 
    'Software Developer', 
    'Software Engineer', 
    'Backend Developer',
    ];
echo 'Current role: ' . $roles[2] . PHP_EOL; // next line in terminal
    
// Associative Arrays
$myself = [
    'name' => 'Aaditya Jujagar',
    'email' => 'aaditya.jujagar@company.com',
    'role' => 'SDE-1',
    // nested arrays
    'companyType' => [
        '1st' => 'Prominant PBCs', 
        '2nd' => 'FinTechs only',
        '3rd' => 'Prominent MNCs', 
        ]
];
echo 'Future role: ' . $myself['role'] . ' at a ' . $myself['companyType']['1st'] . PHP_EOL;

// imp array functions
$nums = [1,22,333,4444];
$total = array_sum($nums);
$filtered = array_filter($nums, fn($a)=> $a % 2 === 0);
$mapped = array_map(fn($b)=> $b * 1.2, $nums);

echo $total + array_sum($filtered) . PHP_EOL;
echo json_encode($mapped) . PHP_EOL;

?>