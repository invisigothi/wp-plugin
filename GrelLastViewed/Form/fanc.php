<?php
 function getAllPages()
 {
    global $wpdb;
     $allSitePages = get_pages();
     $pagesInfo = array();
     foreach ($allSitePages as $page)
     {
         $pagesInfo[] = array(
             "id" => $page->ID,
             "title" => $page->post_title,
         );
     }
     return $pagesInfo;
 }

 