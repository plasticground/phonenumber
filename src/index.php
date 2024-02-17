<?php
require '../vendor/autoload.php';

use Brick\PhoneNumber\PhoneNumber;

$doc = file_get_contents('index.html');

$phone = getPhone();
$errors = [];
$result = [];

if ($phone) {
    try {
        $phoneNumber = PhoneNumber::parse($phone);

        $result = [
            '%is_valid_number%' => boolToText($phoneNumber->isValidNumber()),
            '%is_possible_number%' => boolToText($phoneNumber->isPossibleNumber()),
            '%country_code%' => emptyToText($phoneNumber->getCountryCode()),
            '%national_number%' => emptyToText($phoneNumber->getNationalNumber()),
            '%description%' => emptyToText($phoneNumber->getDescription('en')),
            '%region_code%' => emptyToText($phoneNumber->getRegionCode()),
            '%geographical_code%' => emptyToText($phoneNumber->getGeographicalAreaCode()),
        ];

        docRender($doc, $result);
    } catch (\Throwable $exception) {
        $errors = [
            '%invalid_format%' => 'The number could not be parsed, please check the correctness of the number'
        ];

        docRender($doc, $errors);
    }
}

$meta = [
    '%phone%' => $phone,
    '%error_display%' => isDisplay($errors),
    '%result_display%' => isDisplay($result),
];

docRender($doc, $meta);

echo $doc;


function getPhone(): string
{
    $phone = $_GET['phone'] ?? '';

    if ($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($phone && is_string($phone)) {
            $phone = "+{$phone}";
        } else {
            $phone = '';
        }
    }

    return $phone;
}

function boolToText($var): string
{
    return $var ? 'Yes' : 'No';
}

function emptyToText($var): string
{
    return $var ?: 'Unknown';
}

function isDisplay($condition): string
{
    return $condition ? 'block' : 'none';
}

function docRender(&$doc, array $vars)
{
     $doc = str_replace(array_keys($vars), array_values($vars), $doc);
}