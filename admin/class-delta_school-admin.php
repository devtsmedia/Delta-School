<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    delta_school
 * @subpackage delta_school/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    delta_school
 * @subpackage delta_school/admin
 * @author     Your Name <email@example.com>
 */
class delta_school_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $delta_school    The ID of this plugin.
     */
    private $delta_school;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $delta_school       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($delta_school, $version)
    {

        $this->delta_school = $delta_school;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in delta_school_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The delta_school_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->delta_school, plugin_dir_url(__FILE__) . 'css/delta_school-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in delta_school_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The delta_school_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->delta_school, plugin_dir_url(__FILE__) . 'js/delta_school-admin.js', array('jquery'), $this->version, false);
    }
}

/*
// Add checkbox to LearnDash Lesson settings
function add_group_leaders_checkbox()
{
    add_meta_box(
        'group_leaders_checkbox',
        'Only Accessible for Group Leaders?',
        'group_leaders_checkbox_callback',
        'learndash-lessons-settings',  // LearnDash Lesson post type
        'side',
        'default'
    );
}


function group_leaders_checkbox_callback($post)
{
    $value = get_post_meta($post->ID, '_group_leaders_only', true);
?>
    <label for="group_leaders_checkbox">
        <input type="checkbox" id="group_leaders_checkbox" name="group_leaders_checkbox" <?php checked($value, 'on'); ?> />
        Only accessible for Group Leaders
    </label>
<?php
}

add_action('add_meta_boxes', 'add_group_leaders_checkbox');
*/











/* ADD YEAR GROUP FUNCTIONLITY */
function add_year_groups_submenu_page()
{
    add_submenu_page(
        'learndash-lms', // LearnDash main menu slug
        'Year Groups',
        'Year Groups',
        'manage_options',
        'year_groups_page',
        'year_groups_page_content'
    );
}
add_action('admin_menu', 'add_year_groups_submenu_page');

// Year Groups page content
function year_groups_page_content()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'year_groups';

    // Fetch all year groups
    $year_groups = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sort_order");
?>
    <div class="wrap">
        <div class="main-top">
            <h2 class="main-title">Year Groups</h2>
        </div>
        <div class="add-new-year-data">
            <div class="form-group">
                <input type="text" class="name" name="name" placeholder="Please enter year ">
                <a href="javascript:;" class="page-title-action" id="add-year-group">Add New Year Group</a>
            </div>
        </div>
        <table class="wp-list-table widefat fixed striped" id="year-table">
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody id="year-groups-list">
                <?php
                if ($year_groups) {
                    foreach ($year_groups as $year_group) : ?>
                        <tr data-id="<?php echo esc_attr($year_group->id); ?>">
                            <td class="name" contenteditable="true"><?php echo esc_html($year_group->name); ?></td>
                        </tr>
                    <?php endforeach;
                } else { ?>
                    <tr>
                        <td>No records found</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <div id="loader" style="display: none;">Loading...</div>
        <div id="message" style="display: none;"></div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            jQuery(function($) {
                $(document).ready(function() {
                    // Double-click to edit
                    $('#year-groups-list .name').on('dblclick', function() {
                        $('#year-groups-list').sortable('disable');
                        $(this).prop('contenteditable', true).focus();
                    });

                    // Save changes on blur
                    $('#year-groups-list .name').on('blur', function() {
                        $('#year-groups-list').sortable('enable');
                        var id = $(this).closest('tr').data('id');
                        var name = $(this).text().trim();

                        // Send AJAX request to save the name
                        showLoader();
                        $.post(ajaxurl, {
                            action: 'save_year_group_name',
                            id: id,
                            name: name
                        }, function(response) {
                            hideLoader();
                            showAlert(response.success ? 'success' : 'error', response.data);
                        });

                        $(this).prop('contenteditable', false);
                    });

                    // Add new year group
                    $('#add-year-group').on('click', function() {

                        $year_name = $(".name").val();

                        if ($year_name == '') {
                            alert("Please enter year group");
                            return false;
                        }

                        // Show loading indicator
                        showLoader();

                        // Send AJAX request to add a new year group
                        $.post(ajaxurl, {
                            action: 'add_year_group',
                            // Include any additional data you want to send, for example:
                            name: $year_name,
                            sort_order: 6
                        }, function(response) {
                            // Hide loading indicator
                            hideLoader();

                            // Show a success or error message
                            if (typeof response.message === 'object') {
                                console.log('Response data object:', response.message);
                                showAlert('error', 'Unexpected response from the server.');
                            } else {
                                console.log('Response data object else:', response.message);
                                showAlert(response.message ? 'success' : 'error', response.message);
                                location.reload(); // Refresh the page after adding the new year group
                            }
                        });
                    });

                    // Make the table sortable
                    $('#year-groups-list').sortable({
                        update: function(event, ui) {
                            // Handle sorting update, send AJAX request to save sort order
                            var order = $('#year-groups-list').sortable('toArray', {
                                attribute: 'data-id'
                            });
                            showLoader();
                            $.post(ajaxurl, {
                                action: 'save_year_group_sort_order',
                                order: order
                            }, function(response) {
                                hideLoader();
                                showAlert(response.success ? 'success' : 'error', response.data);
                            });
                        }
                    });

                    // Disable selection on the entire table
                    // $('#year-groups-list').disableSelection();
                });

                function showLoader() {
                    $('#loader').show();
                }

                function hideLoader() {
                    $('#loader').hide();
                }

                function showAlert(type, message) {
                    console.log("Show");
                    var alertClass = type === 'success' ? 'updated' : 'error';
                    $('#message').removeClass('updated error').addClass(alertClass).html('<p>' + message + '</p>').show();
                    setTimeout(function() {
                        $('#message').fadeOut('slow');
                    }, 3000);
                }
            });
        </script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
        <script>
            jQuery(document).ready(function($) {
                $("#year-table").DataTable({
                    "paging": false, // Disable pagination if you have a small number of rows
                    "searching": true,
                    "ordering": false,
                    "info": true, // Disable table information
                });
            });
        </script>
    <?php
}

