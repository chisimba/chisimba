<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end of security

class dbwebpresentslides extends dbtable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_webpresent_slides');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    /**
     * Method to get the number of slides in a presentation
     * @param string $fileId Record Id of the Presentation
     * @return int Number of Slides
     */
    public function getNumSlides($fileId)
    {
        return $this->getRecordCount(' WHERE fileid=\''.$fileId.'\'');
    }

    /**
     * Method to get the slides in a presentation
     * @param string $fileId Record Id of the Presentation
     * @return array list of slides
     */
    public function getSlides($fileId)
    {
        return $this->getAll(' WHERE fileid=\''.$fileId.'\' ORDER BY slideorder');
    }


    /**
     * Method to go through all the slides in a presentation, and add their data to the database
     *
     * This method uses the HTML Export of Open Office which gives:
     * 1) One html page with content per slide
     * 2) One jpeg image per slide content
     *
     * It takes these files, parses them, and then adds them to the database
     * The image is also converted into a thumbnail
     *
     * @param string $fileId Record Id of the Presentation
     * @return array list of slides
     */
    public function scanPresentationDir($id)
    {
        // Setup Directory
        $dir = $this->objConfig->getcontentBasePath().'webpresent/'.$id;

        // Load Scanner
        $objScan = $this->newObject('scanpresentation');

        // Scan Directory - Only relevant files are returned
        $results = $objScan->scanDirectory($dir);

        // Check that there are results
        if (count($results) == 0) {

        } else {
            // Sort by Natural Order
            natsort($results);

            // Set Slide Counter
            $counter = 0;

            // Loop through each slide
            foreach ($results as $result=>$path)
            {

                // Additional check that file exists
                if (file_exists($path))
                {
                    // Increase Counter
                    $counter++;

                    // Get Contents of File
                    $contents = file_get_contents($path);

                    // Get Title
                    preg_match_all('%<title>(?P<title>.*)</title>%', $contents, $titles, PREG_PATTERN_ORDER);

                    // Store Title if one is found
                    if (isset($titles['title'][0]))
                    {
                        $title = $titles['title'][0];
                    } else {
                        $title = 'No Title Found';
                    }

                    // Clear Line Breaks
                    $contents = str_replace("\r", '', $contents);
                    $contents = str_replace("\n", '', $contents);

                    // Get Contents
                    preg_match_all('%</center><br>(?P<content>.*)</body></html>%', $contents, $content, PREG_PATTERN_ORDER);

                    // Store Contents if one exists
                    if (isset($content['content'][0]))
                    {
                        $content = $content['content'][0];
                    } else {
                        $content = 'No Content Found';
                    }

                    // Add to Database
                    $slideId = $this->addSlideContent($id, $title, $content, $counter);

                    // Generate Thumbnail
                    $this->generateSlideThumbnail($id, $slideId, $counter);
                }


            }

        }
    }

    /**
     * Method to add the content of a slide to the database
     * @param string $fileId RecordId of the Presentation
     * @param string $title Title of the Slide
     * @param string $content Content of the Slide
     * @param int $content Order of the Slide
     *
     * @return string Record Id of Slide
     */
    private function addSlideContent($fileId, $title, $content, $order)
    {
        return $this->insert(array(
                'fileid' => $fileId,
                'slidetitle' => $title,
                'slidecontent' => $content,
                'slideorder' => $order,
            ));
    }

    public function getSlideThumbnail($slideId, $title='')
    {
        $full = $this->objConfig->getcontentBasePath().'webpresent_slide_thumbnails/'.$slideId.'.jpg';
        $rel = $this->objConfig->getsiteRoot().$this->objConfig->getcontentPath().'webpresent_slide_thumbnails/'.$slideId.'.jpg';

        if (trim($title) == '')
        {
            $title = '';
        } else {
            $title = ' title="'.htmlentities($title).'" alt="'.htmlentities($title).'"';
        }


        if (file_exists($full))
        {
            return '<img src="'.$rel.'" '.$title.' />';
        } else {
            $slide = $this->getRow('id', $slideId);

            if ($slide == FALSE)
            {
                return 'Error: Slide does not exist';
            } else {

                if ($this->generateSlideThumbnail($slide['fileid'], $slide['id'], $slide['slideorder']))
                {
                    return '<img src="'.$rel.'" '.$title.' />';
                } else {
                    return 'Error: Could not generate thumbnail';
                }

            }

        }

    }

    /**
     * Method to generate a Thumbnail of a slide
     * @param string $fileId Record Id of the Presentation
     * @param string $slideId Record Id of the Slide
     * @param int $order Order of the Slide in the Presentation
     *
     * @return boolean Whether the thumbnail exists or not
     */
    private function generateSlideThumbnail($fileId, $slideId, $order)
    {
        // Var for the destination filename of the slide
        $destination = $this->objConfig->getcontentBasePath().'webpresent_slide_thumbnails/'.$slideId.'.jpg';

        // Check if thumbnail exists
        if (file_exists($destination)) {
            return TRUE;
        } else {
            // Get Source Image
            $source = $this->objConfig->getcontentBasePath().'webpresent/'.$fileId.'/img'.($order-1).'.jpg';

            // Check that Source Image exists
            if (file_exists($source)) {

                // Check that destination directory exists
                $objMkDir = $this->getObject('mkdir', 'files');
                $destinationDir = $this->objConfig->getcontentBasePath().'/webpresent_slide_thumbnails';
                $objMkDir->mkdirs($destinationDir);

                // Load Image Resize Class
                $this->objImageResize = $this->getObject('imageresize', 'files');

                // Set Source
                $this->objImageResize->setImg($source);

                // Resize to 160x120 Maintaining Aspect Ratio
                $this->objImageResize->resize(160, 120, TRUE);

                //$this->objImageResize->show(); // Uncomment for testing purposes

                // If thumbnail can be created, save it to file system
                if ($this->objImageResize->canCreateFromSouce) {
                    $this->objImageResize->store($destination);
                    return TRUE;
                } else { // Thumbnail not generated.
                    return FALSE;
                }
            } else { // Error Source Image Exists
                return FALSE;
            }
        }
    }

    public function getPresentationSlidesContent($id, $withSlideShow = FALSE)
    {
        $slides = $this->getSlides($id);

        $objTrim = $this->getObject('trimstr', 'strings');


        $slidesContent = '<h1>Slides</h1><ul class="presentationslides">';
        $transcriptContent = '<h1>Transcript</h1><ul>';
        $slideShow = array();


        $counter=1;

        foreach ($slides as $slide)
        {
            if ($withSlideShow)
            {
                $slidesContent .= '<li>'.$objTrim->strTrim($slide['slidetitle'], 27).'<br /><a href="javascript:void(viewer.show('.($counter-1).'))">'.$this->getSlideThumbnail($slide['id'], $slide['slidetitle']).'</a></li>';
                //$slidesContent .= '<li>'.$objTrim->strTrim($slide['slidetitle'], 27).'<br /><a href="usrfiles/webpresent/'.$id.'/img'.($counter-1).'.jpg" rel="lightbox[set]" title="'.htmlspecialchars($slide['slidetitle']).'">'.$this->getSlideThumbnail($slide['id'], $slide['slidetitle']).'</a></li>';


                $slideShow[] = "viewer.add('usrfiles/webpresent/".$id."/img".($counter-1).".jpg', '".(addslashes($slide['slidetitle']))."');";
            } else {
                $slidesContent .= '<li>'.$objTrim->strTrim($slide['slidetitle'], 27).'<br />'.$this->getSlideThumbnail($slide['id'], $slide['slidetitle']).'</li>';
            }

            $content = preg_replace('/<.*?>/', ' ', $slide['slidecontent']);

            if (trim($content) != '')
            {
                $transcriptContent .= '<li><strong>Slide '.$counter.'</strong> - '.$content.'</li>';
            }

            $counter++;

        }

        $slidesContent .= '</ul>';
        $transcriptContent .= '</ul>';

        return array('slides'=>$slidesContent, 'transcript'=>$transcriptContent, 'slideshow'=>$slideShow);
    }

    public function getPresentationSlidesFormatted($id)
    {
        $content = $this->getPresentationSlidesContent($id);


        $objTabs = $this->newObject('tabcontent', 'htmlelements');

        $objTabs->addTab('Slides', $content['slides']);
        $objTabs->addTab('Transcript', $content['transcript']);
        $objTabs->width = '524px';

        return $objTabs->show();
    }

    public function deleteSlides($fileId)
    {
        $slides = $this->getSlides($fileId);

        if (count($slides) > 0)
        {
            foreach ($slides as $slide)
            {
                $this->deleteSlideThumbnail($slide['id']);
                $this->delete('id', $slide['id']);

                $firstPage =  $this->objConfig->getcontentBasePath().'webpresent/'.$fileId.'/'.$fileId.'.html';
                if (file_exists($firstPage))
                {
                    unlink($firstPage);
                }

                $img = $this->objConfig->getcontentBasePath().'webpresent/'.$fileId.'/img'.($slide['slideorder']-1).'.jpg';
                if (file_exists($img))
                {
                    unlink($img);
                }

                $html = $this->objConfig->getcontentBasePath().'webpresent/'.$fileId.'/img'.($slide['slideorder']-1).'.html';
                if (file_exists($html))
                {
                    unlink($html);
                }

                $text = $this->objConfig->getcontentBasePath().'webpresent/'.$fileId.'/text'.($slide['slideorder']-1).'.html';
                if (file_exists($text))
                {
                    unlink($text);
                }

            }
        }
    }

    private function deleteSlideThumbnail($slideId)
    {
        $fullPath = $this->objConfig->getcontentBasePath().'webpresent_slide_thumbnails/'.$slideId.'.jpg';

        if (file_exists($fullPath))
        {
            return unlink ($fullPath);
        } else {
            return FALSE;
        }

    }

}
?>