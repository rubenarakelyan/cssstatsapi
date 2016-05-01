<?php
namespace RubenArakelyan\CSSStatsAPI;

/**
 * Class CSSStatsAPI
 * @package RubenArakelyan\CSSStatsAPI
 */
class CSSStatsAPI
{

    private $ch;
    private $url = 'http://cssstats.com/stats';
    private $debug = false;

    /**
     * Constructor
     */
    public function __construct($debug = false)
    {
        // Set debugging mode
        $this->debug = $debug;
        
        // Create a new instance of cURL
        $this->ch = curl_init();
        
        // Set the user agent
        // It does not provide CSSStats.com with any personal information
        // but helps them track usage of this PHP class.
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'CSSStats PHP API for CSSStats.com (+https://github.com/rubenarakelyan/cssstatsapi)');
        
        // Return the result of the query
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // Delete the instance of cURL
        curl_close($this->ch);
    }

    /**
	 * Send an API query
	 *
	 * @return JSON
	 */
    public function query($site_url, $display_details = 'all')
    {
        // Exit if the site URL is not defined
        if (!isset($site_url) || $site_url === '') {
            return $this->_error('Site URL not provided.');
        }
        
        // Exit of the display details aren't 'all', an array of items, or null; convert null to an empty array
        if ($display_details !== 'all' && !is_array($display_details) && $display_details !== null) {
            return $this->_error('Incorrect display details provided.');
        }
        
        if ($display_details === null) {
            $display_details = [];
        }
        
        // Set up the response array
        $response = [];
        $response['cssstats'] = [];
        
        // Query the site
        $result = $this->_execute_query($site_url);
        
        // Set up a DOM document and suppress errors caused by malformed HTML
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        
        // Load the resulting HTML into the DOM document
        $doc->loadHTML($result);
        
        // DEBUG: Output any errors
        $this->_debug(libxml_get_errors(), 'LibXML errors');
        
        // Set up XPath and extract what we need from the HTML
        $xpath = new DOMXPath($doc);
        
        // Extract: site name
        $query = '//header/div/h1[@class="m0"]';
        $site_name = trim(utf8_decode(iterator_to_array($xpath->query($query))[0]->nodeValue));
        $response['cssstats']['site_name'] = $site_name;
        $response['cssstats']['site_url'] = $site_url;
        
        // Extract: CSS size
        $query = '//header/div/h1[@class="h2 m0"]';
        $css_size = trim(utf8_decode(iterator_to_array($xpath->query($query))[0]->nodeValue));
        $response['cssstats']['css_size'] = $css_size;
        
        // Extract: CSS size (gzipped)
        $query = '//header/div/small';
        $css_size_gzipped = trim(utf8_decode(iterator_to_array($xpath->query($query))[0]->nodeValue));
        $response['cssstats']['css_size_gzipped'] = str_replace(' (Gzipped)', '', $css_size_gzipped);
        
        $response['cssstats']['stats'] = [];
        
        //
        // Extract: top stats
        //
        
        // Run the XPath query
        $query = '//section[@id="top-stats"]/div/h1';
        $data_top_stats = $xpath->query($query);
        
        // DEBUG: Output the result of the XPath query
        $this->_debug($data_top_stats, 'XPath query results - top stats');
        
        // Go through all the extracted data
        $array_top_stats = [];
        foreach ($data_top_stats as $d) {
            // Trim the node value and fix UTF-8 values
            $value = trim(utf8_decode($d->nodeValue));

            // DEBUG: Output the node value
            $this->_debug($value, 'XPath query node value - top stats');
            
            // Add the value to the array for later
            $array_top_stats[] = $value;
        }
        
        // Add data to the response
        $response['cssstats']['stats']['top_stats'] = [];
        $response['cssstats']['stats']['top_stats']['rules'] = $array_top_stats[0];
        $response['cssstats']['stats']['top_stats']['selectors'] = $array_top_stats[1];
        $response['cssstats']['stats']['top_stats']['declarations'] = $array_top_stats[2];
        $response['cssstats']['stats']['top_stats']['properties'] = $array_top_stats[3];
        
        //
        // Extract: declarations
        //
        
        // Run the XPath query
        $query = '//section[@id="declarations"]/div/div/h1';
        $data_declarations = $xpath->query($query);
        
        // DEBUG: Output the result of the XPath query
        $this->_debug($data_declarations, 'XPath query results - declarations');
        
        // Go through all the extracted data
        $array_declarations = [];
        foreach ($data_declarations as $d) {
            // Trim the node value and fix UTF-8 values
            $value = trim(utf8_decode($d->nodeValue));

            // DEBUG: Output the node value
            $this->_debug($value, 'XPath query node value - declarations');
            
            // Add the value to the array for later
            $array_declarations[] = $value;
        }
        
        // Add data to the response
        $response['cssstats']['stats']['declarations'] = [];
        $response['cssstats']['stats']['declarations']['font_size'] = $array_declarations[0];
        $response['cssstats']['stats']['declarations']['float'] = $array_declarations[1];
        $response['cssstats']['stats']['declarations']['width'] = $array_declarations[2];
        $response['cssstats']['stats']['declarations']['height'] = $array_declarations[3];
        $response['cssstats']['stats']['declarations']['color'] = $array_declarations[4];
        $response['cssstats']['stats']['declarations']['background_color'] = $array_declarations[5];
        
        //
        // Extract: unique colours
        //
        
        if ($display_details === 'all' || in_array('unique_colors', $display_details, true)) {
            // Run the XPath query
            $query = '//section[@id="unique-colors"]/div/div/div[contains(@class,"h6")]';
            $data_unique_colours = $xpath->query($query);
            
            // DEBUG: Output the result of the XPath query
            $this->_debug($data_unique_colours, 'XPath query results - unique colours');
            
            // Go through all the extracted data
            $array_unique_colours = [];
            foreach ($data_unique_colours as $d) {
                // Trim the node value and fix UTF-8 values
                $value = trim(utf8_decode($d->nodeValue));
    
                // DEBUG: Output the node value
                $this->_debug($value, 'XPath query node value - unique colours');
                
                // Add the value to the array for later
                $array_unique_colours[] = $value;
            }
            
            // Add data to the response
            $response['cssstats']['stats']['unique_colors'] = [];
            foreach ($array_unique_colours as $d) {
                $response['cssstats']['stats']['unique_colors'][] = $d;
            }
        }
        
        //
        // Extract: unique background colours
        //
        
        if ($display_details === 'all' || in_array('unique_background_colors', $display_details, true)) {
            // Run the XPath query
            $query = '//section[@id="unique-background-colors"]/div/div/div[contains(@class,"h6")]';
            $data_unique_background_colours = $xpath->query($query);
            
            // DEBUG: Output the result of the XPath query
            $this->_debug($data_unique_background_colours, 'XPath query results - unique background colours');
            
            // Go through all the extracted data
            $array_unique_background_colours = [];
            foreach ($data_unique_background_colours as $d) {
                // Trim the node value and fix UTF-8 values
                $value = trim(utf8_decode($d->nodeValue));
    
                // DEBUG: Output the node value
                $this->_debug($value, 'XPath query node value - unique background colours');
                
                // Add the value to the array for later
                $array_unique_background_colours[] = $value;
            }
            
            // Add data to the response
            $response['cssstats']['stats']['unique_background_colors'] = [];
            foreach ($array_unique_background_colours as $d) {
                $response['cssstats']['stats']['unique_background_colors'][] = $d;
            }
        }
        
        //
        // Extract: unique font sizes
        //
        
        if ($display_details === 'all' || in_array('unique_font_sizes', $display_details, true)) {
            // Run the XPath query
            $query = '//section[@id="unique-font-sizes"]/div/div';
            $data_unique_font_sizes = $xpath->query($query);
            
            // DEBUG: Output the result of the XPath query
            $this->_debug($data_unique_font_sizes, 'XPath query results - unique font sizes');
            
            // Go through all the extracted data
            $array_unique_font_sizes = [];
            foreach ($data_unique_font_sizes as $d) {
                // Trim the node value and fix UTF-8 values
                $value = trim(utf8_decode($d->nodeValue));
    
                // DEBUG: Output the node value
                $this->_debug($value, 'XPath query node value - unique font sizes');
                
                // Add the value to the array for later
                $array_unique_font_sizes[] = $value;
            }
            
            // Add data to the response
            $response['cssstats']['stats']['unique_font_sizes'] = [];
            foreach ($array_unique_font_sizes as $d) {
                $response['cssstats']['stats']['unique_font_sizes'][] = str_replace('Font Size ', '', $d);
            }
        }
        
        //
        // Extract: unique font families
        //
        
        if ($display_details === 'all' || in_array('unique_font_families', $display_details, true)) {
            // Run the XPath query
            $query = '//section[@id="unique-font-families"]/div';
            $data_unique_font_families = $xpath->query($query);
            
            // DEBUG: Output the result of the XPath query
            $this->_debug($data_unique_font_families, 'XPath query results - unique font families');
            
            // Go through all the extracted data
            $array_unique_font_families = [];
            foreach ($data_unique_font_families as $d) {
                // Trim the node value and fix UTF-8 values
                $value = trim(utf8_decode($d->nodeValue));
    
                // DEBUG: Output the node value
                $this->_debug($value, 'XPath query node value - unique font families');
                
                // Add the value to the array for later
                $array_unique_font_families[] = $value;
            }
            
            // Add data to the response
            $response['cssstats']['stats']['unique_font_families'] = [];
            foreach ($array_unique_font_families as $d) {
                $response['cssstats']['stats']['unique_font_families'][] = $d;
            }
        }
        
        //
        // Extract: media queries
        //
        
        if ($display_details === 'all' || in_array('media_queries', $display_details, true)) {
            // Run the XPath query
            $query = '//section[@id="media-queries"]/div/div';
            $data_media_queries = $xpath->query($query);
            
            // DEBUG: Output the result of the XPath query
            $this->_debug($data_media_queries, 'XPath query results - media queries');
            
            // Go through all the extracted data
            $array_media_queries = [];
            foreach ($data_media_queries as $d) {
                // Trim the node value and fix UTF-8 values
                $value = trim(utf8_decode($d->nodeValue));
    
                // DEBUG: Output the node value
                $this->_debug($value, 'XPath query node value - media queries');
                
                // Add the value to the array for later
                $array_media_queries[] = $value;
            }
            
            // Add data to the response
            $response['cssstats']['stats']['media_queries'] = [];
            foreach ($array_media_queries as $d) {
                $response['cssstats']['stats']['media_queries'][] = $d;
            }
        }
        
        // Return the JSON response
        return json_encode($response);
    }

    /**
	 * Execute an API query
	 *
	 * @return JSON
	 */
    private function _execute_query($site_url)
    {
        // Assemble the data to send
        $fields = 'url=' . urlencode($site_url);
        
        // Set the URL to query
        curl_setopt($this->ch, CURLOPT_URL, $this->url . '?' . $fields);
        
        // Get the result
        $result = curl_exec($this->ch);
        
        // Find out if all is OK
        if (!$result) {
            // A problem occurred with cURL
            return $this->_error('cURL error occurred: ' . curl_error($this->ch));
        } else {
            $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            if ($http_code == 404) {
                // Received a 404 error querying the site
                return $this->_error('Could not reach the server.');
            }
            
            return $result;
        }
    }

    /**
	 * Return an error message
	 *
	 * @return JSON
	 */
    private function _error($error_message)
    {
        return json_encode(['error_message' => $error_message]);
    }

    /**
	 * Print out debugging messages
	 *
	 * @return void
	 */
    private function _debug($debug_message, $debug_message_title = '')
    {
        if ($this->debug) {
            echo '<pre><strong>' . $debug_message_title . '</strong><br>' . print_r($debug_message, true) . '</pre>';
        }
    }
}
