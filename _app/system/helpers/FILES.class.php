<?php

	/*
	
		FILES::lista(PASTA->STRING,FILEFILTER->STRING,PREFIXO->BOOLEAN);
	
	*/

	$path = realpath(dirname(__FILE__))."/";
	FILES::$path = $path;
	
	class FILES{
		
		public static $path = "";
	
		// Files("fotos","*.jpg,*.png,",false); // Desta forma vai retornar todos os arquivos do tipo jpg que estiverem dentro da pasta
		public static function lista($pasta,$fileFilter,$prefixoPasta = false,$type = "arquivos"){
			return self::list_path($pasta,$fileFilter,$prefixoPasta,$type);		
		}
		
		public static function arquivos($pasta,$fileFilter,$prefixoPasta = false){
			return self::list_path($pasta,$fileFilter,$prefixoPasta,'arquivos');
		}
		
		public static function pastas($pasta,$fileFilter,$prefixoPasta = false){
			return self::list_path($pasta,$fileFilter,$prefixoPasta,'pastas');
		}
		
		private static function list_path($pasta,$fileFilter,$prefixoPasta = false,$type = "arquivos"){
			
			$arquivos = array();
			
			$pesquisa = glob($pasta .'/{'. $fileFilter .'}' , GLOB_BRACE);
			$total = count($pesquisa);
			
			for($i = 0 ; $i < $total ; $i++){
				
				$item = $pesquisa[$i];
				
				if(!empty($item)){
					
					if($type == "arquivos" and is_file($item)){
						if($prefixoPasta == false){
							$arquivos[] = basename($item);
						}else{
							$arquivos[] = $item;
						}
					}else if($type == "pastas" and is_dir($item)){
						if($prefixoPasta == false){
							$item = str_replace("\\","/",$item);
							$arquivos[] = end(explode("/",$item));
						}else{
							$arquivos[] = $item;
						}
					}else if($type == "all"){
						if($prefixoPasta == false){
							$item = str_replace("\\","/",$item);
							$arquivos[] = end(explode("/",$item));
						}else{
							$arquivos[] = $item;
						}
					}
					
				}
				
			}
			
			return $arquivos;
			
		}
	
	}

?>