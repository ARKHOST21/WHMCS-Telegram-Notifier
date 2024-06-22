# WHMCS Telegram Notifier

> WHMCS Telegram Notifier is a comprehensive hook for WHMCS that sends real-time notifications to Telegram for various WHMCS events. Stay informed about new orders, invoices, tickets, and more, directly on your Telegram app.

## Features

- Real-time notifications for a wide range of WHMCS events
- Easy setup and configuration
- Customizable notification messages
- Secure handling of sensitive information
- Error logging for easy troubleshooting

## Supported Notifications

- New orders and order status changes
- Invoice creation and payments
- Support ticket updates
- Client registrations and profile edits
- Domain registrations, transfers, and renewals
- Service provisioning and status changes
- Network issues
- Cancellation requests
- Quote creation and acceptance
- And many more!

## Installation

1. Download the `whmcs_telegram_notifier.php` file.
2. Place the file in your WHMCS hooks directory (usually `/includes/hooks/`).
3. Edit the file and replace the following placeholders with your actual data:
- `YOUR_BOT_TOKEN_HERE`: Your Telegram Bot Token
- `YOUR_CHAT_ID_HERE`: Your Telegram Chat ID
5. Adjust the `$GLOBALS['whmcsAdminURL']` to match your WHMCS admin URL, include the trailing `/`.
6. Set the `$GLOBALS['sensitiveInformation']` variable as needed.

## Configuration

You can enable or disable specific notifications by setting the corresponding variables at the top of the script to `true` or `false`.

## Customization

You can customize notification messages by editing the message strings in each hook function within the PHP file.

## Testing

After installation, perform actions in WHMCS (e.g., create a test order or ticket) to trigger notifications and ensure they're received in your Telegram chat.

## Security Note

Be cautious when setting $GLOBALS\['sensitiveInformation'\] to true, as this will include potentially sensitive data like email addresses in the Telegram messages.

## Troubleshooting

Check the WHMCS activity log for any error messages related to the Telegram notifications. The hook includes extensive error logging to help diagnose any issues.

## Changelog

- v1.0.0 (2024-06-23): Initial release

## Contributing

Contributions, issues, and feature requests are welcome! Feel free to check issues page.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

If you encounter any issues or have questions, please open an issue on the GitHub repository.

If you find this hook useful, consider starring the repository and sharing it with others!
