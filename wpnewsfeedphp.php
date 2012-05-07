<?php
/*
Cache RSS/XML feed locally

*/
		$cache_time = 3600; // 1 hour
        $cache_file = $_SERVER['DOCUMENT_ROOT'].'/includes/news.xml'; //xml file saved on server
       
        $timedif = @(time() - filemtime($cache_file));
 
        $xml = "";
        if (file_exists($cache_file) && $timedif < $cache_time) {
                $str = file_get_contents($cache_file);
                $xml = simplexml_load_string($str);
        } else { //not there
                $xml = simplexml_load_file('http://news.olemiss.edu/category/schools/school-of-education/feed/'); //come here
                if ($f = fopen($cache_file, 'w')) { //save info
                        $str = $xml->asXML();
                        fwrite ($f, $str, strlen($str));
                        fclose($f);
                }
        }
?>
<?php
/*

Class RSSParser:    2 October 2002

Author            Duncan Gough

Overview:        An RSS parser that uses PHP and freely available RSS feeds to add fresh news content to your site.

Installation:        Decompress the file into your webroot and include it from whichever pages on which you want
            to display the data, e.g;

            include("rss.php");

Usage:            As above, just include the rss.php file from within your PHP page, and the news will appear.
            You should find the HTML code in the functions endElement(), show_title() and show_list_box() below,
            feel free to modify these to match your site.
*/

class RSSParser    {

    var $title        = "";
    var $link         = "";
    var $description    = "";
    var $inside_item    = false;

    function startElement( $parser, $name, $attrs='' ){
        global $current_tag;

        $current_tag = $name;

        if( $current_tag == "ITEM" )
            $this->inside_item = true;

    } // endfunc startElement

    function endElement( $parser, $tagName, $attrs='' ){
        global $current_tag;

        if ( $tagName == "ITEM" ) {

            printf( "<a class='text' href='%s' target='_blank'>%s</a>", htmlspecialchars( trim( $this->link ) ), htmlspecialchars( trim( $this->title ) ) );
            //printf( "\t<br>%s<br>\n", htmlspecialchars( trim( $this->description ) ) );

            $this->title = "";
            //$this->description = "";
            $this->link = "";
            $this->inside_item = false;

        }

    } // endfunc endElement

    function characterData( $parser, $data ){
        global $current_tag;

        if( $this->inside_item ){
            switch($current_tag){

                case "TITLE":
                    $this->title .= $data;
                    break;
                case "DESCRIPTION":
                    $this->description .= $data;
                    break;
                case "LINK":
                    $this->link .= $data;
                    break;

                default:
                    break;

            } // endswitch

        } // end if

    } // endfunc characterData

    function parse_results( $xml_parser, &$rss_parser, $file )    {

        xml_set_object( $xml_parser, $rss_parser );
        xml_set_element_handler( $xml_parser, "startElement", "endElement" );
        xml_set_character_data_handler( $xml_parser, "characterData" );

        $fp = fopen("$file","r") or die( "Error reading XML file, $file" );

        while ($data = fread($fp, 4096))    {

            // parse the data
            xml_parse( $xml_parser, $data, feof($fp) ) or die( sprintf( "XML error: %s at line %d", xml_error_string( xml_get_error_code($xml_parser) ), xml_get_current_line_number( $xml_parser ) ) );

        } // endwhile

        fclose($fp);

        xml_parser_free( $xml_parser );

    } // endfunc parse_results

} // endclass RSSParser

global $rss_url;

// Set a default feed
$rss_url = "http://education.olemiss.edu/includes/news.xml";
$xml_parser = xml_parser_create();
$rss_parser = new RSSParser(&$rss_parser);

echo '<div class="feedName"></div>';
$rss_parser->parse_results( $xml_parser, $rss_parser, $rss_url );

?>