<?php

require_once get_template_directory().'/libs/Loader.php';
require_once get_template_directory().'/libs/operator.php';
require_once get_template_directory().'/libs/technician.php';
require_once get_template_directory().'/libs/customers.php';
require_once get_template_directory().'/libs/OrderCPT.php';
require_once get_template_directory().'/libs/taxonomy.php';
require_once get_template_directory().'/libs/order.php';
require_once get_template_directory().'/libs/internal_order.php';
require_once get_template_directory().'/libs/ticket.php';
require_once get_template_directory().'/libs/setting.php';
require_once get_template_directory().'/libs/sms.php';
require_once get_template_directory().'/libs/PaymentLoader.php';
require_once get_template_directory().'/libs/PaymentLoaderzibal.php';
require_once get_template_directory().'/libs/financial.php';
require_once get_template_directory().'/libs/cost.php';
require_once get_template_directory().'/libs/jdf.php';
require_once get_template_directory().'/libs/convert_price.php';
require_once get_template_directory().'/libs/CronJob.php';

require_once get_template_directory() . '/rest/bootstrap.php';
require_once get_template_directory() . '/sms/bootstrap.php';
require_once get_template_directory() . '/notification/bootstrap.php';
require_once get_template_directory() . '/holidays/bootstrap.php';
require_once get_template_directory() . '/app-control/bootstrap.php';
require_once get_template_directory() . '/libs/cancel-reasons/admin-bootstrap.php';



define('TAMIR_TEST_MODE', false);
define('TAMIR_JWT_SECRET', 'jnkjnfkjrnkjnvkjnvkjdfnvkjnjjdjddjdjdjddaaaaawwr');
define('TAMIR_ALLOWED_ORIGINS', [
    'http://customer.tamironline.com',
    'https://tamironline.runflare.run',
]);
define( 'SAVEQUERIES', true );

add_theme_support( 'post-thumbnails' );

function register_my_menus(){
    register_nav_menus(
        array(
            'main_menu' => __('منو اصلی'),
            'footer_menu' => __('منو فوتر'),

        )
    );
}
add_action('init' , 'register_my_menus');


add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;

add_filter( 'comment_form_defaults', 'custom_reply_title' );
function custom_reply_title( $defaults ){
    $defaults['title_reply_before'] = '<span id="reply-title" class="h4 comment-reply-title">';
    $defaults['title_reply_after'] = '</span>';
    return $defaults;
}
function remove_jquery_migrate( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
        if ( $script->deps ) {
// Check whether the script has any dependencies

            $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
        }
    }
}
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );
// Add admin menu item
function optimized_order_export_menu() {
    add_management_page(
        'High Performance Order Export',
        'HP Order Export',
        'manage_options',
        'optimized-order-export',
        'optimized_order_export_page'
    );
}
add_action('admin_menu', 'optimized_order_export_menu');

