<?php 

// set the maximal number of displayed search results here
$max_result_number = 10;

if ($_GET['s'] != '') {
    // HACK HACK HACK: With permalinks enabled Wordpress is confused by our path and interprets
    // parts of it as categories or other things in the permalink structure. So we better hide
    // our identify
    $_SERVER['REQUEST_URI'] = str_replace("wp-content/plugins/live-search-popup/search_results.php", 
                                          "", $_SERVER['REQUEST_URI']);
    $posts_per_page = $max_result_number + 1;
    global $table_prefix;
    require('../../../wp-blog-header.php');

    if (count($posts) > 0) {
        echo '<ul id="resultlist">';
        foreach (array_slice($posts, 0, $max_result_number) as $post) {
            the_post(); ?>
            <li class="resultlistitem"><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></li>
<?php
        } 
                                 
        // If there are more than 10 results, show an additional <li>-element 
        // with total # or results - choosing that will then go to the search results page
        if (count($posts) > $max_result_number) {
            echo'<li class="resultlistitem"><a href="' .  get_bloginfo(url) . "?s=" . $_GET['s'] .
              '" style="font-weight: bold" rel="bookmark" onclick="ls_form.submit(); return false;" >&gt;&gt; ' .
              __('Show all results') . "</a></li>";
        }

        echo '</ul>';
    } else {
        echo '<p>' . __("No Results.") . '</p>';
    }
    
    /* uncomment this to show the link that allows closing of the search results
    echo '<a href="#" onclick="ls.close(); return false;" class="close_link">X Clear search results</a>';
    */
}
?>