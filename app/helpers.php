<?php

if(!function_exists('wa_text')){
    function wa_text($text) {
        $text = html_entity_decode($text);
        $text = preg_replace('/<\/p>|<ul>|<\/ul>|<ol>|<\/ol>|<\/li>/', '', $text);

        $text = preg_replace('/(<p>)/', "\n\n", $text); 
        
        // Replace <b> tags with *
        $text = preg_replace('/<strong>(.*?)<\/strong>/', '*$1*', $text);
        
        // Replace <i> tags with _
        $text = preg_replace('/<i>(.*?)<\/i>/', '_$1_', $text);
        
        // Replace <u> tags with ~
        $text = preg_replace('/<u>(.*?)<\/u>/', '~$1~', $text);
        
        // Replace <li> tags with ~
        $text = preg_replace('/<li>/', "\n ~ ", $text);
        
        // Remove all other HTML tags
        $text = strip_tags($text);

        return $text;
    }
}