// Display the export page
function optimized_order_export_page() {
    ?>
    <div class="wrap">
        <h1>High Performance Order Content Export</h1>
        <p>Optimized for large datasets with status filtering</p>
        
        <div class="export-controls">
            <form id="export-options">
                <h2>Export Options</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Status Filter</th>
                        <td>
                            <input type="text" id="status-filter" value="5" class="regular-text">
                            <p class="description">Export only posts with this status value.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Batch Size</th>
                        <td>
                            <select id="batch-size">
                                <option value="50">50 posts</option>
                                <option value="100" selected>100 posts</option>
                                <option value="200">200 posts</option>
                                <option value="500">500 posts (may be slow)</option>
                            </select>
                            <p class="description">Larger batch sizes process faster but may time out on some servers.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Export Format</th>
                        <td>
                            <label><input type="radio" name="format" value="csv" checked> CSV</label>
                            &nbsp;&nbsp;
                            <label><input type="radio" name="format" value="json"> JSON</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Include Fields</th>
                        <td>
                            <label><input type="checkbox" id="include-post-data" checked> Post data</label><br>
                            <label><input type="checkbox" id="include-author-data" checked> Author data</label><br>
                            <label><input type="checkbox" id="include-time-elapsed" checked> Time elapsed calculations</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Post ID Range (Optional)</th>
                        <td>
                            <input type="number" id="min-post-id" placeholder="Min Post ID" class="small-text">
                            to
                            <input type="number" id="max-post-id" placeholder="Max Post ID" class="small-text">
                            <p class="description">Limit export to specific post ID range for faster processing.</p>
                        </td>
                    </tr>
                </table>
            </form>
            
            <button id="start-export" class="button button-primary">Start Export</button>
            
            <div id="export-progress" style="display:none; margin-top: 20px;">
                <div class="progress-bar" style="height: 20px; background-color: #f1f1f1; border-radius: 3px; margin-bottom: 10px;">
                    <div class="progress" style="width: 0%; height: 100%; background-color: #0073aa; border-radius: 3px;"></div>
                </div>
                <p class="status">Preparing export...</p>
                <p class="details"></p>
                <button id="pause-export" class="button">Pause</button>
                <button id="resume-export" class="button" style="display:none;">Resume</button>
                <button id="cancel-export" class="button">Cancel</button>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            var totalPosts = 0;
            var processedPosts = 0;
            var currentOffset = 0;
            var batchSize = 100;
            var exportFormat = 'csv';
            var includePostData = true;
            var includeAuthorData = true;
            var includeTimeElapsed = true;
            var minPostId = null;
            var maxPostId = null;
            var statusFilter = '5';
            var exportData = [];
            var isPaused = false;
            var startTime = null;
            var lastBatchTime = 0;
            var estimatedTimeRemaining = '';
            var batchesProcessed = 0;
            var csvHeaders = null;
            
            // Initialize export
            $('#start-export').on('click', function() {
                // Get options
                batchSize = parseInt($('#batch-size').val());
                exportFormat = $('input[name="format"]:checked').val();
                includePostData = $('#include-post-data').is(':checked');
                includeAuthorData = $('#include-author-data').is(':checked');
                includeTimeElapsed = $('#include-time-elapsed').is(':checked');
                minPostId = $('#min-post-id').val() ? parseInt($('#min-post-id').val()) : null;
                maxPostId = $('#max-post-id').val() ? parseInt($('#max-post-id').val()) : null;
                statusFilter = $('#status-filter').val();
                
                // Reset variables
                totalPosts = 0;
                processedPosts = 0;
                currentOffset = 0;
                exportData = [];
                csvHeaders = null;
                isPaused = false;
                startTime = new Date();
                batchesProcessed = 0;
                
                // Show progress UI
                $(this).prop('disabled', true);
                $('#export-progress').show();
                $('#pause-export').show();
                $('#resume-export').hide();
                $('.status').text('Counting posts with status ' + statusFilter + '...');
                
                // First, get the total count
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'count_optimized_order_content',
                        nonce: '<?php echo wp_create_nonce("optimized_export_nonce"); ?>',
                        min_post_id: minPostId,
                        max_post_id: maxPostId,
                        status_filter: statusFilter
                    },
                    success: function(response) {
                        if (response.success) {
                            totalPosts = response.data.count;
                            $('.status').text('Found ' + totalPosts + ' posts with status ' + statusFilter + '. Starting export...');
                            
                            // Start processing batches
                            processBatch();
                        } else {
                            handleError('Error counting posts: ' + response.data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        handleError('AJAX error: ' + error);
                    }
                });
            });
            
            // Process a batch of posts
            function processBatch() {
                if (isPaused) {
                    return;
                }
                
                var batchStartTime = new Date();
                
                $('.details').text('Processing batch ' + (batchesProcessed + 1) + 
                                  ' (' + currentOffset + ' to ' + (currentOffset + batchSize) + ')' +
                                  (estimatedTimeRemaining ? '. Est. time remaining: ' + estimatedTimeRemaining : ''));
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'process_optimized_order_batch',
                        offset: currentOffset,
                        batch_size: batchSize,
                        include_post_data: includePostData ? 1 : 0,
                        include_author_data: includeAuthorData ? 1 : 0,
                        include_time_elapsed: includeTimeElapsed ? 1 : 0,
                        min_post_id: minPostId,
                        max_post_id: maxPostId,
                        status_filter: statusFilter,
                        nonce: '<?php echo wp_create_nonce("optimized_export_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Calculate batch processing time
                            var batchEndTime = new Date();
                            lastBatchTime = (batchEndTime - batchStartTime) / 1000;
                            batchesProcessed++;
                            
                            // Store headers from first batch if CSV format
                            if (exportFormat === 'csv' && !csvHeaders && response.data.headers) {
                                csvHeaders = response.data.headers;
                                exportData.push(csvHeaders);
                            }
                            
                            // Add the batch data to our export data
                            if (response.data.rows && response.data.rows.length > 0) {
                                if (exportFormat === 'csv') {
                                    // For CSV, we'll just collect all rows
                                    exportData = exportData.concat(response.data.rows);
                                } else {
                                    // For JSON, we need to merge the objects
                                    if (exportData.length === 0) {
                                        exportData = response.data.rows;
                                    } else {
                                        $.each(response.data.rows, function(i, item) {
                                            exportData.push(item);
                                        });
                                    }
                                }
                            }
                            
                            // Update progress
                            processedPosts += response.data.processed;
                            var percentage = Math.min(Math.round((processedPosts / totalPosts) * 100), 100);
                            
                            $('.progress').css('width', percentage + '%');
                            $('.status').text('Processed ' + processedPosts + ' of ' + totalPosts + ' posts (' + percentage + '%)');
                            
                            // Calculate estimated time remaining
                            if (batchesProcessed > 0) {
                                var elapsedSeconds = (new Date() - startTime) / 1000;
                                var postsPerSecond = processedPosts / elapsedSeconds;
                                var remainingPosts = totalPosts - processedPosts;
                                var remainingSeconds = remainingPosts / postsPerSecond;
                                
                                var hours = Math.floor(remainingSeconds / 3600);
                                var minutes = Math.floor((remainingSeconds % 3600) / 60);
                                var seconds = Math.floor(remainingSeconds % 60);
                                
                                estimatedTimeRemaining = '';
                                if (hours > 0) estimatedTimeRemaining += hours + 'h ';
                                if (minutes > 0) estimatedTimeRemaining += minutes + 'm ';
                                estimatedTimeRemaining += seconds + 's';
                            }
                            
                            // Continue with next batch or finish
                            if (processedPosts < totalPosts) {
                                currentOffset += batchSize;
                                setTimeout(processBatch, 100); // Small delay to prevent browser freezing
                            } else {
                                $('.status').text('Export complete! Generating file...');
                                $('.details').text('Processed ' + totalPosts + ' posts in ' + batchesProcessed + ' batches');
                                
                                // Generate and download the file
                                if (exportFormat === 'csv') {
                                    downloadCSV();
                                } else {
                                    downloadJSON();
                                }
                                
                                // Reset UI
                                $('#start-export').prop('disabled', false);
                                $('#pause-export, #resume-export, #cancel-export').hide();
                            }
                        } else {
                            handleError('Error processing batch: ' + response.data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        handleError('AJAX error: ' + error);
                    }
                });
            }
            
            // Handle pause/resume
            $('#pause-export').on('click', function() {
                isPaused = true;
                $(this).hide();
                $('#resume-export').show();
                $('.status').text('Export paused at ' + processedPosts + ' of ' + totalPosts + ' posts');
            });
            
            $('#resume-export').on('click', function() {
                isPaused = false;
                $(this).hide();
                $('#pause-export').show();
                $('.status').text('Resuming export...');
                processBatch();
            });
            
            // Handle cancel
            $('#cancel-export').on('click', function() {
                isPaused = true;
                $('#export-progress').hide();
                $('#start-export').prop('disabled', false);
                $('.status').text('Export cancelled');
                $('.progress').css('width', '0%');
            });
            
            // Handle errors
            function handleError(message) {
                $('.status').html('<span style="color: red;">' + message + '</span>');
                $('#start-export').prop('disabled', false);
                $('#pause-export, #resume-export').hide();
            }
            
            // Download as CSV
            function downloadCSV() {
                // Convert array to CSV string
                var csvContent = '';
                exportData.forEach(function(rowArray) {
                    var row = rowArray.map(function(cell) {
                        // Escape quotes and wrap in quotes if needed
                        cell = String(cell).replace(/"/g, '""');
                        if (cell.includes(',') || cell.includes('"') || cell.includes('\n')) {
                            cell = '"' + cell + '"';
                        }
                        return cell;
                    });
                    csvContent += row.join(',') + '\n';
                });
                
                // Create download link
                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                var url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'order-content-export-status-' + statusFilter + '-' + getFormattedDate() + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                $('.status').text('Export complete! CSV file downloaded.');
            }
            
            // Download as JSON
            function downloadJSON() {
                var jsonContent = JSON.stringify(exportData, null, 2);
                var blob = new Blob([jsonContent], { type: 'application/json;charset=utf-8;' });
                var link = document.createElement('a');
                var url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'order-content-export-status-' + statusFilter + '-' + getFormattedDate() + '.json');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                $('.status').text('Export complete! JSON file downloaded.');
            }
            
            function getFormattedDate() {
                var date = new Date();
                return date.getFullYear() + '-' + 
                       ('0' + (date.getMonth() + 1)).slice(-2) + '-' + 
                       ('0' + date.getDate()).slice(-2);
            }
        });
        </script>
    </div>
    <?php
}

