<?php

/**
 * Finds images from a give URL.
 *
 * @author   Torleif Berger
 * @link     http://www.geekality.net/?p=1585
 * @license  http://creativecommons.org/licenses/by/3.0/
 */

class BviraFinder
{
        private $document;
        private $url;
        private $base;
        

        /**
         * Creates a new image finder object.
         */
        public function __construct($url = null, $vendor = null)
        {
                // Store url
                $this->url = $url;
				$this->vendor = 'Bvira';
				$this->base = 'http://moeim.net/';
				
				 $this->bad = array(
		   'À','à','Á','á','Â','â','Ã','ã','Ä','ä','Å','å','A','a','A','a',
		   'C','c','C','c','Ç','ç',
		   'D','d','Ð','d',
		   'È','è','É','é','Ê','ê','Ë','ë','E','e','E','e',
		   'G','g',
		   'Ì','ì','Í','í','Î','î','Ï','ï',
		   'L','l','L','l','L','l',
		   'Ñ','ñ','N','n','N','n',
		   'Ò','ò','Ó','ó','Ô','ô','Õ','õ','Ö','ö','Ø','ø','o',
		   'R','r','R','r',
		   '','','S','s','S','s',
		   'T','t','T','t','T','t',
		   'Ù','ù','Ú','ú','Û','û','Ü','ü','U','u',
		   '','ÿ','ý','Ý',
		   '','','Z','z','Z','z',
		   'Þ','þ','Ð','ð','ß','','','Æ','æ','µ',
		   '','','','',"'","\n","\r",'_');

		   $this->good = array(
		   'A','a','A','a','A','a','A','a','Ae','ae','A','a','A','a','A','a',
		   'C','c','C','c','C','c',
		   'D','d','D','d',
		   'E','e','E','e','E','e','E','e','E','e','E','e',
		   'G','g',
		   'I','i','I','i','I','i','I','i',
		   'L','l','L','l','L','l',
		   'N','n','N','n','N','n',
		   'O','o','O','o','O','o','O','o','Oe','oe','O','o','o',
		   'R','r','R','r',
		   'S','s','S','s','S','s',
		   'T','t','T','t','T','t',
		   'U','u','U','u','U','u','Ue','ue','U','u',
		   'Y','y','Y','y',
		   'Z','z','Z','z','Z','z',
		   'TH','th','DH','dh','ss','OE','oe','AE','ae','u',
		   '','','','','','','','-');
				
        }


        /**
         * Loads the HTML from the url if not already done.
         */
        public function load()
        {
                // Return if already loaded
                //if($this->document)
                        //return;
                
                // Get the HTML document
                $this->document = self::get_document($this->url);
				

                // Get the base url
                $this->base = self::get_base($this->document);
                if( ! $this->base)
                        $this->base = $this->url;
        }


        /**
         * Returns an array with all the images found.
         */
        public function get_images()
        {
                // Makes sure we're loaded
                $this->load();

			
                // Image collection array
                $images = array();
				
				$xpath = new DOMXpath($this->document);
				$result = $xpath->query('//div[@class="panviewer"]/img');
				
				if ($result->length > 0) {
				
					// For all found img tags
					foreach($result as $img)
					{
					
							// Extract what we want
							$image = array
							(
									'src' => self::make_absolute($img->getAttribute('src'), $this->base),
							);
							
							// Skip images without src // || $img->getAttribute('id') != 'zoomHover gae-click*Product-Page*Zoom-In*Image-Click'
							if( ! $image['src'])
									continue;

							// Add to collection. Use src as key to prevent duplicates.
							$images[$image['src']] = $image;
					}
				
					
				}

                // Return values
                return array_values($images);
        }
		
