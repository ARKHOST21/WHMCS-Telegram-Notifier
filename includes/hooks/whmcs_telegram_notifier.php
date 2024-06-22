<?php

///////////////////////// Provided For Free By /////////////////////////
//                                                                    //
//                     ArkHost - https://arkhost.com                  //
//                                                                    //
////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////
////////////////////////// Configuration Area //////////////////////////
///////////////////////////////////////////////////////////////////////
// Configure the below variables to allow the script to work correctly and connect to both your WHMCS install and Telegram bot.

// Your Telegram Bot Token
$GLOBALS['telegramBotToken'] = "YOUR_BOT_TOKEN_HERE";

// Your Telegram Chat ID
$GLOBALS['telegramChatID'] = "YOUR_CHAT_ID_HERE";

// Your WHMCS Admin URL.
$GLOBALS['whmcsAdminURL'] = "https://WHMCS_ADMIN_URL_HERE.com/admin/";
// Note: Please include the end / on your URL. An example of an accepted link would be: https://account.whmcs.com/admin/

// Your Company Name.
$GLOBALS['companyName'] = "YOUR_COMPANY_NAME_HERE";

// Sensitive Information Display
$GLOBALS['sensitiveInformation'] = false; // false/true
// Note: Disabled by default; if enabled, customer emails will be included in some notifications.
// WARNING: Sharing/storing personally identifiable information on Telegram will require the update of your privacy policy (if applicable) to allow you to remain in compliance with GDPR.



///////////////////////////////////////////////////////////////////////
////////////////////////// Notification Area //////////////////////////
///////////////////////////////////////////////////////////////////////
// Configure the below notification settings to meet the requirements of your team and what you wish to send to the Telegram channel.
// true = Notification enabled.
// false = Notification disabled.

// Notification Settings
$ticketOpened = true;
$ticketClosed = true;
$ticketUserReply = true;
$ticketFlagged = true;
$ticketNewNote = true;
$invoicePaid = true;
$invoiceRefunded = true;
$invoiceLateFee = true;
$newOrder = true;
$pendingOrder = true;
$orderPaid = true;
$orderAccepted = true;
$orderCancelled = true;
$orderCancelledRefunded = true;
$orderFraud = true;
$networkIssueAdd = true;
$networkIssueEdit = true;
$networkIssueClosed = true;
$cancellationRequest = true;
$clientAdd = true;
$clientEdit = true;
$domainRenewal = true;
$domainTransfer = true;
$domainRegistration = true;
$addToClientNotes = true;
$moduleCreate = true;
$moduleTerminate = true;
$moduleSuspend = true;
$moduleUnsuspend = true;
$domainAutoRenewDisabled = true;
$domainAutoRenewEnabled = true;
$ticketAdminReply = true;
$quoteCreated = true;
$quoteAccepted = true;
$invoiceCreated = true;

// Hook Functions

// New Order Placed
if($newOrder === true):
    add_hook('OrderCreated', 1, function($vars) {
        try {
            logActivity("Hook triggered: OrderCreated");
            logActivity("OrderCreated vars: " . print_r($vars, true));
            $order = localAPI('GetOrders', array('id' => $vars['orderid']));
            if (isset($order['orders']['order'][0])) {
                $orderInfo = $order['orders']['order'][0];
                $message = "🛒 *New Order Created*\n\n";
                $message .= "Order ID: #{$vars['orderid']}\n";
                $message .= "User ID: #{$orderInfo['userid']}\n";
                $message .= "Total: {$orderInfo['amount']}\n";
                $message .= "Status: {$orderInfo['status']}\n";
                $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
                sendTelegramMessage($message);
            } else {
                logActivity("OrderCreated: Unable to fetch order details for ID {$vars['orderid']}");
            }
        } catch (Exception $e) {
            logActivity("Error in OrderCreated hook: " . $e->getMessage());
        }
    });
endif;

if($invoiceCreated === true):
    add_hook('InvoiceCreation', 1, function($vars) {
        try {
            logActivity("Hook triggered: InvoiceCreation");
            $message = "📄 *New Invoice Created*\n\n";
            $message .= "Invoice ID: #{$vars['invoiceid']}\n";
            $message .= "User ID: #{$vars['userid']}\n";
            $message .= "Total: {$vars['total']}\n";
            $message .= "Due Date: {$vars['duedate']}\n";
            $message .= "[View Invoice]({$GLOBALS['whmcsAdminURL']}invoices.php?action=edit&id={$vars['invoiceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in InvoiceCreation hook: " . $e->getMessage());
        }
    });
