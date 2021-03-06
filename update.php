<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Handler!</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.4/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>

<body>
    <section class="section">
        <div class="container">
            <div class="column">
                <form method="POST" action="export-attendees.php" class="is-three-fifths">
                    <h1 class="title"> Xác nhận thay đổi event</h1>                    
                    <div class="field">
                        <label class="label">Danh sách sự kiện</label>
                        <div class="control  is-grouped">
                        <div class="select">
                            <select name="eventID" required>
                            <?php 
                                require_once './google-api-php-client-2.2.2/vendor/autoload.php';
                                session_start();
                                
                                /**
                                 * Returns an authorized API client.
                                 * @return Google_Client the authorized client object
                                 */
                                function getClient()
                                {
                                    $client = new Google_Client();
                                    $client->setApplicationName('Google Calendar API PHP Quickstart');
                                    $client->setScopes(Google_Service_Calendar::CALENDAR);
                                    $client->setAuthConfig('credentials.json');
                                    $client->setAccessType('offline');
                                    $client->setIncludeGrantedScopes(true);   // incremental auth
                                    $client->setApprovalPrompt('force');
                                    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
                                        $client->setAccessToken($_SESSION['access_token']);
                                    } else {
                                        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/event-export/oauth2callback.php';
                                        header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
                                    }
                                    return $client;
                                }
                                
                                try {
                                    // Get the API client and construct the service object.
                                    $client = getClient();
                                    $service = new Google_Service_Calendar($client);

                                    $events = $service->events->listEvents('primary');
                                } catch (Exception $e) {
                                    // If get Events Failed => Clear Token
                                    ob_start();
                                    header('Location: /event-export/clear-token.php');
                                    ob_end_flush();
                                    die();
                                }
                                
                                // Load Event List
                                while(true) {
                                    foreach ($events->getItems() as $event) {
                                        echo "<option value='" . $event->getId() . "'>" . $event->getSummary() . "</option>";
                                    }
                                    $pageToken = $events->getNextPageToken();
                                    if ($pageToken) {
                                    $optParams = array('pageToken' => $pageToken);
                                    $events = $service->events->listEvents('primary', $optParams);
                                    } else {
                                    break;
                                    }
                                }
                            ?>
                            </select>
                        </div>
                            <input class="button is-primary" type="submit" value="Xuất excel" />
                        </div>
                    </div>

                </form>

            </div>
            <form method="POST" action="update-event.php" style="margin: 10px">
        
                Email người nhận: <input type="text area" name="email" /><br />
                New title: <input type="text" name="newsummary" /> <br />
                <input type="submit" name=" submit " value="Gửi" />
            </form>
Kết thúc cuộc trò chuyện
Nhập tin nhắn, @name...

        </div>
    </section>
</body>

</html>
