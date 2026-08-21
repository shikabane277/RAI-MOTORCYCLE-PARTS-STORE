<?php
// Test getting cities for Cavite (code 042100000) or NCR (code 130000000)
$ncrCitiesUrl = 'https://psgc.gitlab.io/api/regions/130000000/cities-municipalities.json';
$res = file_get_contents($ncrCitiesUrl);
$cities = json_decode($res, true);
echo "NCR Cities: " . count($cities) . " (e.g. " . $cities[0]['name'] . " code: " . $cities[0]['code'] . ")\n";

// Test getting barangays for Quezon City (code 137404000)
$qcBrgyUrl = 'https://psgc.gitlab.io/api/cities-municipalities/137404000/barangays.json';
$res2 = file_get_contents($qcBrgyUrl);
$brgys = json_decode($res2, true);
echo "Quezon City Barangays: " . count($brgys) . " (e.g. " . $brgys[0]['name'] . ")\n";