endif;

if($invoicePaid === true):
    add_hook('InvoicePaid', 1, function($vars) {
        try {
            logActivity("Hook triggered: InvoicePaid");
            $invoice = localAPI('GetInvoice', array('invoiceid' => $vars['invoiceid']), '');
            $client = localAPI('GetClientsDetails', array('clientid' => $invoice['userid'], 'stats' => false), '');
            
            $message = "💰 *Invoice Paid*\n\n";
            $message .= "Invoice ID: #{$vars['invoiceid']}\n";
            $message .= "Amount Paid: \${$invoice['total']}\n";
            $message .= "User Email: " . ($GLOBALS['sensitiveInformation'] ? $client['email'] : "-- Redacted --") . "\n";
            $message .= "[View Invoice]({$GLOBALS['whmcsAdminURL']}invoices.php?action=edit&id={$vars['invoiceid']})";

            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in InvoicePaid hook: " . $e->getMessage());
        }
    });
endif;

if($invoiceRefunded === true):
    add_hook('InvoiceRefunded', 1, function($vars) {
        try {
            logActivity("Hook triggered: InvoiceRefunded");
            $message = "🔄 *Invoice Refunded*\n\n";
            $message .= "Invoice ID: #{$vars['invoiceid']}\n";
            $message .= "[View Invoice]({$GLOBALS['whmcsAdminURL']}invoices.php?action=edit&id={$vars['invoiceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in InvoiceRefunded hook: " . $e->getMessage());
        }
    });
endif;

if($invoiceLateFee === true):
    add_hook('AddInvoiceLateFee', 1, function($vars) {
        try {
            logActivity("Hook triggered: AddInvoiceLateFee");
            $message = "⏰ *Invoice Late Fee Added*\n\n";
            $message .= "Invoice ID: #{$vars['invoiceid']}\n";
            $message .= "[View Invoice]({$GLOBALS['whmcsAdminURL']}invoices.php?action=edit&id={$vars['invoiceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AddInvoiceLateFee hook: " . $e->getMessage());
        }
    });
endif;

// Order Accepted
if($orderAccepted === true):
    add_hook('AcceptOrder', 1, function($vars) {
        try {
            logActivity("Hook triggered: AcceptOrder");
            $message = "✅ *Order Accepted*\n\n";
            $message .= "Order ID: #{$vars['orderid']}\n";
            $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AcceptOrder hook: " . $e->getMessage());
        }
    });
endif;

if($orderCancelled === true):
    add_hook('CancelOrder', 1, function($vars) {
        try {
            logActivity("Hook triggered: CancelOrder");
            $message = "❌ *Order Cancelled*\n\n";
            $message .= "Order ID: #{$vars['orderid']}\n";
            $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in CancelOrder hook: " . $e->getMessage());
        }
    });
endif;

if($orderCancelledRefunded === true):
    add_hook('CancelAndRefundOrder', 1, function($vars) {
        try {
            logActivity("Hook triggered: CancelAndRefundOrder");
            $message = "🔙 *Order Cancelled & Refunded*\n\n";
            $message .= "Order ID: #{$vars['orderid']}\n";
            $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in CancelAndRefundOrder hook: " . $e->getMessage());
        }
    });
endif;

if($orderFraud === true):
    add_hook('FraudOrder', 1, function($vars) {
        try {
            logActivity("Hook triggered: FraudOrder");
            $message = "⚠️ *Order Marked As Fraudulent*\n\n";
            $message .= "Order ID: #{$vars['orderid']}\n";
            $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in FraudOrder hook: " . $e->getMessage());
        }
    });
endif;

// Order Paid
if($orderPaid === true):
    add_hook('OrderPaid', 1, function($vars) {
        try {
            $order = localAPI('GetOrders', array('id' => $vars['orderid']));
            if (isset($order['orders']['order'][0])) {
                $orderInfo = $order['orders']['order'][0];
                $message = "💰 *Order Paid*\n\n";
                $message .= "Order ID: #{$vars['orderid']}\n";
                $message .= "User ID: #{$orderInfo['userid']}\n";
                $message .= "Total Paid: {$orderInfo['amount']}\n";
                $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
                sendTelegramMessage($message);
            }
        } catch (Exception $e) {
            logActivity("Error in OrderPaid hook: " . $e->getMessage());
        }
    });
