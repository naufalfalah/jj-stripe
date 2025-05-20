<?php

use App\Http\Controllers\Frontend\AdsManagementController;
use App\Http\Controllers\Frontend\AdsReportController;
use App\Http\Controllers\Frontend\AIChatController;
use App\Http\Controllers\Frontend\AITextToSpeechController;
use App\Http\Controllers\Frontend\AIVoiceChatController;
use App\Http\Controllers\Frontend\ClientTourController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\EmailTemplateController;
use App\Http\Controllers\Frontend\FileManagerController;
use App\Http\Controllers\Frontend\GoogleAccountController;
use App\Http\Controllers\Frontend\GoogleAdsReportController;
use App\Http\Controllers\Frontend\LeadController;
use App\Http\Controllers\Frontend\MessageTemplateController;
use App\Http\Controllers\Frontend\PackageController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PayNowPaymentController;
use App\Http\Controllers\Frontend\StripeController;
use App\Http\Controllers\Frontend\StripePaymentController;
use App\Http\Controllers\Frontend\UserMessageTemplateController;
use App\Http\Controllers\Frontend\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')
    ->group(function () {
        Route::controller(LeadController::class)
            ->group(function () {
                Route::get('/response/{id}', 'view_client_file_activity')->name('client.response');
            });
        Route::controller(PageController::class)
            ->group(function () {
                Route::get('/page/{id}/{client_id}', 'client_page_view')->name('client.page_view');
            });
        Route::controller(FileManagerController::class)
            ->group(function () {
                Route::get('/file/{id}/{client_id}', 'client_file_view')->name('client.file_view');
                Route::get('/file-view/{id}', 'file_view')->name('file_view');
            });

        Route::controller(StripeController::class)
            ->prefix('stripe')
            ->as('stripe.')
            ->group(function () {
                Route::post('/customer', 'createCustomer');
                Route::post('/payment-intent', 'createPaymentIntent');
                Route::post('/subscription', 'createSubscription');
                Route::post('/subscription/cancel', 'cancelSubscription');
            });
    });

