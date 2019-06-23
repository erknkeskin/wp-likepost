<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://erkankeskin.com.tr
 * @since      1.0.0
 *
 * @package    Crx_Likepost
 * @subpackage Crx_Likepost/public/partials
 * Template Name: Crx Likepost Page Template
 */

get_header();

global $wpdb;

$query = "SELECT * FROM {$wpdb->prefix}terms AS t1 LEFT JOIN {$wpdb->prefix}term_taxonomy AS t2 ON t1.term_id = t2.term_id WHERE taxonomy='post_tag'";
$total_query = "SELECT COUNT(*) AS CNT FROM {$wpdb->prefix}terms";
$total = $wpdb->get_var( $total_query );
$q = $wpdb->get_results( $query.' ORDER BY t2.count DESC ');

echo "<h1>All Taglist</h1>";
echo '<div class="crx-list"><ul>';
    foreach ($q as $row):
        echo '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i> <a target="_blank" href="'.site_url().'/tag/'.$row->slug.'">'.$row->name.'<span>('.$row->count.')</span></a></li>';
    endforeach;
echo '</ul></div>';

get_sidebar();

get_footer();