endif;


if($pendingOrder === true):
    add_hook('PendingOrder', 1, function($vars) {
        try {
            logActivity("Hook triggered: PendingOrder");
            $message = "⏳ *Order Set to Pending*\n\n";
            $message .= "Order ID: #{$vars['orderid']}\n";
            $message .= "[View Order]({$GLOBALS['whmcsAdminURL']}orders.php?action=view&id={$vars['orderid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in PendingOrder hook: " . $e->getMessage());
        }
    });
endif;

if($networkIssueAdd === true):
    add_hook('NetworkIssueAdd', 1, function($vars) {
        try {
            logActivity("Hook triggered: NetworkIssueAdd");
            $message = "🆕 *New Network Issue Created*\n\n";
            $message .= "Title: " . simpleFix($vars['title']) . "\n";
            $message .= "Start Date: {$vars['startdate']}\n";
            $message .= "End Date: {$vars['enddate']}\n";
            $message .= "Priority: {$vars['priority']}\n";
            $message .= "Description: " . simpleFix($vars['description']) . "\n";
            $message .= "[Manage Issue]({$GLOBALS['whmcsAdminURL']}networkissues.php?action=manage&id={$vars['id']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in NetworkIssueAdd hook: " . $e->getMessage());
        }
    });
endif;

if($networkIssueEdit === true):
    add_hook('NetworkIssueEdit', 1, function($vars) {
        try {
            logActivity("Hook triggered: NetworkIssueEdit");
            $message = "✏️ *Network Issue Edited*\n\n";
            $message .= "Title: " . simpleFix($vars['title']) . "\n";
            $message .= "Start Date: {$vars['startdate']}\n";
            $message .= "End Date: {$vars['enddate']}\n";
            $message .= "Priority: {$vars['priority']}\n";
            $message .= "Description: " . simpleFix($vars['description']) . "\n";
            $message .= "[Manage Issue]({$GLOBALS['whmcsAdminURL']}networkissues.php?action=manage&id={$vars['id']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in NetworkIssueEdit hook: " . $e->getMessage());
        }
    });
endif;

if($networkIssueClosed === true):
    add_hook('NetworkIssueClose', 1, function($vars) {
        try {
            logActivity("Hook triggered: NetworkIssueClose");
            $message = "🏁 *Network Issue Closed*\n\n";
            $message .= "[Manage Issue]({$GLOBALS['whmcsAdminURL']}networkissues.php?action=manage&id={$vars['id']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in NetworkIssueClose hook: " . $e->getMessage());
        }
    });
endif;

if($ticketOpened === true):
    add_hook('TicketOpen', 1, function($vars) {
        try {
            logActivity("Hook triggered: TicketOpen");
            $message = "🎫 *New Support Ticket*\n\n";
            $message .= "Ticket ID: #{$vars['ticketmask']}\n";
            $message .= "Subject: " . simpleFix($vars['subject']) . "\n";
            $message .= "Priority: {$vars['priority']}\n";
            $message .= "Department: {$vars['deptname']}\n";
            $message .= "Message: " . simpleFix($vars['message']) . "\n";
            $message .= "[View Ticket]({$GLOBALS['whmcsAdminURL']}supporttickets.php?action=view&id={$vars['ticketid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in TicketOpen hook: " . $e->getMessage());
        }
    });
endif;

if($ticketClosed === true):
    add_hook('TicketClose', 1, function($vars) {
        try {
            logActivity("Hook triggered: TicketClose");
            $message = "🎫✅ *Ticket Closed*\n\n";
            $message .= "Ticket ID: #{$vars['ticketmask']}\n";
            $message .= "Subject: " . simpleFix($vars['subject']) . "\n";
            $message .= "Closed By: " . ($vars['admin'] ? "Admin" : "User") . "\n";
            $message .= "[View Ticket]({$GLOBALS['whmcsAdminURL']}supporttickets.php?action=view&id={$vars['ticketid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in TicketClose hook: " . $e->getMessage());
        }
    });
