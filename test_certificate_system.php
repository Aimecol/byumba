<?php
/**
 * Comprehensive Test Suite for Diocese Certificate Application System
 * Tests all major functionality with the inserted test data
 */

require_once 'config/database.php';
session_start();

// Get current user
$current_user_id = $_SESSION['user_id'] ?? 1;
$is_authenticated = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diocese Certificate System - Test Suite</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .test-section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
        button { padding: 10px 15px; margin: 5px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .status-pending { color: #856404; font-weight: bold; }
        .status-processing { color: #0c5460; font-weight: bold; }
        .status-approved { color: #155724; font-weight: bold; }
        .status-completed { color: #155724; font-weight: bold; }
        .status-rejected { color: #721c24; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🏛️ Diocese Certificate Application System - Test Suite</h1>
    
    <div class="test-section info">
        <h2>📊 System Status</h2>
        <p><strong>Current User ID:</strong> <?php echo $current_user_id; ?></p>
        <p><strong>Authentication:</strong> <?php echo $is_authenticated ? '✅ Authenticated' : '⚠️ Using fallback user'; ?></p>
        <p><strong>Database:</strong> <?php echo isset($db) ? '✅ Connected' : '❌ Not connected'; ?></p>
        <p><strong>Test Data:</strong> <button onclick="insertTestData()">Insert Test Applications</button></p>
    </div>

    <div class="test-section">
        <h2>🧪 API Endpoint Tests</h2>
        <button onclick="testCertificateTypes()">Test Certificate Types API</button>
        <button onclick="testApplicationsGet()">Test Applications GET</button>
        <button onclick="testApplicationSubmission()">Test Application Submission</button>
        <button onclick="testPaymentAPI()">Test Payment API</button>
        <div id="apiResults"></div>
    </div>

    <div class="test-section">
        <h2>📋 Current Applications Overview</h2>
        <button onclick="loadApplicationsOverview()">Load Applications</button>
        <div id="applicationsOverview"></div>
    </div>

    <div class="test-section">
        <h2>📈 Statistics Dashboard</h2>
        <button onclick="loadStatistics()">Load Statistics</button>
        <div id="statisticsResults"></div>
    </div>

    <div class="test-section">
        <h2>🔍 Filter and Search Tests</h2>
        <button onclick="testStatusFilter('pending')">Filter Pending</button>
        <button onclick="testStatusFilter('processing')">Filter Processing</button>
        <button onclick="testStatusFilter('approved')">Filter Approved</button>
        <button onclick="testStatusFilter('completed')">Filter Completed</button>
        <button onclick="testDateRange()">Test Date Range</button>
        <div id="filterResults"></div>
    </div>

    <div class="test-section">
        <h2>💳 Payment Processing Tests</h2>
        <button onclick="testPaymentFlow()">Test Payment Flow</button>
        <button onclick="testPaymentStatus()">Test Payment Status</button>
        <div id="paymentResults"></div>
    </div>

    <script>
        // Insert test data
        async function insertTestData() {
            try {
                const response = await fetch('insert_test_applications.php');
                const result = await response.text();
                
                const popup = window.open('', 'TestData', 'width=800,height=600,scrollbars=yes');
                popup.document.write(result);
                popup.document.close();
            } catch (error) {
                alert('Error inserting test data: ' + error.message);
            }
        }

        // Test Certificate Types API
        async function testCertificateTypes() {
            const resultsDiv = document.getElementById('apiResults');
            resultsDiv.innerHTML = '<p>Testing Certificate Types API...</p>';

            try {
                const response = await fetch('api/certificate_types.php');
                const result = await response.json();

                if (result.success) {
                    resultsDiv.innerHTML = `
                        <div class="success">
                            <h3>✅ Certificate Types API - SUCCESS</h3>
                            <p><strong>Total Types:</strong> ${result.data.total}</p>
                            <p><strong>Sample Types:</strong></p>
                            <ul>
                                ${result.data.certificate_types.slice(0, 3).map(type => 
                                    `<li>${type.name} - RWF ${type.fee} (${type.processing_days} days)</li>`
                                ).join('')}
                            </ul>
                        </div>
                    `;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="error">
                        <h3>❌ Certificate Types API - FAILED</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        // Test Applications GET API
        async function testApplicationsGet() {
            const resultsDiv = document.getElementById('apiResults');
            resultsDiv.innerHTML = '<p>Testing Applications GET API...</p>';

            try {
                const response = await fetch('api/applications.php', {
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (result.success) {
                    resultsDiv.innerHTML = `
                        <div class="success">
                            <h3>✅ Applications GET API - SUCCESS</h3>
                            <p><strong>Total Applications:</strong> ${result.data.pagination.total_items}</p>
                            <p><strong>Current Page:</strong> ${result.data.pagination.current_page}</p>
                            <p><strong>Sample Applications:</strong></p>
                            <ul>
                                ${result.data.applications.slice(0, 3).map(app => 
                                    `<li>${app.id} - ${app.type} (${app.status})</li>`
                                ).join('')}
                            </ul>
                        </div>
                    `;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="error">
                        <h3>❌ Applications GET API - FAILED</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        // Test Application Submission
        async function testApplicationSubmission() {
            const resultsDiv = document.getElementById('apiResults');
            resultsDiv.innerHTML = '<p>Testing Application Submission...</p>';

            const testData = {
                certificate_type_id: 1,
                form_data: {
                    fullName: 'Test User ' + Date.now(),
                    email: 'test@diocese.rw',
                    phone: '250788123456',
                    nationalId: '1234567890123456'
                },
                notification_methods: ['email', 'sms'],
                notes: 'Test application from system test suite'
            };

            try {
                const response = await fetch('api/applications.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(testData)
                });

                const result = await response.json();

                if (result.success) {
                    resultsDiv.innerHTML = `
                        <div class="success">
                            <h3>✅ Application Submission - SUCCESS</h3>
                            <p><strong>Application Number:</strong> ${result.data.application_number}</p>
                            <p><strong>Application ID:</strong> ${result.data.application_id}</p>
                            <p><strong>Message:</strong> ${result.data.message}</p>
                        </div>
                    `;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="error">
                        <h3>❌ Application Submission - FAILED</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }

        // Load Applications Overview
        async function loadApplicationsOverview() {
            const resultsDiv = document.getElementById('applicationsOverview');
            resultsDiv.innerHTML = '<p>Loading applications...</p>';

            try {
                const response = await fetch('api/applications.php?limit=10', {
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (result.success && result.data.applications.length > 0) {
                    let tableHTML = `
                        <table>
                            <thead>
                                <tr>
                                    <th>Application #</th>
                                    <th>Certificate Type</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Payment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    result.data.applications.forEach(app => {
                        tableHTML += `
                            <tr>
                                <td>${app.id}</td>
                                <td>${app.type}</td>
                                <td><span class="status-${app.status}">${app.status.toUpperCase()}</span></td>
                                <td>${new Date(app.submitted_date).toLocaleDateString()}</td>
                                <td>${app.payment_status}</td>
                            </tr>
                        `;
                    });

                    tableHTML += '</tbody></table>';
                    resultsDiv.innerHTML = tableHTML;
                } else {
                    resultsDiv.innerHTML = '<p>No applications found or API error.</p>';
                }
            } catch (error) {
                resultsDiv.innerHTML = `<div class="error">Error loading applications: ${error.message}</div>`;
            }
        }

        // Load Statistics
        async function loadStatistics() {
            const resultsDiv = document.getElementById('statisticsResults');
            resultsDiv.innerHTML = '<p>Loading statistics...</p>';

            try {
                const response = await fetch('api/applications.php', {
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (result.success) {
                    const summary = result.data.summary;
                    resultsDiv.innerHTML = `
                        <div class="info">
                            <h3>📊 Application Statistics</h3>
                            <p><strong>Total Applications:</strong> ${summary.total}</p>
                            <p><strong>Pending Payment:</strong> ${summary.pending_payment}</p>
                            <h4>By Status:</h4>
                            <ul>
                                <li>Pending: ${summary.by_status.pending || 0}</li>
                                <li>Processing: ${summary.by_status.processing || 0}</li>
                                <li>Approved: ${summary.by_status.approved || 0}</li>
                                <li>Completed: ${summary.by_status.completed || 0}</li>
                                <li>Rejected: ${summary.by_status.rejected || 0}</li>
                            </ul>
                        </div>
                    `;
                }
            } catch (error) {
                resultsDiv.innerHTML = `<div class="error">Error loading statistics: ${error.message}</div>`;
            }
        }

        // Test Status Filter
        async function testStatusFilter(status) {
            const resultsDiv = document.getElementById('filterResults');
            resultsDiv.innerHTML = `<p>Filtering applications by status: ${status}...</p>`;

            try {
                const response = await fetch(`api/applications.php?status=${status}`, {
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (result.success) {
                    resultsDiv.innerHTML = `
                        <div class="info">
                            <h3>🔍 Filter Results: ${status.toUpperCase()}</h3>
                            <p><strong>Found:</strong> ${result.data.applications.length} applications</p>
                            ${result.data.applications.length > 0 ? `
                                <ul>
                                    ${result.data.applications.slice(0, 5).map(app => 
                                        `<li>${app.id} - ${app.type} (${app.submitted_date})</li>`
                                    ).join('')}
                                </ul>
                            ` : '<p>No applications found with this status.</p>'}
                        </div>
                    `;
                }
            } catch (error) {
                resultsDiv.innerHTML = `<div class="error">Filter error: ${error.message}</div>`;
            }
        }

        // Test Payment Flow
        async function testPaymentFlow() {
            const resultsDiv = document.getElementById('paymentResults');
            resultsDiv.innerHTML = '<p>Testing payment flow...</p>';

            const testPayment = {
                phoneNumber: '250788123456',
                amount: 200,
                description: 'Test Certificate Application Fee'
            };

            try {
                const response = await fetch('api/payment-request.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(testPayment)
                });

                const result = await response.json();
                
                resultsDiv.innerHTML = `
                    <div class="info">
                        <h3>💳 Payment Flow Test</h3>
                        <p><strong>Status:</strong> ${result.success ? 'SUCCESS' : 'FAILED'}</p>
                        <p><strong>Response:</strong></p>
                        <pre>${JSON.stringify(result, null, 2)}</pre>
                    </div>
                `;
            } catch (error) {
                resultsDiv.innerHTML = `<div class="error">Payment test error: ${error.message}</div>`;
            }
        }

        // Auto-load overview on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Diocese Certificate System Test Suite loaded');
        });
    </script>
</body>
</html>