		public function get_details()
        {
                // Makes sure we're loaded
                $this->load();
				
				$xpath = new DOMXpath($this->document);
				$result = $xpath->query('//div[@class="prd_rt"]/table/tr/td');

				
				$i =1 ;
				$details = array();
				$temp = '';
				$bad = $this->bad;
				$good = $this->good;
				$keys = array();
				$values = array();
				
                // For all found img tags
                foreach($result as $res)
                {
				  if($i<= 10) // 10 -> number of fields in details section in moeim.net
				  {
					if($i % 2 == 0)			
					{
						$temp = strtolower($temp);
						
						if($temp == 'description')
						{
							$desc				=	$this->stripText($res->nodeValue);
							$details[$temp] 	=  	ucfirst($desc);
							
							//Made in
							preg_match('/(?<=Made in )\S+/i', $desc, $match);
							
							if(isset($match[0]))
							        $details['made']	=	$match[0]; 
							
							if(empty($details['made'])) {
								preg_match('/(?<=Made in: )\S+/i', $desc, $match);
							
								if(isset($match[0]))
									$details['made']	=	$match[0];	
							}

							if(empty($details['made'])) {
								preg_match('/(?<=Origin: )\S+/i', $desc, $match);
							
								if(isset($match[0]))
									$details['made']	=	$match[0];	
							}
							
							if(empty($details['made']) == false) {
								
								preg_match('#\D+#', $details['made'], $match);
								$details['made']	=	$match[0];	
							}
								
							//Title retrieve from desc
							preg_match_all('/^\D+\s*/i', $desc, $match);
							
							if(isset($match[0][0]))
							{
								$match = current($match);
								$details['title']	= ucfirst(trim($match[0]));
							}
							
															
							//materials retrievel
							preg_match_all('/(?<=% )\w+/i', $res->nodeValue, $match);
							
							if(count($match) > 0)
							{
								$details['matrial']	= $match[0];
							}
							
							
							//materials another query
							preg_match_all('/(?<=%)\w+/i', $res->nodeValue, $match);
							
							if(count($match) > 0)
							{
								if(isset($details['matrial']))
									$details['matrial']	=	array_merge($details['matrial'], $match[0]);
								else
									$details['matrial']	=	$match[0];
							}
							
							
						}
						else
						{

							$text = trim(preg_replace('/\s\s+/', ' ', $res->nodeValue));
							$text = str_replace($bad, $good, $text);
							$text = str_replace('A ', '', $text);
							$details[$temp] = $text;
						}
							
							
					}
					//Store the previous value for key
					$temp =  str_replace(' ', '_',$res->nodeValue);
					
					//list price retrieval
					$xpath = new DOMXpath($this->document);
					$result = $xpath->query('//font[@style="color:#c40001; font-size:12px;"]');
					
					// For all found img tags
					foreach($result as $res)
					{
						$details['price']	= $res->nodeValue;
						break;
					}
					
				 }
				 else
				 {
					$sizes = explode('-',$details['size']);
					$end  = 10 + count($sizes);
					if($i > 10 && $i <=$end)
						$keys[] = $res->nodeValue;
					else
						$values[] = $res->nodeValue;
				 }
				 
					$i++;
                }
				
				//Pack size details
				if(count($keys) > 0 & count($values) > 0)
					$details['packs'] = array_combine($keys, $values);
					
			   if(isset($details['title']) === false || empty($details['title']))
					$details['title']	=	$this->vendor.'-'.$details['style_no'];

			return $details;
                
        }
		
		
		public function get_stock()
        {
                // Makes sure we're loaded
                $this->load();
				
				$xpath = new DOMXpath($this->document);
				$result = $xpath->query('//div[@class="info_tit"]/span');
				
                // For all found img tags
                foreach($result as $res)
                {
					return $res->nodeValue;
					break;
                }
                
        }
		
		
		