// AJAX handler to count posts
add_action('wp_ajax_count_optimized_order_content', 'ajax_count_optimized_order_content');
function ajax_count_optimized_order_content() {
    // Check nonce
    if (!check_ajax_referer('optimized_export_nonce', 'nonce', false)) {
        wp_send_json_error(array('message' => 'Security check failed'));
    }
    
    global $wpdb;
    
    // Get status filter
    $status_filter = isset($_POST['status_filter']) ? sanitize_text_field($_POST['status_filter']) : '5';
    
    // Build query with optional post ID range and status filter
    $where_clauses = ["pm.meta_key = 'order_description_content'"];
    $where_params = [];
    
    // Add post ID range if provided
    if (!empty($_POST['min_post_id'])) {
        $where_clauses[] = "p.ID >= %d";
        $where_params[] = intval($_POST['min_post_id']);
    }
    
    if (!empty($_POST['max_post_id'])) {
        $where_clauses[] = "p.ID <= %d";
        $where_params[] = intval($_POST['max_post_id']);
    }
    
    // This query counts posts that have order_description_content meta
    // AND have at least one entry with status = $status_filter
    $query = "
        SELECT COUNT(DISTINCT p.ID) 
        FROM {$wpdb->posts} p
        JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE " . implode(' AND ', $where_clauses) . "
        AND EXISTS (
            SELECT 1 
            FROM {$wpdb->postmeta} pm_status 
            WHERE pm_status.post_id = p.ID 
            AND pm_status.meta_key = 'status' 
            AND pm_status.meta_value = %s
        )
    ";
    
    // Add status filter parameter
    $where_params[] = $status_filter;
    
    // Prepare the query
    $query = $wpdb->prepare($query, $where_params);
    
    $count = $wpdb->get_var($query);
    
    wp_send_json_success(array('count' => (int)$count));
}

