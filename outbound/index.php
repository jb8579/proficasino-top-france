<?php

	$jci_check = FALSE; 

	$rowOpts = [
        [
            'sp' => 'https://trk.proficasino.com/click/1?clickid={clickid}',
            'mp_1' => 'https://trk.proficasino.com/click/58?clickid={clickid}',
            'mp_2' => 'https://trk.proficasino.com/click/58?clickid={clickid}',
            'mp_3' => 'https://trk.proficasino.com/click/58?clickid={clickid}',
            'mp_4' => 'https://trk.proficasino.com/click/58?clickid={clickid}',
            'mp_5' => 'https://trk.proficasino.com/click/58?clickid={clickid}',
        ],
        [
            'sp' => 'https://trk.proficasino.com/click/2?clickid={clickid}',
            'mp_1' => 'https://trk.proficasino.com/click/54?clickid={clickid}',
            'mp_2' => 'https://trk.proficasino.com/click/54?clickid={clickid}',
            'mp_3' => 'https://trk.proficasino.com/click/54?clickid={clickid}',
            'mp_4' => 'https://trk.proficasino.com/click/54?clickid={clickid}',
            'mp_5' => 'https://trk.proficasino.com/click/54?clickid={clickid}',
        ],
        [
            'sp' => 'https://trk.proficasino.com/click/3?clickid={clickid}',
            'mp_1' => 'https://trk.proficasino.com/click/48?clickid={clickid}',
            'mp_2' => 'https://trk.proficasino.com/click/48?clickid={clickid}',
            'mp_3' => 'https://trk.proficasino.com/click/48?clickid={clickid}',
            'mp_4' => 'https://trk.proficasino.com/click/48?clickid={clickid}',
        ],
        [
            'sp' => 'https://trk.proficasino.com/click/4?clickid={clickid}',
            'mp_1' => 'https://trk.proficasino.com/click/57?clickid={clickid}',
            'mp_2' => 'https://trk.proficasino.com/click/62?clickid={clickid}',
        ],
        [
            'sp' => 'https://trk.proficasino.com/click/5?clickid={clickid}',
            'mp_1' => 'https://trk.proficasino.com/click/61?clickid={clickid}',
            'mp_2' => 'https://trk.proficasino.com/click/61?clickid={clickid}',
        ],
        [
            'sp' => 'https://trk.proficasino.com/click/6?clickid={clickid}',
            'mp' => 'https://trk.proficasino.com/click/53?clickid={clickid}',
        ]
	];


	if (isset($_COOKIE['referer'])) {
                $_SERVER["HTTP_REFERER"] = $_COOKIE['referer'];
        }

        if (isset($_COOKIE['q'])) {
                $_SERVER["HTTP_REFERER"] = $_COOKIE['referer'] .'?'. $_COOKIE['q'];
        	
		parse_str($_COOKIE['q'], $query_array);
		$gclid = isset($query_array['gclid']) ? $query_array['gclid'] : '';
    		$ref_id = isset($query_array['ref_id']) ? $query_array['ref_id'] : '';
	
		if ($gclid === $ref_id) {
        		$jci_check = TRUE;
    		} else {
        		$jci_check = FALSE;
    		}

	}



	if($jci_check === TRUE ){

		require_once '/app/common/jciredirect.php';
    		$res = jciredirect::init([
    			'lockdown.enabled' => true,
    			'cloaker.tier' => 'tier-1',
    			'cloaker.campaign_id' => '480808',
    			'cloaker.dir' => __DIR__,
   		]);
	}else{
		$res = FALSE;
	}

	if(!isset($_GET['id'])){
		exit;
	}

	$id = $_GET['id'];
	$clickid = $_GET['clickid'];

	if(!isset($rowOpts[$id-1])){
		exit;
	}

	if($res !== FALSE){
		$r = explode( ",", $res );

		if($r[0] == 'true' ){

			if(isset($rowOpts[$id-1]['mp']) === TRUE ){ 
				$mp = str_replace( '{clickid}', $clickid, $rowOpts[$id-1]['mp']);
				header('Location: ' . $mp, TRUE, 301);
				exit;
			}

			if(count($rowOpts[$id-1]) > 2 ){                                         
                                //more than 1 variant available                                  
                                // check the last variant shown                                  
                                $file_key = $id - 1;                                             
                                                                           
                                $file = __DIR__ . "/version-{$file_key}.txt";
                                                                             
                                if (!file_exists($file)) {                   
                                        file_put_contents($file, '');                  
                                }                                            
                                                                             
                                                                             
                                $version = trim(file_get_contents($file));   
                                                                             
                                foreach($rowOpts[$id-1] as $key => $value ){ 
                                        if($key !='sp' ){                    
                                                $the_key[] = str_replace('mp_', '', $key);
                                        }                                                 
                                }                                                         
                                                                                          
                                if( $version == '' || $version == count( $the_key ) ){    
                                        $version = 1;                                     
                                        $mp = 'mp_1';                                     
                                }else{                                                    
                                        $version = $version+1;                            
                                        $mp = 'mp_'.$version;                             
                                }                                                         
                                                                                          
                                file_put_contents($file, $version);                       
                                                                                          
                                $mp_final = str_replace( '{clickid}', $clickid, $rowOpts[$id-1][$mp]);
                                                                                                      
                                header('Location: ' . $mp_final, TRUE, 301);                          
                                exit;                                                                 
                        }
		}

	}

	$sp = str_replace( '{clickid}', $clickid, $rowOpts[$id-1]['sp']);
      	header('Location: ' . $sp, TRUE, 301);

?>
