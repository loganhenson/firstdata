<?php

try{
    Dotenv::load(__DIR__ . '/../');
}catch(InvalidArgumentException $e){
    echo "Travis env vars used";
}
