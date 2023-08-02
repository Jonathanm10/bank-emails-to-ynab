# Sync bank email notifications to YNAB

## Requirements

PHP >8.1

## Installation

Clone the repo

## Configuration

This guide will help you set up a Google Cloud Project, enable the Gmail API, and obtain the necessary credentials for your application to interact with Gmail.

### Step 1: Creating a Google Cloud Project
1. Visit https://console.cloud.google.com/projectcreate to create a new Cloud Project.

2. You will be requested to name your project. Assign a relevant name and continue to the next step.

3. Wait for your project to be created. This might take a few seconds.

### Step 2: Enabling Gmail API
1. Once your project has been created, you will be redirected to the dashboard. Navigate to https://console.cloud.google.com/apis/library.

2. In the API Library, look for 'Gmail API' using the search bar and select it.

3. Click the 'ENABLE' button, and the Gmail API will be enabled for the selected project.

### Step 3: Creating OAuth credentials
1. Go to APIs & Services → Credentials in your project’s console (https://console.cloud.google.com/apis/credentials).

2. Click on 'CREATE CREDENTIALS' and select 'OAuth client ID'.

3. If you haven't set up your project's OAuth consent screen, do so by following the instructions after clicking 'CONFIGURE CONSENT SCREEN'.

4. Under 'Application type', select 'Web application'. Assign a name if you wish, then under 'Authorized redirect URIs', add 'https://developers.google.com/oauthplayground'.

5. Click 'Create' and note down your client ID and Client Secret.

6. Download your credentials as a JSON file and store it in storage/app/credentials.json.

### Step 4: Obtaining Access Token
1. Head to the OAuth2 Playground at https://developers.google.com/oauthplayground.

2. Click the settings icon (⚙️) at the top right corner, select 'Use your own OAuth credentials', and provide the OAuth2 Client ID and Client Secret from the credentials you created earlier.

3. Under 'Step 1 - Select & authorize APIs', find 'Gmail API v1' from the list, select https://www.googleapis.com/auth/gmail.readonly (or other scopes based on your requirement), and click 'Authorize APIs'.

4. Follow the prompts to select your Google account and grant permissions.

5. Click 'Exchange authorization code for tokens' in 'Step 2 - Exchange authorization code for tokens'. The access token will appear on the right side of the screen.

6. Save the access token in storage/app/token.json.

### Step 6: Set up your YNAB api key
1. [Sign in to the YNAB web app](https://app.ynab.com/settings) and go to the "Account Settings" page and then to the "Developer Settings" page.

2. Under the "Personal Access Tokens" section, click "New Token", enter your password and click "Generate" to get an access token.

3. Copy .env.example to .env and set your YNAB api key

## Usage

### Manual sync

```bash
php bankmailtoynab sync
```

### Sync as a cron job

You can set up the frequency of the sync by editing the `schedule` method in `app/Commands/SyncCommand.php`

```bash
* * * * * php bankmailtoynab schedule:run >> /dev/null 2>&1
```

