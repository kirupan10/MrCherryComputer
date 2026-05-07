<?php
$permissionsLink = '
                                        <a class="dropdown-item" href="{{ shop_route(\'permissions.index\') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/><path d="M9 12l2 2l4 -4"/></svg>
                                            {{ __(\'Permissions\') }}
                                        </a>';

$shopTypes = ['automotive','electronics','food_city','general','pharmacy','restaurant','retail','salon','studio','tech'];
foreach ($shopTypes as $shopType) {
    $file = '/Users/kirupan/Force/NexoraLabs/resources/views/shop-types/'.$shopType.'/layouts/body/admin-navbar.blade.php';
    if (!file_exists($file)) { echo "Missing: $shopType\n"; continue; }
    $content = file_get_contents($file);
    if (strpos($content, 'permissions.index') !== false) { echo "Already done: $shopType\n"; continue; }
    $needle = "{{ __('Letterhead') }}";
    $pos = strrpos($content, $needle);
    if ($pos === false) { echo "Not found: $shopType\n"; continue; }
    $closeA = strpos($content, '</a>', $pos);
    if ($closeA === false) { echo "No close tag: $shopType\n"; continue; }
    $content = substr_replace($content, $permissionsLink, $closeA + 4, 0);
    file_put_contents($file, $content);
    echo "Updated: $shopType\n";
}
