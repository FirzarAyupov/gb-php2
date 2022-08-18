<?php

function add($x, $y)
{
    return $x + $y;
}

if (add(2, 2) == 4) {
    echo "add OK";
} else {
    echo "error Add";
}