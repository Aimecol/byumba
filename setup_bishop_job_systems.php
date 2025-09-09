<?php
/**
 * Setup Script for Bishop Meeting and Job Application Systems
 * Diocese of Byumba - Complete system setup and verification
 */

require_once 'config/database.php';

// Start session to check for authenticated user
session_start();

// Determine user ID - use authenticated user or fallback to 1
$current_user_id = $_SESSION['user_id'] ?? 1;
$is_authenticated = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

echo "<h1>🏛️ Diocese of Byumba - Bishop Meeting & Job Application Systems Setup</h1>";
echo "<p><strong>Current User ID:</strong> " . $current_user_id . "</p>";
echo "<p><strong>Authentication Status:</strong> " . ($is_authenticated ? 'Authenticated' : 'Using fallback user ID') . "</p>";
echo "<hr>";

try {
    // Check if database connection exists
    if (!isset($db)) {
        $db = new PDO(
            'mysql:host=localhost;dbname=diocese_byumba;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    echo "<h2>📋 System Setup Progress</h2>";
    echo "<div style='font-family: monospace; background: #f8f9fa; padding: 15px; border-radius: 5px;'>";

    // Step 1: Update meetings table schema
    echo "<h3>Step 1: Updating Meetings Table Schema</h3>";
    
    // Check if meetings table has new columns
    $check_columns = "SHOW COLUMNS FROM meetings LIKE 'first_name'";
    $stmt = $db->prepare($check_columns);
    $stmt->execute();
    $has_new_columns = $stmt->rowCount() > 0;
    
    if (!$has_new_columns) {
        echo "⚠️ Meetings table needs schema update. Please run: update_meetings_table.sql<br>";
        echo "<p><a href='#' onclick=\"runSQLFile('update_meetings_table.sql')\">🔧 Run Meetings Table Update</a></p>";
    } else {
        echo "✅ Meetings table schema is up to date<br>";
    }

    // Step 2: Check job_applications table
    echo "<h3>Step 2: Checking Job Applications Table</h3>";
    
    try {
        $check_job_table = "SELECT COUNT(*) FROM job_applications LIMIT 1";
        $stmt = $db->prepare($check_job_table);
        $stmt->execute();
        echo "✅ Job applications table exists<br>";
    } catch (PDOException $e) {
        echo "⚠️ Job applications table needs to be created. Please run: create_job_applications_table.sql<br>";
        echo "<p><a href='#' onclick=\"runSQLFile('create_job_applications_table.sql')\">🔧 Create Job Applications Table</a></p>";
    }

    // Step 3: Check API endpoints
    echo "<h3>Step 3: Verifying API Endpoints</h3>";
    
    $api_files = [
        'api/bishop-meeting.php' => 'Bishop Meeting API',
        'api/job-applications.php' => 'Job Applications API'
    ];
    
    foreach ($api_files as $file => $name) {
        if (file_exists($file)) {
            echo "✅ $name endpoint exists<br>";
        } else {
            echo "❌ $name endpoint missing<br>";
        }
    }

    // Step 4: Test data insertion
    echo "<h3>Step 4: Test Data Status</h3>";
    
    // Check meetings test data
    $meetings_count = "SELECT COUNT(*) as count FROM meetings WHERE meeting_number LIKE 'REQ%'";
    $stmt = $db->prepare($meetings_count);
    $stmt->execute();
    $meetings_test_count = $stmt->fetch()['count'];
    
    echo "<p><strong>Bishop Meeting Requests:</strong> " . $meetings_test_count . " test records</p>";
    
    // Check job applications test data
    try {
        $jobs_count = "SELECT COUNT(*) as count FROM job_applications";
        $stmt = $db->prepare($jobs_count);
        $stmt->execute();
        $jobs_test_count = $stmt->fetch()['count'];
        echo "<p><strong>Job Applications:</strong> " . $jobs_test_count . " test records</p>";
    } catch (PDOException $e) {
        echo "<p><strong>Job Applications:</strong> Table not created yet</p>";
    }

    echo "</div>";

    // Step 5: Quick functionality tests
    echo "<h2>🧪 System Functionality Tests</h2>";
    echo "<div style='margin: 20px 0;'>";
    echo "<button onclick='testBishopMeetingAPI()' style='margin: 5px; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;'>Test Bishop Meeting API</button>";
    echo "<button onclick='testJobApplicationAPI()' style='margin: 5px; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;'>Test Job Application API</button>";
    echo "<button onclick='insertTestData()' style='margin: 5px; padding: 10px; background: #ffc107; color: black; border: none; border-radius: 4px; cursor: pointer;'>Insert Test Data</button>";
    echo "<button onclick='viewSystemStats()' style='margin: 5px; padding: 10px; background: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer;'>View System Statistics</button>";
    echo "</div>";

    echo "<div id='testResults' style='margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; min-height: 100px;'>";
    echo "<p><em>Click the buttons above to test system functionality...</em></p>";
    echo "</div>";

    // System overview
    echo "<h2>📊 System Overview</h2>";
    echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;'>";
    
    // Bishop Meeting System
    echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745;'>";
    echo "<h3>👨‍💼 Bishop Meeting System</h3>";
    echo "<ul>";
    echo "<li><strong>Form Fields:</strong> firstName, lastName, email, phone, parish, meetingType, purpose</li>";
    echo "<li><strong>Meeting Types:</strong> Spiritual Guidance, Pastoral Care, Marriage Counseling, Confession, Administrative, Community Issue, Other</li>";
    echo "<li><strong>Status Flow:</strong> submitted → reviewed → scheduled → confirmed → completed</li>";
    echo "<li><strong>API Endpoint:</strong> <code>api/bishop-meeting.php</code></li>";
    echo "<li><strong>Request Numbers:</strong> REQ######</li>";
    echo "</ul>";
    echo "</div>";
    
    // Job Application System
    echo "<div style='background: #e8f4fd; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff;'>";
    echo "<h3>💼 Job Application System</h3>";
    echo "<ul>";
    echo "<li><strong>Form Sections:</strong> Personal Info, Education & Experience, Cover Letter, Documents</li>";
    echo "<li><strong>File Uploads:</strong> Resume (required), Cover Letter, Certificates</li>";
    echo "<li><strong>Status Flow:</strong> submitted → under_review → shortlisted → interview_scheduled → interviewed → selected</li>";
    echo "<li><strong>API Endpoint:</strong> <code>api/job-applications.php</code></li>";
    echo "<li><strong>Application Numbers:</strong> JOB######</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";

    // Quick links
    echo "<h2>🔗 Quick Links</h2>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
    echo "<p><strong>Setup Files:</strong></p>";
    echo "<ul>";
    echo "<li><a href='update_meetings_table.sql' target='_blank'>📄 Meetings Table Schema Update</a></li>";
    echo "<li><a href='create_job_applications_table.sql' target='_blank'>📄 Job Applications Table Creation</a></li>";
    echo "<li><a href='insert_bishop_meetings_test_data.sql' target='_blank'>📄 Test Data for Both Systems</a></li>";
    echo "</ul>";
    echo "<p><strong>API Endpoints:</strong></p>";
    echo "<ul>";
    echo "<li><a href='api/bishop-meeting.php' target='_blank'>🔗 Bishop Meeting API</a></li>";
    echo "<li><a href='api/job-applications.php' target='_blank'>🔗 Job Applications API</a></li>";
    echo "</ul>";
    echo "<p><strong>Frontend Integration:</strong></p>";
    echo "<ul>";
    echo "<li><a href='bishop-meeting.html' target='_blank'>📋 Bishop Meeting Form</a></li>";
    echo "<li><a href='jobs.html' target='_blank'>💼 Job Listings (with application modal)</a></li>";
    echo "</ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
} catch (Exception $e) {
    echo "<h2>❌ General Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

?>

<script>
// Test Bishop Meeting API
async function testBishopMeetingAPI() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<p>🧪 Testing Bishop Meeting API...</p>';

    try {
        // Test GET request
        const getResponse = await fetch('api/bishop-meeting.php', {
            credentials: 'same-origin'
        });
        const getResult = await getResponse.json();

        if (getResult.success) {
            resultsDiv.innerHTML = `
                <div style="background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">
                    <h4>✅ Bishop Meeting API - SUCCESS</h4>
                    <p><strong>Total Meetings:</strong> ${getResult.data.summary.total}</p>
                    <p><strong>By Status:</strong></p>
                    <ul>
                        <li>Submitted: ${getResult.data.summary.by_status.submitted}</li>
                        <li>Reviewed: ${getResult.data.summary.by_status.reviewed}</li>
                        <li>Scheduled: ${getResult.data.summary.by_status.scheduled}</li>
                        <li>Confirmed: ${getResult.data.summary.by_status.confirmed}</li>
                        <li>Completed: ${getResult.data.summary.by_status.completed}</li>
                    </ul>
                    <p><strong>Recent Meetings:</strong></p>
                    <ul>
                        ${getResult.data.meetings.slice(0, 3).map(meeting => 
                            `<li>${meeting.id} - ${meeting.full_name} (${meeting.type}) - ${meeting.status}</li>`
                        ).join('')}
                    </ul>
                </div>
            `;
        } else {
            throw new Error(getResult.message);
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
                <h4>❌ Bishop Meeting API - FAILED</h4>
                <p>${error.message}</p>
            </div>
        `;
    }
}

// Test Job Application API
async function testJobApplicationAPI() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<p>🧪 Testing Job Application API...</p>';

    try {
        const response = await fetch('api/job-applications.php', {
            credentials: 'same-origin'
        });
        const result = await response.json();

        if (result.success) {
            resultsDiv.innerHTML = `
                <div style="background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">
                    <h4>✅ Job Application API - SUCCESS</h4>
                    <p><strong>Total Applications:</strong> ${result.data.summary.total}</p>
                    <p><strong>By Status:</strong></p>
                    <ul>
                        <li>Submitted: ${result.data.summary.by_status.submitted}</li>
                        <li>Under Review: ${result.data.summary.by_status.under_review}</li>
                        <li>Shortlisted: ${result.data.summary.by_status.shortlisted}</li>
                        <li>Interview Scheduled: ${result.data.summary.by_status.interview_scheduled}</li>
                        <li>Selected: ${result.data.summary.by_status.selected}</li>
                    </ul>
                    <p><strong>Recent Applications:</strong></p>
                    <ul>
                        ${result.data.applications.slice(0, 3).map(app => 
                            `<li>${app.id} - ${app.full_name} (${app.job_title}) - ${app.status}</li>`
                        ).join('')}
                    </ul>
                </div>
            `;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        resultsDiv.innerHTML = `
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
                <h4>❌ Job Application API - FAILED</h4>
                <p>${error.message}</p>
            </div>
        `;
    }
}

// Insert test data
async function insertTestData() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<p>📊 Inserting test data...</p>';

    try {
        // This would typically run the SQL file
        resultsDiv.innerHTML = `
            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;">
                <h4>📊 Test Data Insertion</h4>
                <p>To insert test data, please run the following SQL file:</p>
                <p><strong>File:</strong> <code>insert_bishop_meetings_test_data.sql</code></p>
                <p>This file contains:</p>
                <ul>
                    <li>20 realistic bishop meeting requests</li>
                    <li>12 comprehensive job applications</li>
                    <li>All status types and scenarios</li>
                    <li>Multiple users and time periods</li>
                </ul>
                <p><em>You can run this file directly in your MySQL client or phpMyAdmin.</em></p>
            </div>
        `;
    } catch (error) {
        resultsDiv.innerHTML = `
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
                <h4>❌ Test Data Insertion - ERROR</h4>
                <p>${error.message}</p>
            </div>
        `;
    }
}

// View system statistics
async function viewSystemStats() {
    const resultsDiv = document.getElementById('testResults');
    resultsDiv.innerHTML = '<p>📈 Loading system statistics...</p>';

    try {
        // Get both API results
        const [meetingsResponse, jobsResponse] = await Promise.all([
            fetch('api/bishop-meeting.php', { credentials: 'same-origin' }),
            fetch('api/job-applications.php', { credentials: 'same-origin' })
        ]);

        const meetingsResult = await meetingsResponse.json();
        const jobsResult = await jobsResponse.json();

        resultsDiv.innerHTML = `
            <div style="background: #d1ecf1; padding: 15px; border-radius: 5px; border: 1px solid #bee5eb;">
                <h4>📈 System Statistics</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h5>👨‍💼 Bishop Meetings</h5>
                        <p><strong>Total:</strong> ${meetingsResult.success ? meetingsResult.data.summary.total : 'N/A'}</p>
                        ${meetingsResult.success ? `
                            <ul style="font-size: 14px;">
                                <li>Submitted: ${meetingsResult.data.summary.by_status.submitted}</li>
                                <li>Reviewed: ${meetingsResult.data.summary.by_status.reviewed}</li>
                                <li>Scheduled: ${meetingsResult.data.summary.by_status.scheduled}</li>
                                <li>Confirmed: ${meetingsResult.data.summary.by_status.confirmed}</li>
                                <li>Completed: ${meetingsResult.data.summary.by_status.completed}</li>
                            </ul>
                        ` : '<p>API not available</p>'}
                    </div>
                    <div>
                        <h5>💼 Job Applications</h5>
                        <p><strong>Total:</strong> ${jobsResult.success ? jobsResult.data.summary.total : 'N/A'}</p>
                        ${jobsResult.success ? `
                            <ul style="font-size: 14px;">
                                <li>Submitted: ${jobsResult.data.summary.by_status.submitted}</li>
                                <li>Under Review: ${jobsResult.data.summary.by_status.under_review}</li>
                                <li>Shortlisted: ${jobsResult.data.summary.by_status.shortlisted}</li>
                                <li>Interview Scheduled: ${jobsResult.data.summary.by_status.interview_scheduled}</li>
                                <li>Selected: ${jobsResult.data.summary.by_status.selected}</li>
                            </ul>
                        ` : '<p>API not available</p>'}
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        resultsDiv.innerHTML = `
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
                <h4>❌ Statistics Loading - ERROR</h4>
                <p>${error.message}</p>
            </div>
        `;
    }
}
</script>

<style>
button:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

code {
    background: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>

<?php
echo "<hr>";
echo "<p><em>Setup and verification completed. Use the test buttons above to verify system functionality.</em></p>";
?>