Route::middleware(['client', 'XSS'])
    ->prefix('user')
    ->as('user.')
    ->group(function () {
        Route::controller(DashboardController::class)
            ->group(function () {
                Route::get('/', 'index')->name('dashboard');
                Route::post('/get_latest_leads_dashboard', 'get_leads')->name('get_latest_leads_dashboard');

                Route::prefix('profile')
                    ->as('profile.')
                    ->group(function () {
                        Route::get('/', 'edit_profile')->name('edit');
                        Route::post('/update', 'update_profile')->name('update');
                        Route::post('/update_password', 'update_password')->name('update_password');
                        Route::post('/set_token', 'save_device_token')->name('save_device_token');
                        Route::put('/user_notification', 'updateUserNotification')->name('update_user_notification');
                        Route::put('/lead_filter', 'updateClientLeadFilter')->name('update_lead_filter');
                    });
            });

        // Google Calender Management
        Route::controller(GoogleAccountController::class)
            ->prefix('google')
            ->as('google.')
            ->group(function () {
                Route::get('/index', 'index')->name('index');
                Route::get('/oauth', 'oauth')->name('oauth');
                Route::get('/integrate', 'getAuthUrl')->name('integrate');
                Route::post('/create-event', 'save_event')->name('save_event');
                Route::put('/update-event', 'update_event')->name('update_event');
                Route::delete('/delete-event', 'delete_event')->name('delete_event');
                Route::get('/get-events', 'get_google_calender_events')->name('get_google_calender_events');
                Route::get('/google_calender_disconnected', 'google_calender_disconnected')->name('google_calender_disconnected');
            });

        Route::controller(MessageTemplateController::class)
            ->prefix('message-template')
            ->as('message-template.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/template/{id}/{send_message?}', 'msg_tmp_details')->name('temp_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/send', 'send')->name('send');
                Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                Route::get('/delete/{id}', 'delete')->name('delete');

                Route::get('/', 'whatsapp_temp')->name('whatsapp_temp');
                Route::post('/wp_message_store', 'wp_message_store')->name('wp_message_store');
            });

        Route::controller(UserMessageTemplateController::class)
            ->prefix('whatsap_message_template')
            ->as('whatsap_message_template.')
            ->group(function () {
                Route::get('/', 'index')->name('all');
                Route::post('/', 'wp_message_update')->name('wp_message_update');
            });

        Route::controller(EmailTemplateController::class)
            ->prefix('email-template')
            ->as('email-template.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/template/{id}/{send_email?}', 'email_tmp_details')->name('temp_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/send', 'send')->name('send');
                Route::post('/copy_temp', 'copy_temp')->name('copy_temp');
                Route::get('/delete/{id}', 'delete')->name('delete');
            });

        Route::controller(FileManagerController::class)
            ->prefix('file_manager')
            ->as('file_manager.')
            ->group(function () {
                Route::get('/files', 'files_view')->name('view');
                Route::get('/file_detail/{id}', 'file_detail')->name('file_detail');
                Route::get('/file_list', 'files_list')->name('file_list');
                Route::get('/delete/{id}', 'delete_file')->name('delete');
                Route::post('/save_folder', 'save_folder')->name('save_folder');
                Route::post('/update_file_name', 'update_file_name')->name('update_file_name');
                Route::post('/save_file', 'save_file')->name('save_file');
                Route::post('get_client_files', 'get_client_files')->name('get_client_files');
                Route::post('/send_file', 'send_file')->name('send_file');
            });

        Route::controller(PageController::class)
            ->prefix('page')
            ->as('page.')
            ->group(function () {
                Route::get('/page', 'page_view')->name('view');
                Route::post('/page_save', 'page_save')->name('save');
                Route::get('/page_details/{id}', 'page_details')->name('page_details');
                Route::get('/add_page/{id}', 'add_page')->name('add_page');
                Route::get('/page_preview/{id}', 'page_preview')->name('page_preview');
                Route::get('/delete_page/{id}', 'delete_page')->name('delete_page');
                Route::post('/send', 'send')->name('send');
                Route::get('/edit_page/{id}', 'edit_page')->name('edit_page');
            });

        Route::controller(ClientTourController::class)
            ->prefix('client_tour')
            ->as('client_tour.')
            ->group(function () {
                Route::get('/restart', 'restart')->name('restart');
            });

        Route::controller(DashboardController::class)
            ->group(function () {
                Route::get('/notifications', 'notifications')->name('notifications');
                Route::get('/update_notifications', 'update_notifications')->name('update_notifications');
            });

        // Leads Management
        Route::controller(LeadController::class)
            ->prefix('leads-management')
            ->as('leads-management.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/client/{id}', 'client_details')->name('client_details');
                Route::post('/save', 'save')->name('save');
                Route::post('/assign_lead', 'assign_lead_to_isa')->name('assign_lead');
                Route::post('/add_lead_to_spam', 'add_lead_to_spam')->name('add_lead_to_spam');
                Route::post('/group_save', 'group_save')->name('group_save');
                Route::post('/group_lead_save', 'group_lead_save')->name('group_lead_save');
                Route::get('/delete_group/{id}', 'delete_group')->name('delete_group');
                Route::post('/import_file', 'import_file')->name('import_file');
                Route::get('/delete/{id}/{is_reload?}', 'delete')->name('delete');
                Route::post('/activity_save', 'activity_save')->name('activity_save');
                Route::get('/activity_delete/{id}', 'activity_delete')->name('activity_delete');
                Route::post('/set_follow_up', 'set_follow_up')->name('set_follow_up');
                Route::get('/unset_follow_up/{id}', 'unset_follow_up')->name('unset_follow_up');
                Route::get('/get_follow_ups', 'get_follow_ups')->name('get_follow_ups');
                Route::post('/update_status', 'update_status')->name('update_status');

                Route::get('/leads', 'ppc_leads')->name('leads');
                Route::post('/lead_status', 'lead_status')->name('lead_status');
                Route::post('/lead_status', 'get_leads')->name('get_leads_all');
            });

        // Client Wallet
        Route::controller(WalletController::class)
            ->prefix('wallet')
            ->as('wallet.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/add', 'add_top_up')->name('add');
                Route::post('/save', 'save')->name('save');
                Route::get('/transaction_table', 'transaction_table')->name('transaction_table');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('sub_wallets', 'sub_wallets')->name('sub_wallets');
                Route::post('sub_wallets_transactions', 'sub_wallets_transactions')->name('sub_wallets_transactions');
                Route::post('add_topup_subwallet', 'add_topup_subwallet')->name('add_topup_subwallet');
                Route::get('transfer-funds', 'transfer_funds')->name('transfer_funds');
                Route::Post('funds_save', 'funds_save')->name('funds_save');
                Route::get('/view_fund_transections', 'view_fund_transections')->name('view_fund_transections');
                Route::get('/transaction_report', 'transaction_report')->name('transaction_report');
                Route::get('/add_paynow_transaction_id', 'add_paynow_transaction_id')->name('add_paynow_transaction_id');
                Route::post('/wallet_close', 'walletClose')->name('wallet_close');
            });

        // Stripe Payment
        Route::controller(StripePaymentController::class)
            ->prefix('stripe')
            ->as('stripe.')
            ->group(function () {
                Route::get('/checkout/{price}/{product}/{ad_id}', 'stripeCheckout')->name('checkout');
                Route::get('/checkout/success', 'stripeCheckoutSuccess')->name('checkout.success');
            });

        // PayNow Payment
        Route::controller(PayNowPaymentController::class)
            ->prefix('paynow')
            ->as('paynow.')
            ->group(function () {
                Route::get('/checkout', 'payNowCheckout')->name('checkout');
                Route::get('/checkout/success', 'paynowCheckoutSuccess')->name('checkout.success');
            });

        // Client Ads
        Route::controller(AdsManagementController::class)
            ->prefix('ads')
            ->as('ads.')
            ->group(function () {
                Route::get('/all', 'index')->name('all');
                Route::get('/add', 'add_ads')->name('add');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::get('/edit/{id}', 'edit_ads')->name('edit');
                Route::put('/update/{id}', 'update')->name('update');
                Route::post('/save', 'save')->name('save');
                Route::get('/view/progress/{id}', 'view_progress')->name('view_progress');
                Route::post('/view/progress/get_leads_data', 'get_leads_data')->name('get_leads_data');
                Route::post('/view/progress/lead_admin_status', 'lead_admin_status')->name('lead_admin_status');
                Route::get('/check_domain', 'check_domain')->name('check_domain');
            });

        // Client Ads Report
        Route::controller(AdsReportController::class)
            ->prefix('report')
            ->as('report.')
            ->group(function () {
                Route::get('/', 'index')->name('view');
                Route::get('/slip/{id}', 'slip')->name('slip');
            });

        // Google Ads Report
        Route::controller(GoogleAdsReportController::class)
            ->prefix('google-ads-report')
            ->as('google-ads-report.')
            ->group(function () {
                Route::get('/', 'google_ads_report')->name('google_ads_report');
                Route::get('/download_pdf', 'download_pdf')->name('download_pdf');
                Route::post('/save_google_report', 'save_google_report')->name('save_google_report');
                Route::post('/campaign_note_save', 'campaign_note_save')->name('campaign_note_save');
                Route::get('/campaign_note_delete/{id}', 'campaign_note_delete')->name('campaign_note_delete');
                Route::post('update_act_expiry_date', 'update_act_expiry_date')->name('update_act_expiry_date');
            });

        Route::controller(PackageController::class)
            ->prefix('package')
            ->as('package.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'buy')->name('buy');
            });

        Route::controller(AITextToSpeechController::class)
            ->prefix('ai-tts')
            ->as('ai-tts.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'generateSpeech')->name('generateSpeech');
            });

        Route::controller(AIChatController::class)
            ->prefix('ai-chat')
            ->as('ai-chat.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'sendMessage')->name('sendMessage');
            });

        Route::controller(AIVoiceChatController::class)
            ->prefix('ai-voice-chat')
            ->as('ai-voice-chat.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'sendMessage')->name('sendMessage');
            });
    });