// AJAX handler to process batches
add_action('wp_ajax_process_optimized_order_batch', 'ajax_process_optimized_order_batch');
function ajax_process_optimized_order_batch() {
    // Check nonce
    if (!check_ajax_referer('optimized_export_nonce', 'nonce', false)) {
        wp_send_json_error(array('message' => 'Security check failed'));
    }
    
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : 100;
    $include_post_data = isset($_POST['include_post_data']) && $_POST['include_post_data'] == 1;
    $include_author_data = isset($_POST['include_author_data']) && $_POST['include_author_data'] == 1;
    $include_time_elapsed = isset($_POST['include_time_elapsed']) && $_POST['include_time_elapsed'] == 1;
    $status_filter = isset($_POST['status_filter']) ? sanitize_text_field($_POST['status_filter']) : '5';
    
    global $wpdb;
    
    // Build query with optional post ID range and status filter
    $where_clauses = ["pm.meta_key = 'order_description_content'"];
    $where_params = [];
    
    // Add post ID range if provided
    if (!empty($_POST['min_post_id'])) {
        $where_clauses[] = "p.ID >= %d";
        $where_params[] = intval($_POST['min_post_id']);
    }
    
    if (!empty($_POST['max_post_id'])) {
        $where_clauses[] = "p.ID <= %d";
        $where_params[] = intval($_POST['max_post_id']);
    }
    
    // Determine which fields to select based on options
    $select_fields = "p.ID";
    if ($include_post_data) {
        $select_fields .= ", p.post_title, p.post_date, p.post_status";
    }
    if ($include_author_data) {
        $select_fields .= ", p.post_author";
    }
    $select_fields .= ", pm.meta_value";
    
    // This query gets posts that have order_description_content meta
    // AND have status = $status_filter
    $query = "
        SELECT {$select_fields}
        FROM {$wpdb->posts} p
        JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE " . implode(' AND ', $where_clauses) . "
        AND EXISTS (
            SELECT 1 
            FROM {$wpdb->postmeta} pm_status 
            WHERE pm_status.post_id = p.ID 
            AND pm_status.meta_key = 'status' 
            AND pm_status.meta_value = %s
        )
        ORDER BY p.ID
        LIMIT %d OFFSET %d
    ";
    
    // Add status filter and pagination parameters
    $where_params[] = $status_filter;
    $where_params[] = $batch_size;
    $where_params[] = $offset;
    
    // Prepare the query
    $query = $wpdb->prepare($query, $where_params);
    
    $results = $wpdb->get_results($query);
    
    // Define headers for CSV export
    $headers = array('Post ID');
    if ($include_post_data) {
        $headers = array_merge($headers, array('Post Title', 'Post Date', 'Post Status'));
    }
    $headers[] = 'Order Number';
    $headers[] = 'Status';
    $headers[] = 'Status Date';
    $headers[] = 'Content';
    if ($include_author_data) {
        $headers = array_merge($headers, array('Post Author ID', 'Post Author Name', 'Status Author ID', 'Status Author Name'));
    }
    if ($include_time_elapsed) {
        $headers[] = 'Time Elapsed';
    }
    
    $rows = array();
    $processed = 0;
    
    // Author cache to reduce DB queries
    $author_cache = array();
    
    // Process results
    foreach ($results as $row) {
        $processed++;
        $order_data = maybe_unserialize($row->meta_value);
        
        // Skip if not an array
        if (!is_array($order_data)) {
            continue;
        }
        
        // Get post author name if needed
        $post_author_name = '';
        if ($include_author_data && isset($row->post_author)) {
            if (!isset($author_cache[$row->post_author])) {
                $author_cache[$row->post_author] = get_the_author_meta('display_name', $row->post_author);
            }
            $post_author_name = $author_cache[$row->post_author];
        }
        
        // Track previous time for elapsed time calculation
        $previous_time = null;
        
        foreach ($order_data as $entry) {
            // Skip if essential data is missing
            if (!isset($entry['status']) || !isset($entry['author']) || !isset($entry['date']) || !isset($entry['content'])) {
                continue;
            }
            
            // Get status author name if needed
            $status_author_name = '';
            if ($include_author_data) {
                if (!isset($author_cache[$entry['author']])) {
                    $author_cache[$entry['author']] = get_the_author_meta('display_name', $entry['author']);
                }
                $status_author_name = $author_cache[$entry['author']];
            }
            
            // Calculate time elapsed if needed
            $time_elapsed = '';
            if ($include_time_elapsed) {
                $date_timestamp = strtotime($entry['date']);
                if (!$date_timestamp) {
                    // If date format is not standard, use current time as fallback
                    $date_timestamp = time();
                }
                
                if ($previous_time !== null) {
                    $time_diff = $date_timestamp - $previous_time;
                    $hours = floor($time_diff / 3600);
                    $minutes = floor(($time_diff % 3600) / 60);
                    $time_elapsed = $hours . ' hours ' . $minutes . ' minutes';
                }
                
                $previous_time = $date_timestamp;
            }
            
            // Extract order number if available
            $order_number = '';
            if (preg_match('/شماره (\d+)/', $entry['content'], $matches)) {
                $order_number = $matches[1];
            }
            
            // For CSV format
            if ($exportFormat !== 'json') {
                // Create row array based on headers
                $row_data = array($row->ID);
                
                if ($include_post_data) {
                    $row_data[] = isset($row->post_title) ? $row->post_title : '';
                    $row_data[] = isset($row->post_date) ? $row->post_date : '';
                    $row_data[] = isset($row->post_status) ? $row->post_status : '';
                }
                
                $row_data[] = $order_number;
                $row_data[] = $entry['status'];
                $row_data[] = $entry['date'];
                $row_data[] = $entry['content'];
                
                if ($include_author_data) {
                    $row_data[] = isset($row->post_author) ? $row->post_author : '';
                    $row_data[] = $post_author_name;
                    $row_data[] = $entry['author'];
                    $row_data[] = $status_author_name;
                }
                
                if ($include_time_elapsed) {
                    $row_data[] = $time_elapsed;
                }
                
                $rows[] = $row_data;
            } 
            // For JSON format
            else {
                // Build the row data based on selected options
                $row_data = array('post_id' => $row->ID);
                
                if ($include_post_data) {
                    $row_data['post_title'] = isset($row->post_title) ? $row->post_title : '';
                    $row_data['post_date'] = isset($row->post_date) ? $row->post_date : '';
                    $row_data['post_status'] = isset($row->post_status) ? $row->post_status : '';
                }
                
                // Always include order number if found
                $row_data['order_number'] = $order_number;
                
                // Always include status information
                $row_data['status'] = $entry['status'];
                $row_data['status_date'] = $entry['date'];
                $row_data['content'] = $entry['content'];
                
                if ($include_author_data) {
                    $row_data['post_author_id'] = isset($row->post_author) ? $row->post_author : '';
                    $row_data['post_author_name'] = $post_author_name;
                    $row_data['status_author_id'] = $entry['author'];
                    $row_data['status_author_name'] = $status_author_name;
                }
                
                if ($include_time_elapsed) {
                    $row_data['time_elapsed'] = $time_elapsed;
                }
                
                $rows[] = $row_data;
            }
        }
    }
    
    wp_send_json_success(array(
        'processed' => $processed,
        'rows' => $rows,
        'headers' => $headers
    ));
}





