<?php

namespace App\Enums;

class PermissionsEnum
{
    const ALL_ACCESS_TO_DEVICE = 'all_access_to_device';
    const ALL_ACCESS_TO_REPORTS = 'all_access_to_reports';
    const ALL_ACCESS_TO_USERS = 'all_access_to_users';
    const CAN_VIEW_DEVICES = 'can_view_devices';
    const CAN_VIEW_DEVICE_DETAILS = 'can_view_device_details';
    const CAN_CONTROL_DEVICE_TIME = 'can_control_device_time';
    const CAN_TRIGGER_FREE_LIGHT = 'can_trigger_free_light';
    const CAN_DELETE_DEVICE = 'can_delete_device';
    const CAN_DISABLE_DEVICE = 'can_disable_device';
    const CAN_EDIT_DEVICE_BASE_TIME = 'can_edit_device_base_time';
    const CAN_ADD_DEVICE_INCREMENTS = 'can_add_device_increments';
    const CAN_DISABLE_DEVICE_INCREMENTS = 'can_disable_device_increments';
    const CAN_DELETE_DEVICE_INCREMENTS = 'can_delete_device_increments';
    const CAN_EDIT_DEVICE_INCREMENTS = 'can_edit_device_increments';
    const CAN_VIEW_DEVICE_SPECIFIC_RATE_USAGE_REPORT = 'can_view_device_specific_rate_usage_report';
    const CAN_VIEW_DEVICE_SPECIFIC_TIME_TRANSACTION_REPORT = 'can_view_device_specific_time_transaction_report';
    const CAN_DEPLOY_DEVICE = 'can_deploy_device';
    const CAN_EDIT_DEVICE_NAME = 'can_edit_device_name';
    const CAN_EDIT_WATCHDOG_INTERVAL = 'can_edit_watchdog_interval';
    const CAN_EDIT_REMAINING_TIME_INTERVAL = 'can_edit_remaining_time_interval';
    const CAN_VIEW_FINANCIAL_REPORTS = 'can_view_financial_reports';
    const CAN_VIEW_ACTIVITY_LOGS_REPORTS = 'can_view_activity_logs_reports';
};
