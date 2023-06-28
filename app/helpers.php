<?php

if(!function_exists('wa_text')){
    function wa_text($text) {
        $text = html_entity_decode($text);
        $text = preg_replace('/(<figure class="media"><oembed url=")|("><\/oembed><\/figure>)/', "\n", $text); // Google Media Embed
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

if(!function_exists('tgl_indo')){
    function tgl_indo($tanggal, $cetak_hari = false) {
        $hari = array ( 1 =>    'Senin',
				'Selasa',
				'Rabu',
				'Kamis',
				'Jumat',
				'Sabtu',
				'Minggu'
			);
			
	$bulan = array (1 =>   'Januari',
				'Februari',
				'Maret',
				'April',
				'Mei',
				'Juni',
				'Juli',
				'Agustus',
				'September',
				'Oktober',
				'November',
				'Desember'
			);
	$split 	  = explode('-', $tanggal);
	$tgl_indo = $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
	
	if ($cetak_hari) {
		$num = date('N', strtotime($tanggal));
		return $hari[$num] . ', ' . $tgl_indo;
	}
	return $tgl_indo;
    }
}