        /**
         * Gets the html of a url and loads it up in a DOMDocument.
         */
        private static function get_document($url)
        {
		
                // Set up and execute a request for the HTML
                $request = curl_init();
				$cookie="cookie.txt"; 
				$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 

				$options = array(

							CURLOPT_RETURNTRANSFER => TRUE,
							CURLOPT_HEADER => false,
							
							CURLOPT_SSL_VERIFYPEER => false,
							//CURLOPT_CAINFO => 'cacert.pem',
							CURLOPT_FOLLOWLOCATION => 1,
							//CURLOPT_MAXREDIRS => 10,
							CURLOPT_COOKIEJAR => $cookie,
							CURLOPT_COOKIEFILE => $cookie,
							CURLOPT_USERAGENT => $agent,
					);
				
		
			//Hit data page 
			$options[CURLOPT_URL] = $url; 
			curl_setopt_array($request, $options); 
			$response = curl_exec($request); 

			//Close curl session 
			curl_close($request); 

			// Create DOM document
			$document = new DOMDocument();

			// Load response into document, if we got any
			if($response)
			{
					libxml_use_internal_errors(true);
					$document->loadHTML($response);
					libxml_clear_errors();
			}

			return $document;
        
		}
		
		public function init()
		{
			  // Set up and execute a request for the HTML
                $request = curl_init();
				$cookie="cookie.txt"; 
				$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 
				

				$pages = array(
					'home' =>	'http://moeim.net', 
					'login' =>	'http://moeim.net/member/login_proc.asp'); 

				$options = array(

							CURLOPT_RETURNTRANSFER => TRUE,
							CURLOPT_HEADER => false,
							
							CURLOPT_SSL_VERIFYPEER => false,
							//CURLOPT_CAINFO => 'cacert.pem',
							CURLOPT_FOLLOWLOCATION => 0,
							//CURLOPT_MAXREDIRS => 10,
							CURLOPT_COOKIEJAR => $cookie,
							CURLOPT_COOKIEFILE => $cookie,
							CURLOPT_USERAGENT => $agent,
					);
				

			//Hit home page for session cookie 
			$options[CURLOPT_URL] = $pages['home']; 
			curl_setopt_array($request, $options); 
			curl_exec($request); 
			

			//Login 
			$options[CURLOPT_URL] = $pages['login']; 
			$options[CURLOPT_POST] = TRUE; 
			$options[CURLOPT_POSTFIELDS] = 'login_check=login&action_url=login_proc.asp&login_id=balaji@elitehour.com&login_pwd=balaji123'; 
			$options[CURLOPT_FOLLOWLOCATION] = FALSE; 
			curl_setopt_array($request, $options); 
			curl_exec($request); 
		}
		
		public function load_contents($url)
		{
			 // Set up and execute a request for the HTML
                $request = curl_init();
				$cookie="cookie.txt"; 
				$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 

				$options = array(

							CURLOPT_RETURNTRANSFER => TRUE,
							CURLOPT_HEADER => false,
							
							CURLOPT_SSL_VERIFYPEER => false,
							//CURLOPT_CAINFO => 'cacert.pem',
							CURLOPT_FOLLOWLOCATION => 0,
							//CURLOPT_MAXREDIRS => 10,
							CURLOPT_COOKIEJAR => $cookie,
							CURLOPT_COOKIEFILE => $cookie,
							CURLOPT_USERAGENT => $agent,
					);
				
			
			//Hit home page for session cookie 
			$options[CURLOPT_URL] = $url; 
			curl_setopt_array($request, $options); 
			$response = curl_exec($request); 
			
			//Close curl session 
			curl_close($request); 
			
			return $response;
		}
		
		 public function get_categories_url($url)
        {
		
            $response = $this->load_contents($url);
			
			// Create DOM document
			$doc = new DOMDocument();

			// Load response into document, if we got any
			if($response)
			{
					libxml_use_internal_errors(true);
					$doc->loadHTML($response);
					libxml_clear_errors();
			}
			
			$xpath = new DOMXpath($doc);
			$result = $xpath->query('//table[@width="100%"]/tr/td/ul/li/a');
			
			$category_url = array();
			$key	=	'';
			$i = 0;
			
			foreach($result as $res)
			{
				$src 	= $res->getAttribute('href');
				$value 	= $res->nodeValue;
								
                                $regex = "#\(\d+\)#";
				preg_match($regex, $value, $match);
				
				if(isset($match[0]))
				{
					$regex = "#\w+(\s[a-z A-z &]*)*#";
					preg_match($regex, $value, $match1);
					
					$category = $match1[0];
					
					$category_url[$key][$i]['url'] = self::make_absolute($src, $this->base);
					$category_url[$key][$i]['category'] = $category;
					$i++;
				}
				else
				{
					if($key != $value)
					{
						$key = trim($value);
						$i = 0;
					}
				}
			}
			
		return $category_url;
        
		}
		