endif;

if($ticketUserReply === true):
    add_hook('TicketUserReply', 1, function($vars) {
        try {
            logActivity("Hook triggered: TicketUserReply");
            $message = "💬 *New Ticket Reply*\n\n";
            $message .= "Subject: " . simpleFix($vars['subject']) . "\n";
            $message .= "Priority: {$vars['priority']}\n";
            $message .= "Department: {$vars['deptname']}\n";
            $message .= "Message: " . simpleFix($vars['message']) . "\n";
            $message .= "[View Ticket]({$GLOBALS['whmcsAdminURL']}supporttickets.php?action=view&id={$vars['ticketid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in TicketUserReply hook: " . $e->getMessage());
        }
    });
endif;

if($ticketFlagged === true):
    add_hook('TicketFlagged', 1, function($vars) {
        try {
            logActivity("Hook triggered: TicketFlagged");
            $message = "🚩 *Ticket Flagged*\n\n";
            $message .= "Flagged to: {$vars['adminname']}\n";
            $message .= "[View Ticket]({$GLOBALS['whmcsAdminURL']}supporttickets.php?action=view&id={$vars['ticketid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in TicketFlagged hook: " . $e->getMessage());
        }
    });
endif;

if($ticketNewNote === true):
    add_hook('TicketAddNote', 1, function($vars) {
        try {
            logActivity("Hook triggered: TicketAddNote");
            $message = "📝 *Ticket Note Added*\n\n";
            $message .= "Note: " . simpleFix($vars['message']) . "\n";
            $message .= "[View Ticket]({$GLOBALS['whmcsAdminURL']}supporttickets.php?action=view&id={$vars['ticketid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in TicketAddNote hook: " . $e->getMessage());
        }
    });
endif;

if($cancellationRequest === true):
    add_hook('CancellationRequest', 1, function($vars) {
        try {
            logActivity("Hook triggered: CancellationRequest");
            $message = "🚫 *New Cancellation Request*\n\n";
            $message .= "Cancellation Type: {$vars['type']}\n";
            $message .= "Reason: " . simpleFix($vars['reason']) . "\n";
            $message .= "[View Cancellation Requests]({$GLOBALS['whmcsAdminURL']}cancelrequests.php)";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in CancellationRequest hook: " . $e->getMessage());
        }
    });
endif;

if($clientAdd === true):
    add_hook('ClientAdd', 1, function($vars) {
        try {
            logActivity("Hook triggered: ClientAdd");
            $message = "👤 *New Client Registered*\n\n";
            $message .= "Client ID: #{$vars['userid']}\n";
            $message .= "Name: {$vars['firstname']} {$vars['lastname']}\n";
            $message .= "Email: " . ($GLOBALS['sensitiveInformation'] ? $vars['email'] : "-- Redacted --") . "\n";
            $message .= "[View Client]({$GLOBALS['whmcsAdminURL']}clientssummary.php?userid={$vars['userid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in ClientAdd hook: " . $e->getMessage());
        }
    });
endif;

if($clientEdit === true):
    add_hook('ClientEdit', 1, function($vars) {
        try {
            logActivity("Hook triggered: ClientEdit");
            $message = "✏️ *Client Profile Edited*\n\n";
            $message .= "Client ID: #{$vars['userid']}\n";
            $message .= "Name: {$vars['firstname']} {$vars['lastname']}\n";
            $message .= "[View Client]({$GLOBALS['whmcsAdminURL']}clientssummary.php?userid={$vars['userid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in ClientEdit hook: " . $e->getMessage());
        }
    });
endif;

if($domainRenewal === true):
    add_hook('DomainRenewal', 1, function($vars) {
        try {
            logActivity("Hook triggered: DomainRenewal");
            $message = "🔄 *Domain Renewed*\n\n";
            $message .= "Domain: {$vars['sld']}.{$vars['tld']}\n";
            $message .= "Registration Period: {$vars['regperiod']} year(s)\n";
            $message .= "[View Domain]({$GLOBALS['whmcsAdminURL']}clientsdomains.php?userid={$vars['userid']}&domainid={$vars['domainid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in DomainRenewal hook: " . $e->getMessage());
        }
    });
