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
		private $viewCode;
		private $validation;
        

        /**
         * Creates a new image finder object.
         */
        public function __construct($url = null, $vendor = null)
        {
                // Store url
                $this->url = $url;
				$this->vendor = 'Bvira';
				$this->base = 'https://www.lashowroom.com';
				
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
				$result = $xpath->query('//a[@class="large-preview"]');
				
				if ($result->length > 0) {
				
					// For all found img tags
					foreach($result as $img)
					{
					
							// Extract what we want
							$image = array
							(
									//'src' => self::make_absolute($img->getAttribute('href'), $this->base),
									'src' => 'http:'.$img->getAttribute('href'),
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
		
		public function DOMRemove(DOMNode $from) {
			$sibling = $from->firstChild;
			do {
				$next = $sibling->nextSibling;
				$from->parentNode->insertBefore($sibling, $from);
			} while ($sibling = $next);
			$from->parentNode->removeChild($from);    
		}
		
		
		public function get_details()
        {
                // Makes sure we're loaded
                $this->load();
				
				$xpath = new DOMXpath($this->document);
				$details = array();
				$fields = array('description', 'last_update', 'minimum_order', 'fabric', 'content', 'made_in', 'comments');
				
				//get the title
				$result = $xpath->query('//h1[@class="item-style-no"]');
				// For all found img tags
                foreach($result as $res)
                {
					$details['title'] = trim($res->nodeValue);
					$details['style_no'] = str_replace("#", "", $details['title']);
					break;
                }
				
				$result = $xpath->query('//table[@class="item-description"]/tr/td');
				$i=0;
				// For all found img tags
                foreach($result as $res)
                {
					
				
					$content = '';
					foreach($res->childNodes as $node)
					{
						$content .= $node->ownerDocument->saveXML( $node );
					}
					
					$dom = new DOMDocument; 
					$dom->loadHTML($content); 
					$path = new DOMXPath($dom); 
					$nodes = $path->query('//span');
					
					if($nodes->item(0)) { 
						$nodes->item(0)->parentNode->removeChild($nodes->item(0)); 
					} 
					$cont = $dom->saveHTML();
					
					$details[$fields[$i]] = trim(strip_tags($cont));
					$i++;
                }
				
				//unit price
				$result = $xpath->query('//span[@class="price-final"]');
				
			   foreach($result as $res)
			   {
					$content = '';
					foreach($res->childNodes as $node)
					{
						$content .= $node->ownerDocument->saveXML( $node );
					}
					
					$dom = new DOMDocument; 
					$dom->loadHTML($content); 
					$path = new DOMXPath($dom); 
					$nodes = $path->query('//label');
					
					if($nodes->item(0)) { 
						$nodes->item(0)->parentNode->removeChild($nodes->item(0)); 
					} 
					$cont = $dom->saveHTML();
					
					$details['unit_price'] = trim(strip_tags($cont));
				}
				
				//prepack price
				$result = $xpath->query('//span[@class="price-closeout-prepack"]');
				
			   foreach($result as $res)
			   {
					$content = '';
					foreach($res->childNodes as $node)
					{
						$content .= $node->ownerDocument->saveXML( $node );
					}
					
					$dom = new DOMDocument; 
					$dom->loadHTML($content); 
					$path = new DOMXPath($dom); 
					$nodes = $path->query('//label');
					
					if($nodes->item(0)) { 
						$nodes->item(0)->parentNode->removeChild($nodes->item(0)); 
					} 
					$cont = $dom->saveHTML();
					
					$details['prepack_price'] = trim(strip_tags($cont));
				}
				
				//pack size
				$result = $xpath->query('//div[@id="store_item_price_r"]');
				
			   foreach($result as $res)
			   {
					$content = '';
					foreach($res->childNodes as $node)
					{
						$content .= $node->ownerDocument->saveXML( $node );
					}
					
					$dom = new DOMDocument; 
					$dom->loadHTML($content); 
					$path = new DOMXPath($dom); 
					$nodes = $path->query('//span');
					
					if($nodes->item(0)) { 
						$nodes->item(0)->parentNode->removeChild($nodes->item(0)); 
					} 
					if($nodes->item(1)) { 
						$nodes->item(1)->parentNode->removeChild($nodes->item(1)); 
					} 
					
					if($nodes->item(2)) { 
						$nodes->item(2)->parentNode->removeChild($nodes->item(2)); 
					} 
					$cont = $dom->saveHTML();
					$split = explode(" ", trim(strip_tags($cont)));
					
					$details['size'] = $split[0];
					$details['unit_per_pack'] = $split[1];
				}
				
				//colors
				$result = $xpath->query('//a[@class="color-preview b"]');
				// For all found img tags
				
                foreach($result as $res)
                {
					$details['colors'][] = trim($res->nodeValue);
                }
				if(isset($details['comments']))
				{
					preg_match_all('/\w*\d/',$details['comments'],$output);
					
					if(count($output[0]) > 0)
					{
						$size = explode("-", $details['size']);
						$details['size_details'] = array_combine($size, $output[0]);
					}
                    else
                    {
                        $size = explode("-", $details['size']);
                        for($i=0;$i<count($size);$i++)
                        {
                            $test[] = NULL;
                        }
                        $details['size_details'] = array_combine($size, $test);
                    }
				}

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
			  $request = curl_init();
				$cookie="cookie.txt"; 
				$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 
				

				$pages = array(
					'home' =>	'https://www.lashowroom.com/', 
					'login' =>	'https://www.lashowroom.com/login'
				); 


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
			$response = curl_exec($request); 
			
			
			
			//Login 
			$options[CURLOPT_URL] = $pages['login']; 
			$options[CURLOPT_POST] = TRUE; 
			$options[CURLOPT_POSTFIELDS] = 'previous=&img_size=small&img_disp=70&page=1&sort=srd&na_section=1&login_id=info@elitehour.com&login_key=zzxcv1234&submit_login=Log In'; 
			$options[CURLOPT_FOLLOWLOCATION] = FALSE; 
			curl_setopt_array($request, $options); 
			curl_exec($request); 
			$response = curl_exec($request); 
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
		
		public function load_contents_redirect($url)
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
			$result = $xpath->query('//div[@class="lnv-module-content"]/div/ul/li');
			
			$category_url = array();
			$key	=	'no-parent-category';
			$i = 0;
			
			foreach($result as $res)
			{
				$class 	= $res->getAttribute('class');
				$title 	= $res->getAttribute('title');
				$value 	= $res->nodeValue;
				
				$nodes = $res->childNodes;
				$content = '';
				foreach ($nodes as $node) {
						$content .= $node->ownerDocument->saveXML( $node );
				}
				
								
				if($class == 'lnv-category-group-title')
				{

					if($key != $title)
					{
						$key = trim($title);
						$i = 0;
					}
					
					
				}
				else
				{
					if(empty($key) === false && empty($title) === false)
					{
					
						// Create DOM document
						$doc1 = new DOMDocument();

						// Load response into document, if we got any
						if($content)
						{
								libxml_use_internal_errors(true);
								$doc1->loadHTML($content);
								libxml_clear_errors();
						}
						
						$xpath1 = new DOMXpath($doc1);
						$result1 = $xpath1->query('//a');
						$curl = '';
						foreach($result1 as $res1)
						{
							$curl 	= $res1->getAttribute('href');
						}
					
						preg_match('#\w+(\s\w+)*#', $title, $match);
						$category_url[$key][$match[0]] = self::make_absolute($curl, $this->base);;
						$i++;
					}
				}
			}
			
		return $category_url;
        
		}
		
		 public function get_store_url($url)
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
			$result = $xpath->query('//ul[@class="lnv-module-list-store"]/li/a');
			
			$store_url = array();
			$i = 0;
			
			foreach($result as $res)
			{
				$src 	= $res->getAttribute('href');
				$key = trim($res->nodeValue);
				$store_url[$key]['url'] = self::make_absolute($src, $this->base);
				$i++;
			}
			
		return $store_url;
        
		}
		
		public function get_single_url_page($url)
        {
		
		
			 $request = curl_init();
				$cookie="cookie.txt"; 
				$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 
				

				$pages = array(
					'home' =>	'https://www.lashowroom.com/', 
					'login' =>	'https://lashowroom.com/login?previous=/ricokidsusa/browse/category/3/srd/small/70/1'
				); 


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
				

			
			//Login 
			$options[CURLOPT_URL] = $pages['login']; 
			$options[CURLOPT_POST] = TRUE; 
			$options[CURLOPT_POSTFIELDS] = 'previous=/ricokidsusa/browse/category/3/srd/small/70/1&img_size=small&img_disp=70&page=1&sort=srd&na_section=1&login_id=info@elitehour.com&login_key=zzxcv1234&submit_login=Log In'; 
			$options[CURLOPT_FOLLOWLOCATION] = FALSE; 
			curl_setopt_array($request, $options); 
			curl_exec($request); 
            
			$response = $this->load_contents_redirect($url);
			
			
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
			
			//get the pagination
			$counts = $xpath->query('//div[@class="tar pager"]/a');
			$pages = array();
			$i=0;
			
			foreach($counts as $count)
			{

			$cls = $count->getAttribute('class');
			$href = $count->getAttribute('href');
				
				if($cls != "page-block")
					 $pages[$i] = self::make_absolute($href, $this->base);
					 
				$i++;
			}
			
			return array_unique($pages);
		}
		
		public function get_single_url($url)
        {
		
		
			 $request = curl_init();
				$cookie="cookie.txt"; 
				$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)"; 
				

				$pages = array(
					'home' =>	'https://www.lashowroom.com/', 
					'login' =>	'https://lashowroom.com/login?previous=/ricokidsusa/browse/category/3/srd/small/70/1'
				); 


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
				

			
			//Login 
			$options[CURLOPT_URL] = $pages['login']; 
			$options[CURLOPT_POST] = TRUE; 
			$options[CURLOPT_POSTFIELDS] = 'previous=/ricokidsusa/browse/category/3/srd/small/70/1&img_size=small&img_disp=70&page=1&sort=srd&na_section=1&login_id=info@elitehour.com&login_key=zzxcv1234&submit_login=Log In'; 
			$options[CURLOPT_FOLLOWLOCATION] = FALSE; 
			curl_setopt_array($request, $options); 
			curl_exec($request); 
            $response = $this->load_contents_redirect($url);
			
			
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
			
			$result = $xpath->query('//div[@class="item-image-box item-image-box-s item-qv item-qv-s"]/a');
			
			$product_url = array();
			$i = 0;
			
			foreach($result as $res)
			{
				$src 	= $res->getAttribute('href');
				$product_url[$i]['url'] = self::make_absolute($src, $this->base);
				$i++;
			}
			
		return $product_url;
        
		}
		
		
		public function get_style_no($url)
        {
		
			//replace the default limit into as high => 500
			$url = str_replace('nListSet=50', 'nListSet=1157', $url);

		
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
			
			$result = $xpath->query('//table/tr/td[@style="font-size:11px; color:#487f93; line-height:14px;"]');
			
			$style_no = array();
			
			foreach($result as $res)
			{
				$style_no[] = $res->nodeValue;
			}
			

		return $style_no;
        
		}
		
		/**
         * Gets the html of a url and loads it up in a DOMDocument.
         */
        public function get_single_product_url($url)
        {
		
		
            $response = $this->load_contents($url);
			
			print_r($response);
			exit;
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
		


		
		public function get_products($url)
        {
				$this->url = $url;
		
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
			$response = str_replace('"/', '"https://www.lashowroom.com/', $response);
			
			//image path changing
		
			$response = str_replace('https://www.lashowroom.com//i4.lashowroom.com', 'http://i4.lashowroom.com', $response);
			
			//Close curl session 
			curl_close($request); 
			
			// Create DOM document
			$doc = new DOMDocument();

			// Load response into document, if we got any
			if($response)
			{
					libxml_use_internal_errors(true);
					$doc->loadHTML($response);
					libxml_clear_errors();
			}
			
			$products['images'] = $this->get_images();
			$products['details'] = $this->get_details();
			//$products['stock'] = $this->get_stock();
			//$i++;
		

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