		/**
         * Gets the html of a url and loads it up in a DOMDocument.
         */
        public function get_single_product_url($url)
        {
		
			//replace the default limit into as high => 500
			$url = str_replace('nListSet=25', 'nListSet=500', $url);

		
            $response = $this->load_contents($url);
			
			// Create DOM document
			$doc = new DOMDocument();

			// Load response into document, if we got any
			if($response)
			{
					libxml_use_internal_errors(true);
					$doc->loadHTML($response);
					libxml_clear_errors();
			}
			
			$xpath = new DOMXpath($doc);
			$result = $xpath->query('//span[@class="ex_01"]');
			
			foreach($result as $res)
			{
				$total_count	=	$res->nodeValue;
				break;
			}
			
			$result = $xpath->query('//table[@width="138"]/tr/td/a');
			
			$product_url = array();
			
			foreach($result as $res)
			{
				$product_id = $res->getAttribute('onclick');
				$regex = "#(\d+)#";
				preg_match($regex, $product_id, $match);
				
				if(isset($match[1]))
				{
					$purl	=	'style/style_detail.asp?itm_idx='.$match[1];
					//hardcoded, need to optimize
					$base = 'http://moeim.net/';
					$product_url[] = self::make_absolute($purl, $base);
				}
			}
		

		return $product_url;
        
		}
		
		/**
         * Gets the html of a url and loads it up in a DOMDocument.
         */
        public function get_products($url)
        {
		
			$products = array();

			$this->url = $url;
			$products['images']  = $this->get_images();
			$products['details'] = $this->get_details();
			$products['stock'] = $this->get_stock();

			return $products;
        
		}

        /**
         * Tries to get the base tag href from the given document.
         */
        private static function get_base(DOMDocument $document)
        {
                $tags = $document->getElementsByTagName('base');

                foreach($tags as $tag)
                        return $tag->getAttribute('href');

                return NULL;
        }


        /**
         * Makes sure a url is absolute.
         */
        private static function make_absolute($url, $base) 
        {
                // Return base if no url
                if( ! $url) return $base;

                // Already absolute URL
                if(parse_url($url, PHP_URL_SCHEME) != '') return $url;
                
                // Only containing query or anchor
                if($url[0] == '#' || $url[0] == '?') return $base.$url;
                
                // Parse base URL and convert to local variables: $scheme, $host, $path
                extract(parse_url($base));

                // If no path, use /
                if( ! isset($path)) $path = '/';
         
                // Remove non-directory element from path
                $path = preg_replace('#/[^/]*$#', '', $path);
         
                // Destroy path if relative url points to root
                if($url[0] == '/') $path = '';
                
                // Dirty absolute URL
                $abs = "$host$path/$url";
         
                // Replace '//' or '/./' or '/foo/../' with '/'
                $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
                for($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {}
                
                // Absolute URL is ready!
                return $scheme.'://'.$abs;
        }
		
		
		public function stripText($text, $separator = ' ')
		 {
		   $bad = $this->bad;

		   $good = $this->good;

		   // convert special characters
		   $text = str_replace($bad, $good, $text);
		   
		   
				 
		   // convert special characters
		   $text = utf8_decode($text);
		   $text = htmlentities($text);
		   $text = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $text);
		   $text = html_entity_decode($text);
		   
		   //$text = strtolower($text);

		   // strip all non word chars
		   //$text = preg_replace('/\W/', ' ', $text);

		   // replace all white space sections with a separator
		   $text = preg_replace('/\ +/', $separator, $text);

		   // trim separators
		   $text = trim($text, $separator);
		   //$text = preg_replace('/\-$/', '', $text);
		   //$text = preg_replace('/^\-/', '', $text);
			   
		   return $text;
		 }
}