endif;

if($domainTransfer === true):
    add_hook('DomainTransferCompleted', 1, function($vars) {
        try {
            logActivity("Hook triggered: DomainTransferCompleted");
            $message = "↪️ *Domain Transfer Completed*\n\n";
            $message .= "Domain: {$vars['sld']}.{$vars['tld']}\n";
            $message .= "[View Domain]({$GLOBALS['whmcsAdminURL']}clientsdomains.php?userid={$vars['userid']}&domainid={$vars['domainid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in DomainTransferCompleted hook: " . $e->getMessage());
        }
    });
endif;

if($domainRegistration === true):
    add_hook('DomainRegistration', 1, function($vars) {
        try {
            logActivity("Hook triggered: DomainRegistration");
            $message = "🌐 *New Domain Registered*\n\n";
            $message .= "Domain: {$vars['sld']}.{$vars['tld']}\n";
            $message .= "Registration Period: {$vars['regperiod']} year(s)\n";
            $message .= "[View Domain]({$GLOBALS['whmcsAdminURL']}clientsdomains.php?userid={$vars['userid']}&domainid={$vars['domainid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in DomainRegistration hook: " . $e->getMessage());
        }
    });
endif;

if($addToClientNotes === true):
    add_hook('AddToClientNotes', 1, function($vars) {
        try {
            logActivity("Hook triggered: AddToClientNotes");
            $message = "📝 *Note Added to Client Profile*\n\n";
            $message .= "Client ID: #{$vars['userid']}\n";
            $message .= "Note: " . simpleFix($vars['note']) . "\n";
            $message .= "[View Client]({$GLOBALS['whmcsAdminURL']}clientsnotes.php?userid={$vars['userid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AddToClientNotes hook: " . $e->getMessage());
        }
    });
endif;

if($moduleCreate === true):
    add_hook('AfterModuleCreate', 1, function($vars) {
        try {
            logActivity("Hook triggered: AfterModuleCreate");
            $message = "🚀 *New Service Provisioned*\n\n";
            $message .= "Service ID: #{$vars['serviceid']}\n";
            $message .= "Product: {$vars['producttype']}\n";
            $message .= "[View Service]({$GLOBALS['whmcsAdminURL']}clientsservices.php?userid={$vars['userid']}&id={$vars['serviceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AfterModuleCreate hook: " . $e->getMessage());
        }
    });
endif;

if($moduleTerminate === true):
    add_hook('AfterModuleTerminate', 1, function($vars) {
        try {
            logActivity("Hook triggered: AfterModuleTerminate");
            $message = "🛑 *Service Terminated*\n\n";
            $message .= "Service ID: #{$vars['serviceid']}\n";
            $message .= "Product: {$vars['producttype']}\n";
            $message .= "[View Service]({$GLOBALS['whmcsAdminURL']}clientsservices.php?userid={$vars['userid']}&id={$vars['serviceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AfterModuleTerminate hook: " . $e->getMessage());
        }
    });
endif;

if($moduleSuspend === true):
    add_hook('AfterModuleSuspend', 1, function($vars) {
        try {
            logActivity("Hook triggered: AfterModuleSuspend");
            $message = "⏸️ *Service Suspended*\n\n";
            $message .= "Service ID: #{$vars['serviceid']}\n";
            $message .= "Product: {$vars['producttype']}\n";
            $message .= "[View Service]({$GLOBALS['whmcsAdminURL']}clientsservices.php?userid={$vars['userid']}&id={$vars['serviceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AfterModuleSuspend hook: " . $e->getMessage());
        }
    });
endif;

if($moduleUnsuspend === true):
    add_hook('AfterModuleUnsuspend', 1, function($vars) {
        try {
            logActivity("Hook triggered: AfterModuleUnsuspend");
            $message = "▶️ *Service Unsuspended*\n\n";
            $message .= "Service ID: #{$vars['serviceid']}\n";
            $message .= "Product: {$vars['producttype']}\n";
            $message .= "[View Service]({$GLOBALS['whmcsAdminURL']}clientsservices.php?userid={$vars['userid']}&id={$vars['serviceid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in AfterModuleUnsuspend hook: " . $e->getMessage());
        }
    });
endif;

