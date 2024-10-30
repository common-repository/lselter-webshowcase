<?php
/**
 * Created by PhpStorm.
 * User: Lönja
 * Date: 15/10/2014
 * Time: 04:22
 */



function cron_add_weekly($schedules)
{
    // Adds once weekly to the existing schedules.
    $schedules['weekly'] = array(
        'interval' => 604800,
        'display' => __('Once Weekly')
    );
    return $schedules;
}

//register scheduled caching of thumbnails on activation
register_activation_hook($plugin_base_file, 'lselter_webshowcase_activation');
/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 */
function lselter_webshowcase_activation()
{
    wp_schedule_event(time(), 'weekly', 'lselter_webshowcase_weekly_event_hook');
}

add_action('lselter_webshowcase_weekly_event_hook', 'lselter_webshowcase_cache_thumbnails');
/**
 * On the scheduled action hook, run the function.
 */

register_deactivation_hook($plugin_base_file, 'lselter_webshowcase_deactivation');
/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function lselter_webshowcase_deactivation()
{
    wp_clear_scheduled_hook('lselter_webshowcase_weekly_event_hook');
}

/**
 * @param $new_schedule : string (schedule ID)
 * updates the screenshotting schedule to reflect option setting
 *
 */
function update_schedule($new_schedule){
    foreach(wp_get_schedules() as $x=> $y) {
        wp_clear_scheduled_hook('lselter_webshowcase_'.$x.'_event_hook');
    }
    wp_schedule_event(time(), (string) $new_schedule, 'lselter_webshowcase_'.$new_schedule.'_event_hook');

    add_action('lselter_webshowcase_'.$new_schedule.'_event_hook', 'lselter_webshowcase_cache_thumbnails');
}