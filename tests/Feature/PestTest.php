<?php

it('will merge arrays', function() {

    $array1 = [
        'name' => 'Alice',
        'age' => 27,
        'country' => 'United Kingdom'
    ];

    $array2 = [
        'age' => 32
    ];

    $new_array = arraysMergeUnique($array1, $array2);

    expect($new_array)->toEqualCanonicalizing([
        'name' => 'Alice',
        'age' => 32,
        'country' => 'United Kingdom'
    ]);
});
