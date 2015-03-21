# cssstatsapi

PHP API to obtain statistics for your CSS from http://cssstats.com/.

## Usage

    // Include the API
    require_once 'cssstatsapi.php';
    
    // Instantiate the API
    $api = new CSSStatsAPI();
    
    // Execute a query for a sample website
    $result = $api->query('http://yahoo.com/');
    
    // Indicate that this is a JSON response
    header('Content-Type: application/json');
    
    // Print the JSON response
    print_r($result);

## Output

    {
        "cssstats": {
            "site_name":"Yahoo",
            "site_url":"http:\/\/yahoo.com\/",
            "css_size":"221 KB",
            "css_size_gzipped":"51 KB",
            "stats": {
                "top_stats": {
                    "rules":"2,221",
                    "selectors":"2,685",
                    "declarations":"5,303",
                    "properties":"132"
                },
                "declarations": {
                    "font_size":"81",
                    "float":"85",
                    "width":"401",
                    "height":"271",
                    "color":"245",
                    "background_color":"117"
                },
                "unique_colors": ["#5f5f5f","#afafaf","#3f3f3f","#fff","#2db6e5","#27a684","#7a58cf","#324fe1","#333","#FFF","#e2e2e6","#1d1da3","#000","#ffc208","inherit","#aaa","#373737","white","#30302f","#2d5ec0","#1e2683","#191919","#abaeb7","#666","#777","#231f20","#f47821","#f00","#7b0099","#1665c3","#c00","#16387c","#93242a","#00a651","#006cc9","#454953","#1e7d8e","#a1a1a1","#b5b5b5","#ffdad6","rgba(255,255,255,0.7)","#b20f60","#fe6968","#ccc","#178817","#06c","#999","#BBB","#565c68","#555","#eade9f","#fbfbfb","#7690f4","#b68ee7","#413f3e","#61399d","#482b74","#29a8bf","#a4a6a9","#8a8a8a","#d43125","#111","#a298c2","#8ec0ff","#400090","#c8c8c8","#969696","#929eb8","#cb3234","#b0b0b0"],
                "unique_background_colors": ["#fff","transparent","#5771ff","#4255b5","#e2e2e6","#5f5f5f","#333434","#ac362d","#d43125","#9e251b","#7d30b2","#abaeb7","#565c68","black","white","#1da09c","#454953","#2d1152","#3f0095","#f2f2f1","#400090","#fafafa","#eee","#f7f7f7","#340077","#204c82","#500095","#2d62ad","#f1f1f1","#3775dd","#fafafc","#efefef","#f2f4f6","#000","#1665c3","#7b0099","#c00","#f2f2f2","#93242a","#cdcdcd","#f5f5f9","#fceb9d","#e7edf8","#171780","#df2319","#fe4e4d","#382d2d","#c0c0c0","#3f1c59","#922a8f","#f70049","#61399d","#f5f4f9","#5100ba","#3f0091","#321c59","#6e329d","#4576ea","#dae4fa","#1d2532","#52b633","#f7f8fa","#e5e8f7","#FFF","#c6d7ff","#404e67","#fcefc7"],
                "unique_font_sizes": ["0","16px\\9","13","15","267px","28px","27px","25px","23px","22px","21px","18px","17px","16px","1em","100%","15px","14px","87%","84.61538%","13px","80%","12px","75%","11px","10px"],
                "unique_font_families": ["\"Helvetica Neue\",Helvetica,Arial","'Helvetica Neue',Helvetica,Arial,sans-serif","\"Helvetica Neue\",HelveticaNeue,helvetica,arial,sans-serif","'HelveticaNeue-Light',Helvetica","Georgia,'Times New Roman',serif","'Helvetica Neue'","sans-serif","\"Helvetica Neue\", Helvetica, Arial"],
                "media_queries": ["onlyScreenAnd (WebkitMinDevicePixelRatio:2),onlyScreenAnd (minMozDevicePixelRatio:2),onlyScreenAnd (OMinDevicePixelRatio:2\/1),onlyScreenAnd (minDevicePixelRatio:2),onlyScreenAnd (minResolution:192dpi),onlyScreenAnd (minResolution:2dppx)","screenAnd (maxWidth:1129px)","onlyScreenAnd (maxWidth :1025px)","onlyScreenAnd (maxWidth:1010px)","onlyScreenAnd (WebkitMinDevicePixelRatio:2),\nonlyScreenAnd (MinMozDevicePixelRatio:2),\nonlyScreenAnd (OMinDevicePixelRatio:2\/1),\nonlyScreenAnd (MinDevicePixelRatio:2),\nonlyScreenAnd (MinResolution:192dpi),\nonlyScreenAnd (MinResolution:2dppx)"]
            }
        }
    }

`{"error_message":"Incorrect display details provided."}`

## Options

`void CSSStatsAPI ( [ $debug = false ] )`

* `$debug`: (Optional) Set to `true` to print out messages and data helpful for debugging.

`json query ( string $site_url [, mixed $display_details = 'all' ] )`

* `$site_url`: The URL of the site to analyse - this is passed to cssstats.com for analysis.
* `$display_details`: (Optional) Either
    * `'all'` to include all sections in the output (note that this does not currently capture graph content),
    * `null` to include only the top stats and declaration counts in the output, or
    * an array containing one or more of `unique_colors`, `unique_background_colors`, `unique_font_sizes`, `unique_font_families` and/or `media_queries` to also include those sections in the output

## Error messages

* Site URL not provided: No URL was provided to the `query` method.
* Incorrect display details provided: The `$display_details` attribute was not either `'all'`, `null`, or an array of items.
* cURL error occurred: [error message]: There was a problem when trying to contact cssstats.com; the error message will provide more details.
* Could not reach the server: A 404 error was encountered when attempting to contact cssstats.com.

## Support

Please submit issues to https://github.com/rubenarakelyan/cssstatsapi/issues.

## Contributing

All pull requests are gratefully accepted.

## Licence

All files in this repository are licenced under the Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) licence.

See http://creativecommons.org/licenses/by-sa/3.0/ for the full licence text.

Please note that data pulled by the API is licenced separately.