if($domainAutoRenewDisabled === true):
    add_hook('DomainAutoRenewDisabled', 1, function($vars) {
        try {
            logActivity("Hook triggered: DomainAutoRenewDisabled");
            $message = "🔕 *Domain Auto-Renew Disabled*\n\n";
            $message .= "Domain: {$vars['sld']}.{$vars['tld']}\n";
            $message .= "[View Domain]({$GLOBALS['whmcsAdminURL']}clientsdomains.php?userid={$vars['userid']}&domainid={$vars['domainid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in DomainAutoRenewDisabled hook: " . $e->getMessage());
        }
    });
endif;

if($domainAutoRenewEnabled === true):
    add_hook('DomainAutoRenewEnabled', 1, function($vars) {
        try {
            logActivity("Hook triggered: DomainAutoRenewEnabled");
            $message = "🔔 *Domain Auto-Renew Enabled*\n\n";
            $message .= "Domain: {$vars['sld']}.{$vars['tld']}\n";
            $message .= "[View Domain]({$GLOBALS['whmcsAdminURL']}clientsdomains.php?userid={$vars['userid']}&domainid={$vars['domainid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in DomainAutoRenewEnabled hook: " . $e->getMessage());
        }
    });
endif;

if($ticketAdminReply === true):
    add_hook('TicketAdminReply', 1, function($vars) {
        try {
            logActivity("Hook triggered: TicketAdminReply");
            $message = "👨‍💼 *Admin Reply to Ticket*\n\n";
            $message .= "Ticket ID: #{$vars['ticketmask']}\n";
            $message .= "Subject: " . simpleFix($vars['subject']) . "\n";
            $message .= "Reply: " . simpleFix($vars['message']) . "\n";
            $message .= "[View Ticket]({$GLOBALS['whmcsAdminURL']}supporttickets.php?action=view&id={$vars['ticketid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in TicketAdminReply hook: " . $e->getMessage());
        }
    });
endif;

if($quoteCreated === true):
    add_hook('QuoteCreated', 1, function($vars) {
        try {
            logActivity("Hook triggered: QuoteCreated");
            $message = "💼 *New Quote Created*\n\n";
            $message .= "Quote ID: #{$vars['quoteid']}\n";
            $message .= "Subject: {$vars['subject']}\n";
            $message .= "Total: {$vars['total']}\n";
            $message .= "[View Quote]({$GLOBALS['whmcsAdminURL']}quotes.php?action=manage&id={$vars['quoteid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in QuoteCreated hook: " . $e->getMessage());
        }
    });
endif;

if($quoteAccepted === true):
    add_hook('QuoteAccepted', 1, function($vars) {
        try {
            logActivity("Hook triggered: QuoteAccepted");
            $message = "✅ *Quote Accepted*\n\n";
            $message .= "Quote ID: #{$vars['quoteid']}\n";
            $message .= "Subject: {$vars['subject']}\n";
            $message .= "Total: {$vars['total']}\n";
            $message .= "[View Quote]({$GLOBALS['whmcsAdminURL']}quotes.php?action=manage&id={$vars['quoteid']})";
            sendTelegramMessage($message);
        } catch (Exception $e) {
            logActivity("Error in QuoteAccepted hook: " . $e->getMessage());
        }
    });
endif;

// Helper Functions
function sendTelegramMessage($message) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['telegramBotToken'] . "/sendMessage";
    $data = [
        'chat_id' => $GLOBALS['telegramChatID'],
        'text' => $message,
        'parse_mode' => 'Markdown',
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        logActivity("Telegram Notification Failed: " . print_r($error, true));
        return false;
    } else {
        $response = json_decode($result, true);
        if (!$response['ok']) {
            logActivity("Telegram API Error: " . print_r($response, true));
            return false;
        }
        logActivity("Telegram Notification Sent Successfully");
        return true;
    }
}

function simpleFix($value) {
    if(strlen($value) > 150) {
        $value = trim(preg_replace('/\s+/', ' ', $value));
        $valueTrim = explode("\n", wordwrap($value, 150));
        $value = $valueTrim[0] . '...';
    }
    $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), 'UTF-8', $value);
    return escapeMarkdown($value);
}

function escapeMarkdown($text) {
    $characters = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    return str_replace($characters, array_map(function($char) { return "\\$char"; }, $characters), $text);
}

?>
