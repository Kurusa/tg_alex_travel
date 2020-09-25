<?php
$arr = ['Київ', 'Львів', 'Київ'];


$buttons = [];
foreach ($arr as $item) {
    if (!in_array($item, $buttons)) {
        $buttons[] = $item;
    }
}

foreach ($buttons as $button) {
    $buttons[] = [$button];
}

print_r($buttons);