<?php

$headers = "From: colin@diamondlighthouse.com\r\n";
$headers .= "Reply-To: colin@diamondlighthouse.com\r\n";
$headers .= "Return-Path: colin@diamondlighthouse.com\r\n";


if ( mail('glcolin@hotmail.com','Test','What is it?',$headers) ) {
   echo "The email has been sent!";
   } else {
   echo "The email has failed!";
   }