function save_year_group_sort_order()
{
    if (isset($_POST['order'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'year_groups';
        $order = $_POST['order'];
        $count = 1;
        foreach ($order as $item_id) {
            $wpdb->update(
                $table_name,
                array('sort_order' => $count),
                array('id' => $item_id),
                array('%d'),
                array('%d')
            );
            $count++;
        }
        wp_send_json_success('Sort order updated successfully.');
    } else {
        wp_send_json_error('Invalid request.');
    }
}
add_action('wp_ajax_save_year_group_sort_order', 'save_year_group_sort_order');


function save_year_group_name()
{
    if (isset($_POST['id']) && isset($_POST['name'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'year_groups';
        $id = absint($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $wpdb->update(
            $table_name,
            array('name' => $name),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
        wp_send_json_success('Year Group updated successfully.');
    } else {
        wp_send_json_error('Invalid request.');
    }
}
add_action('wp_ajax_save_year_group_name', 'save_year_group_name');



function add_year_group()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'year_groups';
    $name = sanitize_text_field($_POST['name']);
    $result = $wpdb->insert(
        $table_name,
        array('name' => $name),
        array('%s')
    );
    if ($result === false) {
        $error_message = $wpdb->last_error;
        wp_send_json_error(array('message' => 'Error adding year group: ' . $error_message));
    }
    $insert_id = $wpdb->insert_id;
    $response = array('id' => $insert_id, 'message' => 'Year group added successfully.');
    wp_send_json($response);
}
// Hook the AJAX action
add_action('wp_ajax_add_year_group', 'add_year_group');
add_action('wp_ajax_nopriv_add_year_group', 'add_year_group');

/* END YEAR GROUP FUNCTIONLITY*/



/* ADD A SCHOOL */

// Add a new menu option under LearnDash
function add_schools_menu()
{
    add_submenu_page(
        'learndash-lms',
        'Schools',
        'Schools',
        'manage_options',
        'schools',
        'display_schools_page'
    );
}
add_action('admin_menu', 'add_schools_menu');


/* Display Schools listing page */
function display_schools_page()
{
    display_schools_table();
}


/* Display Schools table */
function display_schools_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'schools';
    $school_configurations = $wpdb->prefix . 'school_configurations';


    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['school_id'])) {
        $school_id = intval($_GET['school_id']);
        $wpdb->delete($table_name, array('id' => $school_id));
        $wpdb->delete($school_configurations, array('school_id' => $school_id));
    }

    // Fetch and display Schools data in a table
    $schools = $wpdb->get_results("SELECT * FROM $table_name");

    // Add your page content, such as the Schools listing, here
    echo '<div class="wrap"><div class="main-top"><h2 class="main-title">Schools</h2> <a href="' . admin_url('admin.php?page=add_school') . '" class="page-title-action">Add New School</a></div>';
    // Display the Schools table

    // Add content for Basic Dat

    echo '<div class="schools-table-container">';
    echo '<table id="schools-table" class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th>BRIN nummer</th><th>Instellingsnaam</th><th>Edit</th><th>Delete</th>';
    echo '</tr></thead><tbody>';

    foreach ($schools as $school) {
        echo '<tr>';
        echo '<td>' . esc_html($school->brin_number) . '</td>';
        echo '<td>' . esc_html($school->name) . '</td>';
        echo '<td><a href="' . admin_url('admin.php?page=edit_school&id=' . $school->id) . '">Edit</a></td>';
        echo '<td><a href="' . admin_url('admin.php?page=schools&action=delete&school_id=' . $school->id) . '" onclick="return confirm(\'Are you sure you want to delete this school?\')">Delete</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>'; // Close schools-table-container

    echo '</div>'; // Close wrap

    ?>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

        <script>
            jQuery(document).ready(function($) {
                $("#schools-table").DataTable({
                    "paging": true, // Disable pagination if you have a small number of rows
                    "searching": true,
                    "ordering": true,
                    "info": true, // Disable table information
                });

            });
        </script>

    <?php
}



// Add a submenu page for adding a new School
function add_add_school_menu()
{
    add_submenu_page(
        null,
        'Add School',
        'Add School',
        'manage_options',
        'add_school',
        'display_add_school_page'
    );
}
add_action('admin_menu', 'add_add_school_menu');



// Function to display the form for adding a new school
function display_add_school_page()
{
    ?>
        <!-- Display the form for adding a new school -->
        <div class="wrap">
            <div class="main-top">
                <h2 class="main-title">Add New School</h2>
            </div>
            <!--  Form for adding a new school -->
            <form method="post" action="">
                <label for="school_name">School Name</label>
                <input type="text" name="school_name" id="school_name" required placeholder="Please search school name">
                <input type="submit" name="search_school" value="Search" class="page-title-action">
            </form>

            <?php

            // Display the selected school details
            if (isset($_POST['search_school'])) {
                handle_school_submission();
            }

            echo '</div>';
        }

        // Function to handle the school submission
        function handle_school_submission()
        {
            if (isset($_POST['search_school'])) {
                // echo 1; die;
                // Process the selected school
                $selected_school = sanitize_text_field($_POST['school_name']);
                // Perform API request to get detailed school data based on the selected school
                $api_url = 'https://onderwijsdata.duo.nl/api/3/action/datastore_search?resource_id=9801fdea-01bc-43cc-8e4e-3e03a2bbbbf8&q=' . urlencode($selected_school);
                // $response = wp_remote_get($api_url);
                $response = wp_remote_get($api_url, array('timeout' => 30));

                if (!is_wp_error($response)) {
                    $body = wp_remote_retrieve_body($response);
                    $data = json_decode($body, true);

                    // Display detailed school data
                    if (count($data['result']['records']) == 0) {
                        echo '<p>No data found for the selected school.<p>';
                    }

                    if (isset($data['result']['records'])) {
                        $school_data = $data['result']['records'];
                        display_detailed_school_data($school_data);
                    }
                } else {
                    echo '<p>Error fetching data from the API.</p>';
                }
            }
        }


        // Function to display detailed school data
        function display_detailed_school_data($school_data)
        {
            if ($school_data) {
                // print_r($school_data); die;
                echo '<h3 class="sec-heading">Detailed School Data</h3>';
                echo '<div class="res-wrap">';
                foreach ($school_data as $key => $value) {
                    # code...
                    echo '<ul class="search-add-school">';
                    echo '<li><strong>Basis Poort ID:</strong> ' . esc_html($value['_id']) . '</li>';
                    echo '<li><strong>Brin Number:</strong> ' . esc_html($value['BRIN NUMMER']) . '</li>';
                    echo '<li><strong>Instellings Naam:</strong> ' . esc_html($value['INSTELLINGSNAAM']) . '</li>';
                    echo '<li><strong>Plaatsnaam:</strong> ' . esc_html($value['PLAATSNAAM']) . '</li>';
                    echo '<li>';
                    echo '<a href="javascript:;" class="add-school-btn" data-school="' . esc_attr(json_encode($value)) . '">Add</a></li>';

                    // Add other school properties as needed
                    echo '</ul>';
                }
                echo '</div>';
            }
            ?>
            <script>
                jQuery(document).ready(function($) {
                    // Attach click event to the "Add" button
                    $('.add-school-btn').on('click', function() {
                        // Get the serialized JSON data from the data attribute
                        schoolData = $(this).data('school');
                        // Parse the JSON data

                        // console.log(schoolObject);
                        // Now, you can send the data to the server using AJAX
                        $.post(ajaxurl, {
                            action: 'add_school_from_search_listing',
                            // Include any additional data you want to send, for example:
                            schoolData: schoolData,
                        }, function(response) {
                            // Hide loading indicator

                            // Show a success or error message
                            window.location.href = 'admin.php?page=schools';

                        });
                    });
                });
            </script>
        <?php
        }

        function add_school_from_search_listing()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'schools';

            // Assuming 'schoolData' is a JSON string sent via POST
            $schoolData = $_POST['schoolData'];
            // print_r($schoolData); die;
            // If schoolData is not empty, insert it into the database
            if (!empty($schoolData)) {

                // Decode the JSON string into an associative array
                // $schoolDataArray = json_decode($schoolData, true);
                //    print_r($schoolData);
                //     die;
                // if ($schoolData) {

                // Define the data to be inserted
                $insert_data = array(
                    'brin_number' => isset($schoolData['BRIN NUMMER']) ? sanitize_text_field($schoolData['BRIN NUMMER']) : '',
                    'basispoort_id' => isset($schoolData['_id']) ? absint($schoolData['_id']) : 0,
                    'name' => isset($schoolData['INSTELLINGSNAAM']) ? sanitize_text_field($schoolData['INSTELLINGSNAAM']) : '',
                    'address' => isset($schoolData['STRAATNAAM']) ? $schoolData['STRAATNAAM'] . ' ' . $schoolData['HUISNUMMER-TOEVOEGING'] : '',
                    'postal_code' => isset($schoolData['POSTCODE']) ? sanitize_text_field($schoolData['POSTCODE']) : '',
                    'city' => isset($schoolData['PLAATSNAAM']) ? sanitize_text_field($schoolData['PLAATSNAAM']) : '', // Assuming PLAATSNAAM is the correct key
                    'municipal_number' => isset($schoolData['GEMEENTENUMMER']) ? sanitize_text_field($schoolData['GEMEENTENUMMER']) : '',
                    'municipal_name' => isset($schoolData['GEMEENTENAAM']) ? sanitize_text_field($schoolData['GEMEENTENAAM']) : '',
                    'denomination' => isset($schoolData['DENOMINATIE']) ? sanitize_text_field($schoolData['DENOMINATIE']) : '',
                    'phone' => isset($schoolData['TELEFOONNUMMER']) ? '0' . sanitize_text_field($schoolData['TELEFOONNUMMER']) : '', // Adding '0' before TELEFOONNUMMER
                    'website' => isset($schoolData['INTERNETADRES']) ? strtolower(sanitize_text_field($schoolData['INTERNETADRES'])) : '', // Convert to lowercase
                    // Add other fields based on your table structure
                );


                // Insert data into the table
                $wpdb->insert($table_name, $insert_data);

                wp_send_json_success(array('message' => 'School added successfully'));

                // }
            }

            // You might want to send a response back to the client if needed
            wp_die();
        }

        // Hook the AJAX action
        add_action('wp_ajax_add_school_from_search_listing', 'add_school_from_search_listing');
        add_action('wp_ajax_nopriv_add_school_from_search_listing', 'add_school_from_search_listing');


        //     // Add submenu page
        add_action('admin_menu', 'add_edit_school_menu');

        function add_edit_school_menu()
        {
            add_submenu_page(
                'learndash',
                'Edit School',
                'Edit School',
                'manage_options', // Replace with appropriate LearnDash capability
                'edit_school',
                'display_edit_school_page'
            );
        }

        //     // Display edit school page
        function display_edit_school_page()
        {
            // Check if the current user has the necessary capability (replace 'edit_courses' with the appropriate LearnDash capability)
            if (!current_user_can('edit_courses')) {
                wp_die('You do not have sufficient permissions to access this page.');
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'schools';
            $school_configurations = $wpdb->prefix . 'school_configurations';


            // Handle delete action
            if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['school_id'])) {
                // Check if the school has no links to other tables before deleting
                $school_id = intval($_GET['school_id']);
                // Implement your logic to check for links here
                // If there are no links, you can delete the school
                $wpdb->delete($table_name, array('id' => $school_id));
            }

            // Handle delete action
            if (isset($_GET['action']) && $_GET['action'] == 'sc_delete' && isset($_GET['d_id'])) {
                // Check if the school has no links to other tables before deleting

                $d_id = intval($_GET['d_id']);
                // Implement your logic to check for links here
                // If there are no links, you can delete the school
                $wpdb->delete($school_configurations, array('id' => $d_id));
            }


            // Fetch and display Schools data in a table
            $schools = $wpdb->get_results("SELECT * FROM $table_name");

            // Fetch and display Schools data in a table
            $school_configurations = $wpdb->get_results("SELECT * FROM $school_configurations where school_id =" . $_GET['id']);
            $school_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $school = get_school_data_by_id($school_id);



            echo '<a href="' . admin_url('admin.php?page=schools') . '" class="backtoschool"> ← Back to school</a>';

        ?>
            <div class="above-tabs">
                <span><strong>School Name</strong><?php echo esc_attr($school->name); ?></span>
                <span><strong>City</strong> <?php echo esc_attr($school->city); ?></span>
            </div>

            <?php
            // Tabs Section
            echo '<h2 class="nav-tab-wrapper">';
            echo '<a class="nav-tab " href="#basic-data">Basic Data</a>';
            echo '<a class="nav-tab" href="#school"> Assign Product</a>';
            echo '</h2>';

            // Basic Data Tab
            echo '<div id="basic-data" class="schools-tab-content">';

            // Add your page content, such as the Schools listing, here
            echo '<div class="wrap"><div class="main-top"><h2 class="main-title">School Detail</h2> </div>';
            // Display the Schools table

            // Add content for Basic Data tab as needed
            echo '<div class="wrap">';

            echo '<div class="schools-table-container">';
            // Display the form with populated fields
            echo '<div action="javascript:;" class="gen-form">';
            echo '<input type="hidden" name="school_id" id="school_id" value="' . esc_attr($school->id) . '" />';

            echo '<label for="name_contact">Name Contact:</label>';
            echo '<input type="text" name="name_contact" id="name_contact" value="' . esc_attr($school->name_contact) . '" />';

            echo '<label for="invoice_email_address">Invoice email address:</label>';
            echo '<input type="text"  name="invoice_email_address" id="invoice_email_address" value="' . esc_attr($school->invoice_email_address) . '" />';


            echo '<label for="brin_number">BRIN nummer:</label>';
            echo '<input type="text" readonly name="brin_number" value="' . esc_attr($school->brin_number) . '" />';

            echo '<label for="name">BasispoortID:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->basispoort_id) . '" />';


            echo '<label for="name">Straatnaam Huisnummer-Toevoeging :</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->address) . '" />';

            echo '<label for="name">Postcode:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->postal_code) . '" />';

            echo '<label for="name">Plaatsnaam:</label>';
            echo '<input type="text" readonly  name="name" value="' . esc_attr($school->city) . '" />';

            echo '<label for="name">Gemeentenummer:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->municipal_number) . '" />';

            echo '<label for="name">Gemeentenaam:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->municipal_name) . '" />';

            echo '<label for="name">Denominatie:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->denomination) . '" />';

            echo '<label for="name">Telefoonnummer:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->phone) . '" />';

            echo '<label for="name">Internetadres:</label>';
            echo '<input type="text" readonly name="name" value="' . esc_attr($school->website) . '" />';

            echo  '<input type="button" name="update_school" id="update_school_btn" value="Update School" class="page-title-action">';
            // Add other fields as needed
            ?>
            <div id="school-update-message" style="display:none"></div>
            <?php


            echo '</div>';
            echo '</div>'; // Close schools-table-container

            echo '</div>'; // Close wrap
            echo '</div>';
            echo '</div>';

            // School Tab
            echo '<div id="school" class="schools-tab-content ">';

            // Add your page content, such as the Schools listing, here
            echo '<div class="wrap"><div class="main-top"><h2 class="main-title"> Assign Product</h2> <a href="' . admin_url('admin.php?page=add_assigned_product&id=' . $_GET['id']) . '" class="page-title-action">Assign Product</a></div>';
            // Display the Schools table

            // Add content for Basic Data tab as needed
            echo '<div class="wrap">';

            echo '<div class="schools-table-container">';
            echo '<table id="asigned-product-table" class="wp-list-table widefat fixed striped gen-table">';
            echo '<thead><tr>';
            echo '<th>School Year</th> <th>Product</th>  <th>Quantity</th>  <th>Comments</th><th>Edit</th><th>Delete</th>';
            echo '</tr></thead><tbody>';

            foreach ($school_configurations as $school_configuration) {
                $bookval = $school_configuration->books == 0 ? "No" : "yes";
                $product_name = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}products where id = " . $school_configuration->product_id);
                $school_year_name = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}year_groups where id = " . $school_configuration->school_year_id);

                echo '<tr>';
                echo '<td>' . esc_html($school_year_name) . '</td>';
                echo '<td>' . esc_html($product_name) . '</td>';
                // echo '<td>' . $bookval . '</td>';
                echo '<td>' . esc_html($school_configuration->num_students) . '</td>';
                echo '<td>' . esc_html($school_configuration->comments) . '</td>';
                echo '<td><a href="' . admin_url('admin.php?page=basispoort-edit-assign_product&s_id=' . $_GET['id'] . '&id=' . $school_configuration->id) . '">Edit</a></td>';
                echo '<td><a href="' . admin_url('admin.php?page=edit_school&action=sc_delete&id=' . $_GET['id'] . '&d_id=' . $school_configuration->id) . '#school" onclick="return confirm(\'Are you sure you want to delete this assign product?\')">Delete</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div>'; // Close schools-table-container

            echo '</div>'; // Close wrap
            ?>
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
            <script>
                jQuery(document).ready(function($) {
                    $("#asigned-product-table").DataTable({
                        "paging": true, // Disable pagination if you have a small number of rows
                        "searching": true,
                        "ordering": true,
                        "info": true, // Disable table information
                    });
                });
            </script>
            <!-- Add this HTML structure where you want to display the form -->
            <?php

            // Assuming you have fetched data from the server or generated it in some way
            $school_years = ["2023-2024", "2024-2025", "2025-2026"];
            $year_groups = [1, 2, 3, 4, 5];
            // Fetch products from the Basispoort extension

            // Output the school year and year group dropdown options
            echo '<script>';
            echo '$(document).ready(function () {';
            echo 'var schoolYears = ' . json_encode($school_years) . ';';
            echo 'var yearGroups = ' . json_encode($year_groups) . ';';
            echo 'var products = ' . json_encode($products) . ';'; // Make sure to fetch products from the server
            echo 'populateDropdown("school-year-dropdown", schoolYears);';
            echo 'populateDropdown("year-group-dropdown", yearGroups);';
            echo 'populateDropdown("product-dropdown", products);';
            echo '});';

            // Helper function to populate dropdowns
            echo 'function populateDropdown(dropdownId, data) {';
            echo '$("#" + dropdownId).empty();';
            echo 'data.forEach(function (item) {';
            echo '$("#" + dropdownId).append("<option value=" + item + ">" + item + "</option>");';
            echo '});';
            echo '}';
            echo '</script>';

            echo '</div>';

            ?>
            <script>
                jQuery(document).ready(function($) {
                    $('#update_school_btn').on('click', function() {
                        // alert("Aslert");
                        school_id = $("#school_id").val();
                        name_contact = $("#name_contact").val();
                        invoice_email_address = $("#invoice_email_address").val();

                        // console.log(schoolObject);
                        // Now, you can send the data to the server using AJAX
                        $.post(ajaxurl, {
                            action: 'update_school',
                            school_id: school_id,
                            name_contact: name_contact,
                            invoice_email_address: invoice_email_address,
                        }, function(response) {
                            console.log("Response" + response.data.message);
                            $("#school-update-message").show().text(response.data.message);
                            // Hide loading indicator

                            // Show a success or error message
                            // window.location.href = 'admin.php?page=schools';

                        });
                    });
                });
            </script>
            <script>
                jQuery(document).ready(function($) {
                    $("#schools-table").DataTable({
                        "paging": true, // Disable pagination if you have a small number of rows
                        "searching": true,
                        "ordering": true,
                        "info": true, // Disable table information
                    });

                    // Handle tab switching
                    // Check if the URL contains #school
                    console.log(window.location.href.indexOf("#school"));
                    if (window.location.href.indexOf("#school") > -1) {
                        // Add 'nav-tab-active' class to the school tab
                        $('a[href="#school"]').addClass("nav-tab-active");

                        // Show the content of the school tab
                        $("#school").addClass("active");
                    } else {
                        // Remove the 'nav-tab-active' class from all tabs and hide their content
                        $(".nav-tab-wrapper a").removeClass("nav-tab-active");
                        $(".schools-tab-content").removeClass("active");

                        // Set the default tab to 'basic-data'
                        $('a[href="#basic-data"]').addClass("nav-tab-active");
                        $("#basic-data").addClass("active");
                    }

                    // Handle tab switching
                    $(".nav-tab-wrapper a").on("click", function(e) {
                        e.preventDefault();

                        // Remove the 'nav-tab-active' class from all tabs and hide their content
                        $(".nav-tab-wrapper a").removeClass("nav-tab-active");
                        $(".schools-tab-content").removeClass("active");

                        // Add the 'nav-tab-active' class to the clicked tab
                        $(this).addClass("nav-tab-active");

                        // Show the content of the clicked tab
                        var tabContentId = $(this).attr("href");
                        $(tabContentId).addClass("active");
                    });
                });
            </script>
            <style>
                /* Add your custom CSS styles here */
                .schools-tab-content {
                    display: none;
                }

                .schools-tab-content.active {
                    display: block;
                }
            </style>
        <?php
        }

        function get_school_data_by_id($school_id)
        {
            global $wpdb;
            // Your table name
            $table_name = $wpdb->prefix . 'schools';
            // Query to retrieve school data based on ID
            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $school_id);
            // Execute the query
            $school_data = $wpdb->get_row($query);
            return $school_data;
        }




        // Add Basispoort main menu item
        function add_basispoort_menu_item()
        {
            add_menu_page(
                'Basispoort',
                'Basispoort',
                'manage_options',
                'basispoort-menu',
                'basispoort_menu_page',
                'dashicons-admin-generic',
                30
            );
        }
        add_action('admin_menu', 'add_basispoort_menu_item');

        function basispoort_menu_page()
        {
            // echo "Test";
        }


        // Create submenu: Methods
        function add_methods_submenu()
        {
            add_submenu_page(
                'basispoort-menu',
                'Methods',
                'Methods',
                'manage_options',
                'basispoort-methods',
                'methods_listing_page'
            );
        }
        add_action('admin_menu', 'add_methods_submenu');

        // Methods listing page
        function methods_listing_page()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'methods';

            if (isset($_GET['action']) && $_GET['action'] == 'method_delete' && isset($_GET['method_id'])) {
                // Check if the school has no links to other tables before deleting
                $method_id = intval($_GET['method_id']);
                // Implement your logic to check for links here
                // If there are no links, you can delete the school
                $wpdb->delete($table_name, array('id' => $method_id));
            }
            echo '<div class="wrap"><div class="main-top"><h2 class="main-title">Methods</h2> <a href="' . admin_url('admin.php?page=basispoort-add-method') . '" class="page-title-action">Add New Method</a></div>';

            // Display a list of existing methods
            $methods = get_existing_methods();

            echo '<table class="wp-list-table widefat fixed striped gen-table" id="method-table">';
            echo '<thead><tr>';
            echo '<th>ID</th><th>Code</th><th>Name</th><th>URL</th><th>Icon</th><th>Edit</th><th>Delete</th>';
            echo '</tr></thead><tbody>';

            foreach ($methods as $method) {
                echo '<tr>';
                echo '<td>' . esc_html($method['id']) . '</td>';
                echo '<td>' . esc_html($method['code']) . '</td>';
                echo '<td>' . esc_html($method['name']) . '</td>';
                echo '<td>' . esc_html($method['url']) . '</td>';
                echo '<td><img src="' . esc_url($method['icon_url']) . '" width="20" height="20"></td>';
                echo '<td><a href="' . esc_url(admin_url('admin.php?page=basispoort-edit-methods&id=' . $method['id'])) . '">Edit</a></td>';
                echo '<td><a href="' . admin_url('admin.php?page=basispoort-methods&action=method_delete&method_id=' . $method['id']) . '" onclick="return confirm(\'Are you sure you want to delete this method?\')">Delete</a></td>';


                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div>';
        ?>
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

            <script>
                jQuery(document).ready(function() {
                    jQuery("#method-table").DataTable({
                        "paging": true, // Disable pagination if you have a small number of rows
                        "searching": true,
                        "ordering": true,
                        "info": true, // Disable table information
                    });
                });
            </script>
        <?php
        }

        // Function to get existing methods
        function get_existing_methods()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'methods';
            return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        }

        // Create submenu: Add Method
        function add_add_method_submenu()
        {
            add_submenu_page(
                null,
                'Add Method',
                'Add Method',
                'manage_options',
                'basispoort-add-method',
                'add_method_page'
            );
        }
        add_action('admin_menu', 'add_add_method_submenu');

        // Function to handle SVG icon upload
        function handle_svg_icon_upload()
        {
            if ($_FILES['method-icon']['error'] === UPLOAD_ERR_OK) {
                $file_name = sanitize_file_name($_FILES['method-icon']['name']);
                $file_tmp = $_FILES['method-icon']['tmp_name'];
                $file_type = $_FILES['method-icon']['type'];

                // Check if the file type is SVG
                if ($file_type === 'image/svg+xml') {
                    $upload_dir = wp_upload_dir();
                    $icon_url = $upload_dir['url'] . '/' . $file_name;

                    // Move the uploaded file to the uploads directory
                    move_uploaded_file($file_tmp, $upload_dir['path'] . '/' . $file_name);

                    return $icon_url;
                } else {
                    // Handle non-SVG file type error
                    echo 'Error: Only SVG files are allowed.';
                }
            }

            return '';
        }


        // Function to save method data to the database
        function save_method_data($code, $name, $url, $icon_url, $associated_products = array())
        {
            // print_r($associated_products); die;
            global $wpdb;
            $table_name = $wpdb->prefix . 'methods';

            // Check if the code is unique
            $existing_method = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE code = %s", $code), ARRAY_A);

            if ($existing_method) {
                // Code is not unique, handle accordingly (display error, return, etc.)
                return false;
            }

            // Add the new method
            $wpdb->insert(
                $table_name,
                array(
                    'code' => $code,
                    'name' => $name,
                    'url' => $url,
                    'icon_url' => $icon_url,
                ),
                array('%s', '%s', '%s', '%s')
            );
            $method_id = $wpdb->insert_id;

            // Associate products with the method
            update_post_meta($method_id, 'associated_products', $associated_products);

            return $method_id; // Return the ID of the added method

            // return true; // Method data successfully saved
        }

        // Add Method page content
        function add_method_page()
        {
            echo '<div class="wrap">';

            echo ' <div class="main-top">
                <h2 class="main-title">Add Method</h2>
            </div>';

            // Display a form to add methods
            echo '<form method="post" action="" enctype="multipart/form-data" class="gen-form">';
            echo '<label for="method-code">Code:</label>';
            echo '<input type="text" name="method-code" required>';
            echo '<label for="method-name">Name:</label>';
            echo '<input type="text" name="method-name" required>';
            echo '<label for="method-url">URL:</label>';
            echo '<input type="text" name="method-url" required>';
            echo '<label for="method-icon">Icon (SVG only):</label>';
            echo '<input type="file" name="method-icon" accept=".svg" required>';

            // Add section to associate products with the method
            echo '<label for="associated-products">Associated Products:</label>';
            global $wpdb;
            $products_table = $wpdb->prefix . 'products';
            // Fetch all year groups
            $products_datas = $wpdb->get_results("SELECT * FROM $products_table");
            // print_r($products_datas);
            // die;

            echo '<select name="associated-products[]" multiple>'; // Allow selecting multiple products

            foreach ($products_datas as $product) {
                echo '<option value="' . esc_attr($product->id) . '">' . esc_html($product->name) . '</option>';
            }

            echo '</select>';


            echo '<input type="submit" name="add-method" value="Add Method" class="page-title-action">';
            echo '</form>';

            // Handle form submission
            if (isset($_POST['add-method'])) {
                // Add method logic here
                $code = sanitize_text_field($_POST['method-code']);
                $name = sanitize_text_field($_POST['method-name']);
                $url = sanitize_text_field($_POST['method-url']);

                // Handle file upload for icon
                $icon_url = handle_svg_icon_upload();

                // Save method data to the database or perform other necessary actions
                // save_method_data($code, $name, $url, $icon_url);

                $associated_products = isset($_POST['associated-products']) ? array_map('intval', $_POST['associated-products']) : array();
                // print_r($associated_products); die;
                $method_id = save_method_data($code, $name, $url, $icon_url, $associated_products);


                // Redirect back to the listing page after adding a method
                print('<script>window.location.href="admin.php?page=basispoort-methods"</script>');

                // wp_redirect(admin_url('admin.php?page=basispoort-methods'));
                exit();
            }

            echo '</div>';
        }

        // Create submenu: Products
        function add_products_submenu()
        {
            add_submenu_page(
                'basispoort-menu',
                'Products',
                'Products',
                'manage_options',
                'basispoort-products',
                'products_listing_page'
            );
        }
        add_action('admin_menu', 'add_products_submenu');

        // Products listing page
        function products_listing_page()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'products';

            if (isset($_GET['action']) && $_GET['action'] == 'product_delete' && isset($_GET['product_id'])) {
                // Check if the school has no links to other tables before deleting
                $product_id = intval($_GET['product_id']);
                // Implement your logic to check for links here
                // If there are no links, you can delete the school
                $wpdb->delete($table_name, array('id' => $product_id));
            }

            echo '<div class="wrap"><div class="main-top"><h2 class="main-title">Products</h2> <a href="' . admin_url('admin.php?page=basispoort-add-product') . '" class="page-title-action">Add New Product</a></div>';

            // Display a list of existing products
            $products = get_existing_products();

            echo '<div class="table-responsive">';
            echo '<table class="display  wp-list-table widefat fixed striped gen-table" id="product-table" style="width:100%"> ';
            echo '<thead><tr>';
            echo '<th>Code</th><th>Name</th><th>URL</th><th width="150">Is Physical Product</th><th>Price</th><th  width="150">VAT Percentage</th><th>Icon</th><th>Action</th>';
            echo '</tr></thead><tbody>';

            // Assuming you have a list of product IDs from wp_school_configurations
            $configured_product_ids = array(); // Replace this with actual code to fetch product IDs

            // Example of fetching product IDs
            global $wpdb;
            $table_name_configurations = $wpdb->prefix . 'school_configurations';
            $configured_product_ids = $wpdb->get_col("SELECT DISTINCT product_id FROM $table_name_configurations");

            foreach ($products as $product) {
                $product_id = $product['id'];

                echo '<tr>';
                // Check if product ID exists in wp_school_configurations
                // echo '<td>' . esc_html($product['id']) . '</td>';
                echo '<td>' . esc_html($product['code']) . '</td>';
                echo '<td>' . esc_html($product['name']) . '</td>';
                echo '<td>' . esc_html($product['url']) . '</td>';
                echo '<td>' . esc_html($product['is_physical_product'] == 0 ? "No" : "Yes") . '</td>';
                echo '<td>' . esc_html($product['Price']) . '</td>';
                echo '<td>' . esc_html($product['vat_percentage']) . '</td>';
                echo '<td><img src="' . esc_url($product['icon_url']) . '" width="20" height="20"></td>';
                echo '<td><a href="' . esc_url(admin_url('admin.php?page=basispoort-edit-product&id=' . $product['id'])) . '">Edit &nbsp;</a>';

                if (!in_array($product_id, $configured_product_ids)) {
                    echo '<a href="' . admin_url('admin.php?page=basispoort-products&action=product_delete&product_id=' . $product['id']) . '" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a></td>';
                } else {
                    echo '';
                }
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div>';
            echo '</div>';
        ?>
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

            <script>
                jQuery(document).ready(function() {
                    jQuery("#product-table").DataTable({
                        "paging": true, // Disable pagination if you have a small number of rows
                        "searching": true,
                        "ordering": false,
                        "info": true, // Disable table information
                        // "scrollX": true
                    });
                });
            </script>
        <?php
        }

        // Function to get existing products
        function get_existing_products()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'products';
            return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        }

        // Create submenu: Add Products
        function add_add_product_submenu()
        {
            add_submenu_page(
                null,
                'Add Product',
                'Add Product',
                'manage_options',
                'basispoort-add-product',
                'add_product_page'
            );
        }
        add_action('admin_menu', 'add_add_product_submenu');


        // Function to save method data to the database
        function save_product_data($code, $name, $url, $icon_url, $is_physical_product, $price, $vat_percentage)
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'products';

            // Check if the code is unique
            $existing_product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE code = %s", $code), ARRAY_A);

            if ($existing_product) {
                // Code is not unique, handle accordingly (display error, return, etc.)
                return false;
            }

            // Add the new product
            $wpdb->insert(
                $table_name,
                array(
                    'code' => $code,
                    'name' => $name,
                    'url' => $url,
                    'is_physical_product' => $is_physical_product,
                    'Price' => $price,
                    'vat_percentage' => $vat_percentage,
                    'icon_url' => $icon_url,
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
            );

            return true; // Product data successfully saved
        }


        // Add Product page content
        function add_product_page()
        {
            echo '<div class="wrap">';
            echo ' <div class="main-top">
                <h2 class="main-title">Add Product</h2>
            </div>';

            // Display a form to add product
            echo '<form method="post" action="" enctype="multipart/form-data" class="gen-form">';
            echo '<label for="product-code">Code:</label>';
            echo '<input type="text" name="product-code" required>';
            echo '<label for="product-name">Name:</label>';
            echo '<input type="text" name="product-name" required>';

            echo '<label for="product-url">URL:</label>';
            echo '<input type="text" name="product-url" required>';

            echo '<label for="is_physical_product">Is Physical product:</label>';
        ?>
            <select id="is_physical_product" name="is_physical_product" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
            <?php
            echo '<label for="price">Price:</label>';
            echo '<input type="text" name="price" required>';
            echo '<label for="vat_percentage">VAT percentage:</label>';
            ?>
            <select id="vat_percentage" name="vat_percentage" required>
                <option value="0">0</option>
                <option value="9">9</option>
                <option value="21">21</option>
            </select>
            <?php


            echo '<label for="product-icon">Icon (SVG only):</label>';
            echo '<input type="file" name="product-icon" accept=".svg" required>';
            echo '<input type="submit" name="add-product" value="Add Product" class="page-title-action">';
            echo '</form>';

            // Handle form submission
            if (isset($_POST['add-product'])) {
                // Add product logic here
                $code = sanitize_text_field($_POST['product-code']);
                $name = sanitize_text_field($_POST['product-name']);
                $url = sanitize_text_field($_POST['product-url']);
                $is_physical_product = sanitize_text_field($_POST['is_physical_product']);
                $price = sanitize_text_field($_POST['price']);
                $vat_percentage = sanitize_text_field($_POST['vat_percentage']);

                // Handle file upload for icon
                $icon_url = handle_svg_icon_upload_product();

                // Save product data to the database or perform other necessary actions
                save_product_data($code, $name, $url, $icon_url, $is_physical_product, $price, $vat_percentage);

                // Redirect back to the listing page after adding a product
                print('<script>window.location.href="admin.php?page=basispoort-products"</script>');
                // wp_redirect(admin_url('admin.php?page=basispoort-products'));
                exit();
            }

            echo '</div>';
        }

        // Function to handle SVG icon upload
        function handle_svg_icon_upload_product()
        {
            // echo $_FILES['product-icon']['error']; die;
            if ($_FILES['product-icon']['error'] === UPLOAD_ERR_OK) {
                $file_name = sanitize_file_name($_FILES['product-icon']['name']);
                $file_tmp = $_FILES['product-icon']['tmp_name'];
                $file_type = $_FILES['product-icon']['type'];

                // Check if the file type is SVG
                if ($file_type === 'image/svg+xml') {
                    $upload_dir = wp_upload_dir();
                    $icon_url = $upload_dir['url'] . '/' . $file_name;

                    // Move the uploaded file to the uploads directory
                    move_uploaded_file($file_tmp, $upload_dir['path'] . '/' . $file_name);

                    return $icon_url;
                } else {
                    // Handle non-SVG file type error
                    echo 'Error: Only SVG files are allowed.';
                }
            }

            return '';
        }



        // Create submenu: Common Settings
        function add_common_settings_submenu()
        {
            add_submenu_page(
                'basispoort-menu',
                'Common Settings',
                'Common Settings',
                'manage_options',
                'basispoort-common-settings',
                'common_settings_page'
            );
        }
        add_action('admin_menu', 'add_common_settings_submenu');

        // Callback function to display the Common Settings page
        function common_settings_page()
        {
            ?>
            <div class="wrap">
                <h1>Common Settings</h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('common_settings_group');
                    do_settings_sections('common_settings');
                    submit_button();
                    ?>
                </form>
            </div>
        <?php
        }

        // Register Common Settings
        function common_settings_init()
        {
            register_setting(
                'common_settings_group', // Option group
                'active_environment'     // Option name
            );

            add_settings_section(
                'common_settings_section', // ID
                'Common Settings',         // Title
                'common_settings_section_callback', // Callback function to display section
                'common_settings'          // Page
            );

            add_settings_field(
                'environments',           // ID
                'Active Environment',           // Title
                'environments_callback',  // Callback function to display field
                'common_settings',        // Page
                'common_settings_section' // Section
            );
        }
        add_action('admin_init', 'common_settings_init');

        // Callback function to display the section
        function common_settings_section_callback()
        {
            echo '<p>Configure common settings here.</p>';
        }

        // Callback function to display the environments field
        function environments_callback()
        {
            $active_environment = get_option('active_environment', 'test-rest.basispoort.nl');
            $environments = array(
                'test-rest.basispoort.nl' => 'Test',
                'acceptatie-rest.basispoort.nl' => 'Acceptance',
                'staging-rest.basispoort.nl' => 'Staging',
                'rest.basispoort.nl' => 'Production',
            );

            echo '<select name="active_environment">';
            foreach ($environments as $url => $label) {
                $selected = ($active_environment == $url) ? 'selected' : '';
                echo "<option value='$url' $selected>$label</option>";
            }
            echo '</select>';
        }



        function add_assigned_product_menu()
        {
            add_submenu_page(
                null,
                'Add Assigned Product',
                'Add Assigned Product',
                'manage_options',
                'add_assigned_product',
                'display_assigned_product_page'
            );
        }
        add_action('admin_menu', 'add_assigned_product_menu');



        // Function to display the form for adding a new school
        function display_assigned_product_page()
        {
            $school_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        ?>
            <!-- Display the form for adding a new school -->
            <div class="wrap">
                <div class="main-top">
                    <h2 class="main-title">Add Assigned Product</h2>
                </div>
                <!--  Form for adding a new school -->
                <form method="post" action="">
                    <div id="school-year-form" class="gen-form">
                        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
                        <label>School Year:</label>
                        <?php
                        global $wpdb;
                        $year_groups_table = $wpdb->prefix . 'year_groups';

                        // Fetch all year groups
                        $year_groups = $wpdb->get_results("SELECT * FROM $year_groups_table ORDER BY sort_order");
                        ?>
                        <select id="school-year-dropdown" name="school_year_id">
                            <!-- Populate with available school years dynamically -->
                            <!-- You can do this in PHP when generating the page -->
                            <?php foreach ($year_groups as $year_group) : ?>
                                <option value="<?php echo esc_html($year_group->id); ?>"><?php echo esc_html($year_group->name); ?></option>
                            <?php endforeach; ?>
                            <!-- Add more options dynamically -->
                        </select>

                        <label>Product:</label>
                        <?php
                        global $wpdb;
                        $products_table = $wpdb->prefix . 'products';

                        // Fetch all year groups
                        $products_datas = $wpdb->get_results("SELECT * FROM $products_table");
                        ?>
                        <select id="product-dropdown" name="product_id">
                            <!-- Populate with available products dynamically -->
                            <!-- You can fetch this data from the server using AJAX -->
                            <!-- Populate using a PHP loop if needed -->
                            <?php foreach ($products_datas as $products_data) : ?>
                                <option value="<?php echo esc_html($products_data->id); ?>"><?php echo esc_html($products_data->name); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- <label>Books?</label>
                        <select id="books-dropdown" name="books">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select> -->

                        <label>Quantity:</label>
                        <input type="number" id="students-input" name="number_of_students" required>

                        <label>Comments:</label>
                        <textarea id="comments-textarea" name="comments"></textarea>
                        <input type="submit" name="add_assigned_product" id="add-new-assigned-product-button" class="page-title-action" value="Add New Assigned Product" style="margin-top:15px;">
                    </div>
                </form>

                <?php

                // Handle form submission
                if (isset($_POST['add_assigned_product'])) {
                    // Add product logic here
                    $school_id = sanitize_text_field($_POST['school_id']);
                    $school_year_id = sanitize_text_field($_POST['school_year_id']);
                    $product_id = sanitize_text_field($_POST['product_id']);
                    // $books = sanitize_text_field($_POST['books']);
                    $number_of_students = sanitize_text_field($_POST['number_of_students']);
                    $comments = sanitize_text_field($_POST['comments']);


                    // Save product data to the database or perform other necessary actions
                    save_assigned_product_data($school_id, $school_year_id,  $product_id,  $number_of_students, $comments);

                    // Redirect back to the listing page after adding a product
                    print('<script>window.location.href="admin.php?page=edit_school&id=' . $school_id . '#school"</script>');
                    // wp_redirect(admin_url('admin.php?page=basispoort-products'));
                    exit();
                }

                echo '</div>';
            }

            // Function to save method data to the database
            function
            save_assigned_product_data($school_id, $school_year_id, $product_id,  $number_of_students, $comments)
            {
                global $wpdb;
                $table_name = $wpdb->prefix . 'school_configurations';

                // Add the new product
                $wpdb->insert(
                    $table_name,
                    array(
                        'school_id' => $school_id,
                        'school_year_id' => $school_year_id,
                        'product_id' => $product_id,
                        // 'books' => $books,
                        'num_students' => $number_of_students,
                        'comments' => $comments
                    ),
                    array('%s', '%s', '%s',  '%s', '%s')
                );

                return true; // Product data successfully saved
            }



            // Add submenu page
            function add_edit_product_page()
            {
                add_submenu_page(
                    'basispoort-menu',          // Parent menu slug
                    'Edit Product',              // Page title
                    'Edit Product',              // Menu title
                    'manage_options',            // Capability required
                    'basispoort-edit-product',   // Page slug
                    'edit_product_page_callback'  // Callback function to display the page content
                );
            }


            add_action('admin_menu', 'add_edit_product_page');

            // Function to retrieve product details by ID
            function get_product_details($product_id)
            {
                global $wpdb;

                // Replace 'your_products_table' with your actual table name
                $table_name = $wpdb->prefix . 'products';

                $product = $wpdb->get_row(
                    $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $product_id),
                    ARRAY_A
                );

                return $product;
            }



            // Callback function to display the edit product page
            function edit_product_page_callback()
            {
                // Retrieve product ID from the query parameter
                $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                // Retrieve product details based on the ID
                $product = get_product_details($product_id);

                // Display the edit form
                ?>
                <div class="wrap">
                    <div class="main-top">
                        <h2 class="main-title">Edit Product</h2>
                    </div>

                    <?php
                    if ($product) {
                    ?>
                        <form method="post" action="" enctype="multipart/form-data" class="gen-form">
                            <input type="hidden" name="product-id" value="<?php echo esc_attr($product['id']); ?>">
                            <label for="product-code">Code:</label>
                            <input type="text" name="product-code" value="<?php echo esc_attr($product['code']); ?>" required>
                            <label for="product-name">Name:</label>
                            <input type="text" name="product-name" value="<?php echo esc_attr($product['name']); ?>" required>
                            <label for="product-url">URL:</label>
                            <input type="text" name="product-url" value="<?php echo esc_attr($product['url']); ?>" required>

                            <label for="is_physical_product">Is Physical product:</label>
                            <select id="is_physical_product" name="is_physical_product" required>
                                <option value="1" <?php echo $product['is_physical_product'] == 1 ? "selected" : ""; ?>>Yes</option>
                                <option value="0" <?php echo $product['is_physical_product'] == 0 ? "selected" : ""; ?>>No</option>
                            </select>

                            <label for="price">Price:</label>
                            <input type="text" name="price" required value="<?php echo esc_attr($product['Price']); ?>">

                            <label for="vat_percentage">VAT percentage:</label>
                            <select id="vat_percentage" name="vat_percentage" required>
                                <option value="0" <?php echo $product['vat_percentage'] == 0 ? "selected" : ""; ?>>0</option>
                                <option value="9" <?php echo $product['vat_percentage'] == 9 ? "selected" : ""; ?>>9</option>
                                <option value="21" <?php echo $product['vat_percentage'] == 21 ? "selected" : ""; ?>>21</option>
                            </select>


                            <label for="product-icon">Icon (SVG only):</label>
                            <input type="file" name="product-icon" accept=".svg">
                            <input type="submit" name="update-product" value="Update Product" class="page-title-action">
                        </form>
                    <?php
                    } else {
                        echo '<p>Product not found.</p>';
                    }
                }


                if (isset($_POST['update-product'])) {
                    // Get form data
                    $product_id = isset($_POST['product-id']) ? intval($_POST['product-id']) : 0;
                    $code = sanitize_text_field($_POST['product-code']);
                    $name = sanitize_text_field($_POST['product-name']);
                    $url = sanitize_text_field($_POST['product-url']);
                    $is_physical_product = sanitize_text_field($_POST['is_physical_product']);
                    $price = sanitize_text_field($_POST['price']);
                    $vat_percentage = sanitize_text_field($_POST['vat_percentage']);

                    // Handle file upload for icon
                    $icon_url = handle_svg_icon_upload_product();

                    // Call the update function
                    update_product_data($product_id, $code, $name, $url, $is_physical_product, $price, $vat_percentage, $icon_url);
                }

                // Function to update product data
                function update_product_data($product_id, $code, $name, $url, $is_physical_product, $price, $vat_percentage, $icon_url)
                {
                    global $wpdb;

                    // Replace 'your_products_table' with your actual table name
                    $table_name = $wpdb->prefix . 'products';

                    // Get the existing product data
                    $existing_product = get_product_details($product_id);

                    // Use the existing image URL if a new image is not uploaded
                    $icon_url = !empty($icon_url) ? $icon_url : $existing_product['icon_url'];


                    $result = $wpdb->update(
                        $table_name,
                        array(
                            'code' => $code, 'name' => $name, 'url' => $url,
                            'is_physical_product' => $is_physical_product,
                            'price' => $price,
                            'vat_percentage' => $vat_percentage,
                            'icon_url' => $icon_url
                        ),
                        array('id' => $product_id)
                    );
                    // echo 1; die;
                    // Add your error handling or success notification logic here
                    if ($result !== false) {
                        // echo '<div class="updated"><p>Product updated successfully.</p></div>';
                        // Redirect to the listing page
                        // wp_redirect(admin_url('admin.php?page=basispoort-products'));
                        print('<script>window.location.href="admin.php?page=basispoort-products"</script>');
                        // exit;
                    } else {
                        echo '<div class="error"><p>Failed to update product.</p></div>';
                    }
                }







                // Add submenu page
                function add_edit_method_page()
                {
                    add_submenu_page(
                        'basispoort-menu',          // Parent menu slug
                        'Edit Method',              // Page title
                        'Edit Method',              // Menu title
                        'manage_options',            // Capability required
                        'basispoort-edit-methods',   // Page slug
                        'edit_method_page_callback'  // Callback function to display the page content
                    );
                }


                add_action('admin_menu', 'add_edit_method_page');

                // Function to retrieve method details by ID
                function get_method_details($method_id)
                {
                    global $wpdb;

                    // Replace 'your_method_table' with your actual table name
                    $table_name = $wpdb->prefix . 'methods';

                    $method = $wpdb->get_row(
                        $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $method_id),
                        ARRAY_A
                    );

                    return $method;
                }



                // Callback function to display the edit product page
                function edit_method_page_callback()
                {
                    // Retrieve method ID from the query parameter
                    $method_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                    // Retrieve method details based on the ID
                    $method = get_method_details($method_id);
                    // Retrieve existing associated products
                    $associated_products = get_associated_products($method_id);
                    $associated_products = unserialize($associated_products[0]);


                    // Retrieve all available products
                    $all_products = get_existing_products();
                    // print_r($associated_products);
                    // print_r($all_products);

                    // Display the edit form
                    ?>
                    <div class="wrap">
                        <div class="main-top">
                            <h2 class="main-title">Edit Method</h2>
                        </div>

                        <?php
                        if ($method) {
                        ?>
                            <form method="post" action="" enctype="multipart/form-data" class="gen-form">
                                <input type="hidden" name="method-id" value="<?php echo esc_attr($method['id']); ?>">
                                <label for="method-code">Code:</label>
                                <input type="text" name="method-code" value="<?php echo esc_attr($method['code']); ?>" required>
                                <label for="method-name">Name:</label>
                                <input type="text" name="method-name" value="<?php echo esc_attr($method['name']); ?>" required>
                                <label for="method-url">URL:</label>
                                <input type="text" name="method-url" value="<?php echo esc_attr($method['url']); ?>" required>
                                <label for="method-icon">Icon (SVG only):</label>
                                <input type="file" name="method-icon" accept=".svg">

                                <?php

                                // Section for selecting associated products
                                echo '<label for="associated-products">Associated Products:</label>';
                                echo '<select multiple name="associated-products[]">';
                                foreach ($all_products as $product) {
                                    echo '<option value="' . esc_attr($product['id']) . '"';
                                    if (in_array($product['id'], $associated_products)) {
                                        echo ' selected';
                                    }
                                    echo '>' . esc_html($product['name']) . '</option>';
                                }
                                echo '</select>';
                                ?>
                                <input type="submit" name="update-method" value="Update Method" class="page-title-action">
                            </form>
                        <?php
                        } else {
                            echo '<p>Method not found.</p>';
                        }
                    }


                    if (isset($_POST['update-method'])) {
                        // Get form data
                        $method_id = isset($_POST['method-id']) ? intval($_POST['method-id']) : 0;
                        $code = sanitize_text_field($_POST['method-code']);
                        $name = sanitize_text_field($_POST['method-name']);
                        $url = sanitize_text_field($_POST['method-url']);

                        // Handle file upload for icon
                        $icon_url = handle_svg_icon_upload();

                        // Call the update function
                        $associated_products = isset($_POST['associated-products']) ? array_map('intval', $_POST['associated-products']) : array();
                        update_method_data($method_id, $code, $name, $url, $icon_url, $associated_products);

                        // update_method_data($method_id, $code, $name, $url, $icon_url);
                    }

                    // Function to retrieve associated products for a method
                    function get_associated_products($method_id)
                    {
                        global $wpdb;

                        // Replace 'your_methods_table' with your actual table name
                        $methods_table = $wpdb->prefix . 'methods';

                        $associated_products = $wpdb->get_col(
                            $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = 'associated_products'", $method_id)
                        );

                        return $associated_products;
                    }


                    // Function to update method data
                    function update_method_data($method_id, $code, $name, $url, $icon_url, $associated_products = array())
                    {
                        global $wpdb;

                        // Replace 'your_methods_table' with your actual table name
                        $table_name = $wpdb->prefix . 'methods';
                        $methods_products_table = $wpdb->prefix . 'products';


                        // Get the existing method data
                        $existing_method = get_method_details($method_id);

                        // Use the existing image URL if a new image is not uploaded
                        $icon_url = !empty($icon_url) ? $icon_url : $existing_method['icon_url'];


                        $result = $wpdb->update(
                            $table_name,
                            array('code' => $code, 'name' => $name, 'url' => $url, 'icon_url' => $icon_url),
                            array('id' => $method_id)
                        );

                        update_post_meta($method_id, 'associated_products', $associated_products);

                        // echo 1; die;
                        // Add your error handling or success notification logic here
                        if ($result !== false) {
                            // echo '<div class="updated"><p>method updated successfully.</p></div>';
                            // Redirect to the listing page
                            // wp_redirect(admin_url('admin.php?page=basispoort-methods'));
                            print('<script>window.location.href="admin.php?page=basispoort-methods"</script>');
                            // exit;
                        } else {
                            echo '<div class="error"><p>Failed to update method.</p></div>';
                        }
                    }








                    // Add submenu page
                    function add_edit_assign_product_page()
                    {
                        add_submenu_page(
                            'basispoort-menu',          // Parent menu slug
                            'Edit Assign Product',              // Page title
                            'Edit assign Product',              // Menu title
                            'manage_options',            // Capability required
                            'basispoort-edit-assign_product',   // Page slug
                            'edit_assign_product_page_callback'  // Callback function to display the page content
                        );
                    }


                    add_action('admin_menu', 'add_edit_assign_product_page');

                    // Function to retrieve assign_product details by ID
                    function get_assign_product_details($assign_product_id)
                    {
                        global $wpdb;

                        // Replace 'your_assign_product_table' with your actual table name
                        $table_name = $wpdb->prefix . 'school_configurations';

                        $assign_product = $wpdb->get_row(
                            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $assign_product_id),
                            ARRAY_A
                        );

                        return $assign_product;
                    }



                    // Callback function to display the edit product page
                    function edit_assign_product_page_callback()
                    {
                        // Retrieve assign_product ID from the query parameter
                        $assign_product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                        $school_id = isset($_GET['s_id']) ? intval($_GET['s_id']) : 0;

                        // Retrieve assign_product details based on the ID
                        $assign_product = get_assign_product_details($assign_product_id);

                        // Display the edit form
                        ?>
                        <div class="wrap">
                            <div class="main-top">
                                <h2 class="main-title">Edit Assign Product</h2>
                            </div>

                            <?php
                            // print_r($assign_product);
                            if ($assign_product) {
                            ?>
                                <form method="post" action="">
                                    <div id="school-year-form" class="gen-form">
                                        <input type="hidden" name="assign_product_id" value="<?php echo esc_attr($assign_product['id']); ?>">
                                        <input type="hidden" name="school_id" value="<?php echo esc_attr($school_id); ?>">

                                        <label>School Year:</label>
                                        <?php
                                        global $wpdb;
                                        $year_groups_table = $wpdb->prefix . 'year_groups';

                                        // Fetch all year groups
                                        $year_groups = $wpdb->get_results("SELECT * FROM $year_groups_table ORDER BY sort_order");
                                        ?>
                                        <select id="school-year-dropdown" name="school_year_id">
                                            <!-- Populate with available school years dynamically -->
                                            <!-- You can do this in PHP when generating the page -->
                                            <?php foreach ($year_groups as $year_group) : ?>
                                                <option <?php echo $year_group->id == $assign_product['school_year_id'] ? "Selected" : ''; ?> value="<?php echo esc_html($year_group->id); ?>"><?php echo esc_html($year_group->name); ?></option>
                                            <?php endforeach; ?>
                                            <!-- Add more options dynamically -->
                                        </select>

                                        <label>Product:</label>
                                        <?php
                                        global $wpdb;
                                        $products_table = $wpdb->prefix . 'products';

                                        // Fetch all year groups
                                        $products_datas = $wpdb->get_results("SELECT * FROM $products_table");
                                        ?>
                                        <select id="product-dropdown" name="product_id">
                                            <!-- Populate with available products dynamically -->
                                            <!-- You can fetch this data from the server using AJAX -->
                                            <!-- Populate using a PHP loop if needed -->
                                            <?php foreach ($products_datas as $products_data) : ?>
                                                <option <?php echo $products_data->id == $assign_product['product_id'] ? "Selected" : ''; ?> value="<?php echo esc_html($products_data->id); ?>"><?php echo esc_html($products_data->name); ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                        <!-- <label>Books?</label>
                                        <select id="books-dropdown" name="books">
                                            <option value="1" <?php //echo $assign_product->books == 1 ? "Selected" : ''; 
                                                                ?>>Yes</option>
                                            <option value="0" <?php //echo $assign_product->books == 0 ? "Selected" : ''; 
                                                                ?>>No</option>
                                        </select> -->

                                        <label>Quantity:</label>
                                        <input required type="number" id="students-input" name="number_of_students" value="<?php echo esc_attr($assign_product['num_students']); ?>">

                                        <label>Comments:</label>
                                        <textarea id="comments-textarea" name="comments"><?php echo esc_attr($assign_product['comments']); ?></textarea>
                                        <input type="submit" name="update_assigned_product" id="update-new-assigned-product-button" class="page-title-action" value="Update Assign Product" style="margin-top:15px;">
                                    </div>
                                </form>
                            <?php
                            } else {
                                echo '<p>Assign Product not found.</p>';
                            }
                        }


                        if (isset($_POST['update_assigned_product'])) {
                            $assign_product_id = isset($_POST['assign_product_id']) ? intval($_POST['assign_product_id']) : 0;
                            $school_id = isset($_POST['school_id']) ? intval($_POST['school_id']) : 0;

                            // Get form data

                            $school_year_id = sanitize_text_field($_POST['school_year_id']);
                            $product_id = sanitize_text_field($_POST['product_id']);
                            // $books = sanitize_text_field($_POST['books']);
                            $number_of_students = sanitize_text_field($_POST['number_of_students']);
                            $comments = sanitize_text_field($_POST['comments']);

                            // die;
                            // Handle file upload for icon

                            // Call the update function
                            update_assign_product_date($assign_product_id, $school_id, $school_year_id,  $product_id, $number_of_students, $comments);
                        }

                        // Function to update method data
                        function update_assign_product_date($assign_product_id, $school_id,  $school_year_id,  $product_id,  $number_of_students, $comments)
                        {
                            global $wpdb;

                            // Replace 'your_methods_table' with your actual table name
                            $table_name = $wpdb->prefix . 'school_configurations';



                            $result = $wpdb->update(
                                $table_name,
                                array(
                                    'school_year_id' => $school_year_id,
                                    'product_id' => $product_id,
                                    'books' => '',
                                    'num_students' => $number_of_students,
                                    'comments' => $comments
                                ),
                                array('id' => $assign_product_id)
                            );
                            // echo 1; die;
                            // Add your error handling or success notification logic here
                            if ($result !== false) {
                                // echo '<div class="updated"><p>method updated successfully.</p></div>';
                                // Redirect to the listing page
                                // wp_redirect(admin_url('admin.php?page=basispoort-methods'));
                                print('<script>window.location.href="admin.php?page=edit_school&id=' . $school_id . '#school"</script>');

                                // exit;
                            } else {
                                echo '<div class="error"><p>Failed to update Assign Product.</p></div>';
                            }
                        }







                        // if (isset($_POST['update_school'])) {
                        //     $school_id = isset($_POST['school_id']) ? intval($_POST['school_id']) : 0;

                        //     // Get form data

                        //     $name_contact = sanitize_text_field($_POST['name_contact']);
                        //     $invoice_email_address = sanitize_text_field($_POST['invoice_email_address']);

                        //     // Call the update function
                        //     update_school($school_id, $name_contact, $invoice_email_address);
                        // }

                        // Function to update method data
                        function update_school()
                        {
                            global $wpdb;

                            // Replace 'your_methods_table' with your actual table name
                            $table_name = $wpdb->prefix . 'schools';

                            $school_id = $_POST['school_id'];
                            $name_contact = $_POST['name_contact'];
                            $invoice_email_address = $_POST['invoice_email_address'];

                            $result = $wpdb->update(
                                $table_name,
                                array(
                                    'name_contact' => $name_contact,
                                    'invoice_email_address' => $invoice_email_address,
                                ),
                                array('id' => $school_id)
                            );
                            wp_send_json_success(array('message' => 'School has been updated'));
                        }
                        add_action('wp_ajax_update_school', 'update_school');
                        add_action('wp_ajax_nopriv_update_school', 'update_school');








                        // Add the checkbox to the LearnDash lesson settings tab
                        function add_group_leader_checkbox()
                        {
                            add_meta_box(
                                'group_leader_checkbox', //id
                                'Only accessible for Group leaders?', //title
                                'render_group_leader_checkbox', // callback
                                'sfwd-lessons',
                                'normal',
                                'high'
                            );
                        }

                        add_action('add_meta_boxes', 'add_group_leader_checkbox');

                        // Render the checkbox on the LearnDash lesson settings tab
                        function render_group_leader_checkbox($post)
                        {
                            $value = get_post_meta($post->ID, '_group_leader_only', true);
                            ?>
                            <label for="group_leader_checkbox">
                                <input type="checkbox" id="group_leader_checkbox" name="group_leader_checkbox" <?php checked($value, 'on'); ?> />
                                Only accessible for Group leaders?
                            </label>
                        <?php
                        }

                        // Save the checkbox value when the LearnDash lesson is updated
                        function save_group_leader_checkbox($post_id)
                        {
                            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
                            if (!current_user_can('edit_post', $post_id)) return;

                            $value = isset($_POST['group_leader_checkbox']) ? 'on' : 'off';
                            update_post_meta($post_id, '_group_leader_only', $value);
                        }

                        add_action('save_post', 'save_group_leader_checkbox');



                        // Filter to check if the lesson should be visible for the current user
                        function is_lesson_visible_for_user($content)
                        {
                            global $post;

                            // Check if the lesson has the checkbox set to true
                            $group_leader_only = get_post_meta($post->ID, '_group_leader_only', true);

                            // Check if the current user is a Group Leader
                           $is_group_leader = current_user_can('group_leader'); // Replace 'manage_group' with the actual capability assigned to Group Leaders 

                            // If the checkbox is set to true and the user is not a Group Leader, hide the content
                            if ($group_leader_only === 'on' && !$is_group_leader) {
                                return 'This lesson is only visible to Group Leaders.';
                            }

                            return $content;
                        }

                        add_filter('the_content', 'is_lesson_visible_for_user');






                        ?>