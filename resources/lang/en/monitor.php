<?php

return [
    // Header
    'choose_package' => 'Choose Ping Package',
    'current_limit' => 'Current limit: :count Pings',
    'upgrade_unlock' => 'Upgrade to unlock more pings',

    // Payment form
    'you_selected_package' => 'You selected package :amount VND',
    'need_login' => 'You need to :login_link to purchase VIP package',
    'login' => 'Login',
    'continue' => 'Continue',
    'buy_vip_description' => 'Buy VIP Ping Package',

    // Package details
    'free_package' => ':count Pings',
    'free' => 'Free',
    'trial' => 'Trial',
    'in_use' => 'In Use',
    'one_month' => 'One Month',
    'register' => 'Register',
    'hot' => 'HOT',

    // Features
    'time_interval_free' => 'Time Interval <span class="free_count">:count </span> minutes',
    'time_interval' => 'Time Interval <span class="vip_count">:count </span> minutes',
    'monitors_count' => ':count pings',
    'email_notification' => 'Email notification',
    'basic_report' => 'Basic report',
    'email_alert' => 'Email notification',
    'web_hook_alert' => 'WebHook, Telegram, Slack, Discord... notification',
    'send_consecutive_notification' => 'Consecutive notifications',
    'telegram_alert' => 'Telegram notification',
    'app_alert' => 'App notification',

    'ping_monitor' => 'Ping server',
    'ssl_monitor' => 'SSL Ping',
    'web_monitor' => 'Web Ping',
    'content_monitor' => 'Web Content',
    'port_monitor' => 'Port Ping',
    'database_monitor' => 'Database Port Ping',

    'api_access' => 'API Access',

    // Error messages
    'error_occurred' => 'Error occurred: :message',
    'error_occurred_2' => 'Error occurred 2: :message',
    'go_back' => 'Go Back',
    'go_home' => 'GO HOME',
    'payment_success' => 'Payment successful: :order_id, Amount: :amount',

    // API messages
    'api' => [
        'upgrade_here' => 'Upgrade here',
        'not_found' => 'Ping item not found',
        'no_permission_edit' => 'You do not have permission to edit this ping item',
        'exceeded_limit' => 'You have exceeded the number of enabled pings. Please disable some pings or upgrade your package.',
        'no_permission_alert' => 'You do not have permission to use this alert configuration ID: :alert_id',
        'update_error' => 'Update error: :message',
        'update_success' => 'Updated successfully',
        'interval_too_short_for_free' => "[:desc] \nFor free package, the minimum interval is 5 minutes. Please upgrade your package to set a shorter interval.",
        'allow_alert_for_consecutive_error_for_free' => "[:desc] \nFree package: upgrade your package to enable Send consecutive alerts.",
        // Port validation errors
        'invalid_format_port' => "Web/Domain/IP must be in format IP:PORT \n For example: abc.com:80",
        'invalid_port_number' => 'Port must be a number between 1 and 65535',
        'invalid_domain_ip' => 'Domain is not a valid domain or IP',
    ],

    // JavaScript messages
    'free_package_message' => 'Free package is currently applied to your account by default!',

    // Alert configuration form fields
    'select' => '-Select-',
    'send_email' => 'Send Email',
    'send_sms' => 'Send SMS',
    'send_telegram' => 'Send Telegram',
    'call_webhook' => 'Call Webhook',

    'telegram_chat_id' => 'Telegram Chat ID',
    'telegram_bot_token' => 'Telegram Bot Token',
    'get_from_botfather' => 'Get from BotFather',
    'get_from_botfather_35_char' => 'Get from BotFather, 35 characters',

    'enter_email' => 'Enter email',
    'enter_valid_email' => 'Enter a valid email',

    'webhook_url' => 'Webhook URL',
    'enter_webhook_url' => 'Enter webhook URL (http://example.com/webhook)',

    'phone_number' => 'Phone Number',
    'enter_phone_number' => 'Enter phone number (e.g. +84901234567)',

    // JavaScript placeholders and messages
    'js_enter_email' => 'Enter email',
    'js_enter_valid_email' => 'Enter a valid email:',
    'js_enter_telegram_info' => 'Enter chat group ID and token, separated by comma',
    'js_enter_telegram_id_token' => 'Enter Telegram group ID, Token',
    'js_enter_webhook_url' => 'Enter webhook URL',
    'js_enter_valid_webhook_url' => 'Enter a valid Webhook URL:',
    'js_enter_phone_number' => 'Enter phone number',
    'js_enter_valid_phone_number' => 'Enter a valid phone number:',
    
    // Alert Configuration
    'no_alert_config' => 'No alert configuration yet',
    'create_new' => 'Create new',
    'select_alert_type' => '-Select Alert Type-',

];
